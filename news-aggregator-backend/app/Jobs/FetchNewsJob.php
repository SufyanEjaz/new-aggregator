<?php

namespace App\Jobs;

use App\Services\GuardianApiService;
use App\Services\NewsApiService;
use App\Services\NyTimesApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchNewsJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $serviceName;

    /**
     * Create a new job instance.
     */
    public function __construct($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * Execute the job.
     */
    public function handle(GuardianApiService $guardianApiService, NyTimesApiService $nyTimesApiService, NewsApiService $newsApiService): void
    {
        // Fetch from the appropriate service based on the provided service name
        switch ($this->serviceName) {
            case 'guardian':
                $this->fetchFromService($guardianApiService, 'The Guardian');
                break;
            case 'nytimes':
                $this->fetchFromService($nyTimesApiService, 'New York Times');
                break;
            case 'newsapi':
                $this->fetchFromService($newsApiService, 'NewsAPI');
                break;
            default:
                Log::warning("Unknown news service: {$this->serviceName}");
                break;
        }
    }

    /**
     * Fetch news articles from a given service.
     *
     * @param $service The service instance (GuardianApiService, NyTimesApiService, or NewsApiService).
     * @param string $serviceName The name of the service for logging purposes.
     */
    private function fetchFromService($service, $serviceName)
    {
        // Retry fetching the news 3 times with exponential backoff
        retry(3, function () use ($service, $serviceName) {
            $service->fetch();
            Log::info("Successfully fetched news from {$serviceName}");
        }, [1000, 2000, 5000]);
    }
}
