<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http; // Import the Http facade

class DailyApiCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:apicall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes a daily API call to update user interests';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Set the correct API URL
        $url = 'http://admin.tducoin.com/api/usermanage/interest-auto';

        // Define the API key
        $apiKey = 'oqKbBxKcEn9l4IXE4EqS2sgNzXPFvE';

        // Make the POST request with the API key in the header
        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
            ])->post($url);

            // Check the response and provide feedback
            if ($response->successful()) {
                $this->info('API call successful: ' . $response->body());
            } else {
                $this->error('API call failed with status: ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('API call failed with error: ' . $e->getMessage());
        }

        return 0;
    }
}
