<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Helpers\OsuHelpers;
use App\Models\Beatmap;
use App\Models\Beatmapset;
use App\Models\Player;
use App\Models\Score;


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

            $count = 0;
            $scores = $response->json();

            if (empty($scores))
            {
                $msg = $username . ' has no recent plays.';
                $this->info($msg);
                Log::info($msg);

                continue;
            }

            foreach($scores as $score){
                $recordHash = OsuHelpers::computeRecordHash($score);
                if (Score::where('record_hash', $recordHash)->exists()) continue;


                $beatmapId = $score['beatmap']['id'];

                $existingBeatmap = Beatmap::find($beatmapId);

                $beatmapData = $score['beatmap'];
                $beatmapsetId = $score['beatmapset']['id'];

                $beatmapDataQuery = [
                        'id' => $beatmapId,
                        'beatmapset_id' => $beatmapsetId,
                        'difficulty_rating' => $beatmapData['difficulty_rating'],
                        'user_id' => $beatmapData['user_id'],
                        'total_length' => $beatmapData['total_length'],
                        'version' => $beatmapData['version'],
                        'checksum' => $beatmapData['checksum'],
                        'bpm' => $beatmapData['bpm'],
                        'cs' => $beatmapData['cs'],
                        'ar' => $beatmapData['ar'],
                        'drain' => $beatmapData['drain'],
                        'accuracy' => $beatmapData['accuracy'],
                        'hit_length' => $beatmapData['hit_length'],
                        'count_circles' => $beatmapData['count_circles'],
                        'count_sliders' => $beatmapData['count_sliders'],
                        'count_spinners' => $beatmapData['count_spinners'],
                        'last_updated' => Carbon::parse($beatmapData['last_updated'])->format('Y-m-d H:i:s'),
                    ];

                if (!$existingBeatmap)
                {

                    if (Beatmapset::where('id', $beatmapsetId)->exists()){
                        continue;
                    }

                    $beatmapsetData = $score['beatmapset'];
                    Beatmapset::create([
                        'id' => $beatmapsetId,
                        'artist' => $beatmapsetData['artist'],
                        'artist_unicode' => $beatmapsetData['artist_unicode'],
                        'creator' => $beatmapsetData['creator'],
                        'title' => $beatmapsetData['title'],
                        'title_unicode' => $beatmapsetData['title_unicode'],
                        'user_id' => $beatmapsetData['user_id'],
                    ]);
                    Log::info('Added new song ' . $beatmapsetData['title'] . ' with ID: ' . $beatmapsetId);
                    Beatmap::create($beatmapDataQuery);
                }
                else
                {
                    $beatmapLastUpdated = $existingBeatmap->last_updated;
                    if ($beatmapLastUpdated->gt(Carbon::parse($beatmapData['last_updated'])))
                    {
                        Beatmap->update($beatmapDataQuery);
                    }
                }

                Score::create([
                    'user_id' => $score['user_id'],
                    'beatmap_id' => $beatmapId,
                    'score_id' => $score['id'],
                    'record_hash' => $recordHash,
                    'accuracy' => $score['accuracy'],
                    'max_combo' => $score['max_combo'],
                    'mods' => $score['mods'],
                    'passed' => $score['passed'] ? 1 : 0,
                    'perfect' => $score['perfect'] ? 1 : 0,
                    'pp' => $score['pp'] ?? 0,
                    'rank' => $score['rank'],
                    'score' => $score['score'],
                    'count_100' => $score['statistics']['count_100'] ?? 0,
                    'count_300' => $score['statistics']['count_300'] ?? 0,
                    'count_50' => $score['statistics']['count_50'] ?? 0,
                    'count_geki' => $score['statistics']['count_geki'] ?? 0,
                    'count_katu' => $score['statistics']['count_katu'] ?? 0,
                    'count_miss' => $score['statistics']['count_miss'] ?? 0,
                    'submission_date' => Carbon::parse($score['created_at'])->format('Y-m-d H:i:s'),
                ]);

                $count++;
            }

            $success = 'Fetched ' . count($scores) . ' scores for ' . $username ;
            $this->info($success);
            Log::info($success);
            /* $this->info(json_encode($scores, JSON_PRETTY_PRINT)); */
        }
    }
}
