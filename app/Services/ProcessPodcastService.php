<?php
namespace App\Services;

use Carbon\Carbon;
use SimpleXMLElement;
use GuzzleHttp\Client;
use App\Models\Podcast;
use App\Models\PodcastEpisode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPodcastService
{

    private $client;
    private $rssFeedUrl;
    private $paramData = [
        'headers' => [
           'Accept' => 'application/xml',
        ]
     ];

    private $guzzleTimeoutValue = 0;
    
    /**
     * Set required properties.
     * @param string $rssFeedUrl
     * @return void
     */
    public function __construct(string $rssFeedUrl) {
        $this->rssFeedUrl = $rssFeedUrl;
        $this->setClient();
    }


    /**
     * This function sets the guzzle http client
     * @return void
     */
    public function setClient():void {
        $this->client = new Client(['timeout'  => $this->guzzleTimeoutValue]);
    }



    /**
     * This function connects to the rss feed url to obtain xml data
     * @return void
     */
    public function processRssFeedData():void {

        set_time_limit(0);

        ini_set("memory_limit", "256M");
    
            try{
                $rssResponse = ($this->client->get($this->rssFeedUrl, $this->paramData))->getBody()->getContents();
               
                $xmlData = simplexml_load_string($rssResponse);

                $podcastProcessStatus = $this->processPodcastData($xmlData, $this->rssFeedUrl);

                if(!$podcastProcessStatus){
                  
                    Log::error("Error processing RSS feeds for podcast: {$this->rssFeedUrl}");
                }

            }catch(\Exception $e){
                
                Log::error("Error processing RSS feeds for podcast: {$this->rssFeedUrl} with error message {$e->getMessage()} on line {$e->getLine()} in file {$e->getFile()}");
            }

        

    }



    /**
     * This function extracts web url of a podcast from an rss feed url
     * @param string $xmlUrl
     * @return string
     */
    public function getWebUrlFromXmlUrl(string $xmlUrl):string {
        $rssFeedUrlData = explode("/", $xmlUrl)[2];
        
        if(str_contains($rssFeedUrlData, 'feeds')) {
            $explodedFeedUrlData = explode(".", $rssFeedUrlData);
            if($explodedFeedUrlData[0] == 'feeds') {
                $rssFeedUrlData = "https://".implode(".", (collect($explodedFeedUrlData)->except([0]))->all());
            }
        }

        return $rssFeedUrlData;
    }



    /**
     * This function handles the processing of Podcasts using xml data retrieved from rss feed
     * @param SimpleXMLElement $xmlData
     * @param string $feedUrl
     * @return bool
     */
    public function processPodcastData(SimpleXMLElement $xmlData, string $feedUrl):bool {

        try{
            $podcastData = $xmlData->channel;

            DB::beginTransaction();

            $podcastObject = Podcast::updateOrCreate(
                                                    ['rss_feed_url' => $feedUrl],
                                                    [
                                                        'title' => (string)$podcastData->title ?? null,
                                                        'art_work_url' => (string)$podcastData->image->url ?? null,
                                                        'description' => (string)$podcastData->description ?? null,
                                                        'language' => (string)$podcastData->language ?? null,
                                                        'website_url' => (string)$podcastData->link ?? $this->getWebUrlFromXmlUrl($feedUrl)
                                                    ]
                                                    );

                //process episodes
                foreach($podcastData->item as $podcastItemData) {
                    PodcastEpisode::updateOrCreate(
                                                  ['podcast_guid' => (string)$podcastItemData->guid],
                                                  [
                                                    'title' => (string)$podcastItemData->title ?? null,
                                                    'description' => (string)$podcastItemData->description ?? null,
                                                    'audio_url' => (string)$podcastItemData->link ?? null,
                                                    'date_published' => Carbon::parse((string)$podcastItemData->pubDate),
                                                    'podcast_id' => $podcastObject->id
                                                  ]);
            }

            DB::commit();
            
            return true;

        }catch(\Exception $e) {

            Log::error("Error processing RSS feeds for podcast: {$feedUrl} with error message {$e->getMessage()} on line {$e->getLine()} in file {$e->getFile()}");

            DB::rollBack();
            
            return false;

        }

    }




}