<?php

namespace App\Console\Commands;

use App\Jobs\FetchNewsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Throwable;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news articles from external sources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Starting to fetch news articles...');

            $services = ['guardian', 'nytimes', 'newsapi'];
            foreach ($services as $service) {
                Queue::push(new FetchNewsJob($service));
            }

            $this->info('News fetching process completed.');
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            $this->error($e->getMessage());
        }
    }
}
