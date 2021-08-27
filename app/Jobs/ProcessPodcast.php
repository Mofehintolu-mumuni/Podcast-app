<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Services\ProcessPodcastService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPodcast implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    private $rssFeedUrl;

    /**
     * Create a new job instance.
     * @param string $rssFeedUrl
     * @return void
     */
    public function __construct(string $rssFeedUrl)
    {
        $this->rssFeedUrl = $rssFeedUrl;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new ProcessPodcastService($this->rssFeedUrl))->processRssFeedData();
    }
}
