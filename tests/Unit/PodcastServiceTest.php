<?php

namespace Tests\Unit;


use PHPUnit\Framework\TestCase;
use App\Services\ProcessPodcastService;

class PodcastServiceTest extends TestCase
{
    /**
     * A test to get web url from xml url.
     *
     * @return void
     */
    public function test_get_web_url_from_xml_url()
    {
        //use an rss feed url that has feed in it

        $feedUrl = "https://feeds.simplecast.com/54nAGcIl";

        $podcastServiceInstance = new ProcessPodcastService($feedUrl);
        $rssFeedUrl = $podcastServiceInstance->getWebUrlFromXmlUrl($feedUrl);
        $this->assertStringNotContainsString("feeds", $rssFeedUrl);
       
    }
}
