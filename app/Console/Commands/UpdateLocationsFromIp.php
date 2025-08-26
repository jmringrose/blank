<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailSequence;
use Illuminate\Support\Facades\Http;

class UpdateLocationsFromIp extends Command
{
    protected $signature = 'locations:update-from-ip {--limit=10 : Number of records to process} {--debug : Show debug information}';
    protected $description = 'Update location field from IP addresses using free IP geolocation service';

    public function handle()
    {
        $limit = $this->option('limit');
        $debug = $this->option('debug');
        
        if ($debug) {
            $totalRecords = EmailSequence::count();
            $withIp = EmailSequence::whereNotNull('ip_address')->count();
            $withLocation = EmailSequence::whereNotNull('location')->where('location', '!=', '')->where('location', '!=', 'Unknown')->count();
            
            $this->info("Debug info:");
            $this->info("Total records: {$totalRecords}");
            $this->info("Records with IP: {$withIp}");
            $this->info("Records with location: {$withLocation}");
        }
        
        // Get records with IP but no location (or location = 'Unknown')
        $sequences = EmailSequence::where('ip_address', '!=', null)
            ->where(function($query) {
                $query->whereNull('location')
                      ->orWhere('location', 'Unknown')
                      ->orWhere('location', '');
            })
            ->limit($limit)
            ->get();

        if ($sequences->isEmpty()) {
            $this->info('No records need location updates.');
            if ($debug) {
                $this->info('Try adding new signups first or check if IP addresses are being captured.');
            }
            return;
        }

        $this->info("Processing {$sequences->count()} records...");
        $bar = $this->output->createProgressBar($sequences->count());

        foreach ($sequences as $sequence) {
            try {
                // Use free ip-api.com service (no API key needed, 1000 requests/month)
                $response = Http::timeout(5)->get("http://ip-api.com/json/{$sequence->ip_address}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($data['status'] === 'success') {
                        $location = trim($data['city'] . ', ' . $data['regionName'] . ', ' . $data['country']);
                        $sequence->update(['location' => $location]);
                        $this->newLine();
                        $this->info("Updated {$sequence->first} {$sequence->last}: {$location}");
                    } else {
                        $sequence->update(['location' => 'Location unavailable']);
                    }
                } else {
                    $this->error("Failed to lookup IP: {$sequence->ip_address}");
                }
                
                // Rate limit: 45 requests per minute for free tier
                sleep(2);
                
            } catch (\Exception $e) {
                $this->error("Error processing {$sequence->ip_address}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Location updates completed!');
    }
}