<?php

namespace App\Console\Commands;

use Throwable;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ProcessPodcast as ProcessPodcastJob;


class ProcessPodcast extends Command
{
    /**
     * The array containing all rss feed urls
     *
     * @var array
     */
    private $rssFeedUrlsData = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
   
    protected $signature = 'process:podcast {rssFeedUrl*?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Podcast using RSS feed url';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        set_time_limit(0);

        $dialogueStatus = true;
        while($dialogueStatus) {
            $this->rssFeedUrlsData[] = $this->ask('Please enter a valid rss feed url');
            if ($this->confirm('Have you finished inputting all your rss feed urls?')) {
                $dialogueStatus = false;
            }
        }
        
        $this->info("Started processing RSS feeds *******");

        $this->newLine();

        $jobsArray = [];
        collect($this->rssFeedUrlsData)->map(function($feedUrl, $key) use (&$jobsArray){
                //ascertain that validator did not fail then add job
                if(!($this->validateRssFeedUrl($feedUrl))->fails()) {              
                    $jobsArray[] = new ProcessPodcastJob($feedUrl);
                }
        });

        //check if there are jobs to be dispatched
        if(sizeof($jobsArray) > 0) {
            //dispatch jobs in batch and monitor progress

            $batch = Bus::batch($jobsArray)->allowFailures()->dispatch();

            $batchProcessingStatus = true;

            $totalJobsInBatch = $batch->totalJobs;

            $batchCompletionPercentageRecorder = [];

            $progressBar = $this->output->createProgressBar($totalJobsInBatch);

            $progressBar->start();

            while($batchProcessingStatus) {
                //find current batch details
                $batch = Bus::findBatch($batch->id);

                //get current batch completion percentage
                $completionPercentage = $batch->progress();
                
                if($completionPercentage > 0) {
                    if(!in_array($completionPercentage, $batchCompletionPercentageRecorder)) {
                        $batchCompletionPercentageRecorder[] = $completionPercentage;

                        $previousAdvanceValue = 0;

                        (sizeof($batchCompletionPercentageRecorder) > 1) ?  $previousAdvanceValue = $this->computeAdvanceValue($totalJobsInBatch, $batchCompletionPercentageRecorder[array_search($completionPercentage, $batchCompletionPercentageRecorder) - 1]) : null;
                        
                        $progressBarAdvanceValue = $this->computeAdvanceValue($totalJobsInBatch, $completionPercentage) - $previousAdvanceValue;

                        $progressStartValue = 0;

                        while($progressStartValue < $progressBarAdvanceValue) {
                            $progressBar->advance();
                            $progressStartValue++;
                        }

                    }
                }

                $batch->finished() ? $batchProcessingStatus = false : null;
            }

            $progressBar->finish();

            $failedJobs = $batch->failedJobs;

            ($failedJobs > 0) ? $commandCompletionMessage = "Completed action , {$failedJobs} rss feeds failed to process" : $commandCompletionMessage = "Completed processing all RSS feeds";

        }else{
            $commandCompletionMessage = "Completed action with no RSS feed processed";
        }

        $this->newLine();

        $this->info($commandCompletionMessage);
        
        return 1;
    }


    /**
     * This function computes command line progress bar advance value based on total jobs in batch and batch completion percentage value
     * @param int $totalJobsInBatch
     * @param float $completionPercentage
     * @return float
     */
    public function computeAdvanceValue(int $totalJobsInBatch, float $completionPercentage):float {
        $advanceValue = ($completionPercentage / 100) * $totalJobsInBatch;
        return $advanceValue;
    }

    /**
     * This function validates an rss feed url
     * @param string $feedUrl
     * @return object
     */
    public function validateRssFeedUrl(string $feedUrl):object {
        return Validator::make(['feed_url' => $feedUrl],
                               [
                                'feed_url' => 'required|string|url',
                               ]);
    }


}
