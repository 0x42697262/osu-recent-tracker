<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Models\Player;


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
        $players = Player::all();

        $this->info('Fetching latest osu! scores for ' . count($players) . ' players...');
        Log::info('Started osu:fetch-osu-scores command for '. count($players) . ' players');

        /**
        * Maximum number of score results (max: 100).
        *
        * @var integer
        */
        $limit = 100;

        // Check for authentication so that we don't waste peppy's cpu
        $auth_resp = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->withToken(env('OSU_API_V2_ACCESS_TOKEN'))->get("https://osu.ppy.sh/api/v2/");

        $json = $auth_resp->json();

        if (isset($json['authentication']) && $json['authentication'] === 'basic')
        {
            $error = "Authentication failed: osu! API token is invalid or expired.";
            $this->error($error);
            Log::error($error);

            return Command::FAILURE;
        }


        foreach ($players as $player)
        {
            $user_id = $player->id;
            $username = $player->username;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate, br', // no need to worry about 300kib json response, compresesd to ~16kib
            ])->withToken(env('OSU_API_V2_ACCESS_TOKEN'))
            ->get("https://osu.ppy.sh/api/v2/users/{$user_id}/scores/recent", [
                'legacy_only' => 0,
                'include_fails' => 1,
                'mode' => 'osu',
                'limit' => $limit,
            ]);

            if (!$response->successful()) {
                $error = 'Failed to fetch player '. $username . ' recent scores: ' . $response->body();
                $this->error($error);
                Log::error($error);

                return Command::FAILURE;
            }

            $scores = $response->json();
            $success = 'Fetched ' . count($scores) . ' scores for ' . $username ;
            $this->info($success);
            Log::info($success);
            /* $this->info(json_encode($scores, JSON_PRETTY_PRINT)); */
        }
    }
}
