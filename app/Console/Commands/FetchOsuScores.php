<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchOsuScores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'osu:fetch-osu-scores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches recent osu! scores and stores them in the database';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Fetching latest osu! scores...');
        Log::info('Started osu:fetch-osu-scores command.');

        /**
        * Maximum number of score results (max: 20).
        *
        * @var integer
        */
        $limit = 20;
        /**
        * Result offset for pagination.
        *
        * @var integer
        */
        $offset = 0;

        $user_id = 23131365;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->withToken(env('OSU_API_V2_ACCESS_TOKEN'))
          ->get("https://osu.ppy.sh/api/v2/users/{$user_id}/scores/recent", [
              'legacy_only' => 0,
              'include_fails' => 1,
              'mode' => 'osu',
              'limit' => $limit,
              'offset' => $offset,
          ]);


        if (!$response->successful()) {
            $error = 'Failed to fetch player recent scores: ' . $response->body();
            $this->error($error);
            Log::error($error);

            return Command::FAILURE;
        }

        $scores = $response->json();
        $success = 'Fetched ' . count($scores) . ' scores successfully.';
        $this->info($success);
        Log::info($success);
        $this->info(json_encode($scores, JSON_PRETTY_PRINT));
    }
}
