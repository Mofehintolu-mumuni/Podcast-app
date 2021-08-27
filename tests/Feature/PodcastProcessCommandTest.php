<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PodcastProcessCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Process podcast command  test.
     *
     * @return void
     */
    public function test_podcast_command()
    {

        $feedUrls = [
                    "https://www.omnycontent.com/d/playlist/2b465d4a-14ee-4fbe-a3c2-ac46009a2d5a/b1907157-de93-4ea2-a952-ac700085150f/be1924e3-559d-4f7d-98e5-ac7000851521/podcast.rss",
                    "https://nosleeppodcast.libsyn.com/rss",
                    "https://www.omnycontent.com/d/playlist/aaea4e69-af51-495e-afc9-a9760146922b/43816ad6-9ef9-4bd5-9694-aadc001411b2/808b901f-5d31-4eb8-91a6-aadc001411c0/podcast.rss",
                    "https://feeds.megaphone.fm/stuffyoushouldknow",
                    "https://feeds.megaphone.fm/stuffyoumissedinhistoryclass",
                    "https://www.omnycontent.com/d/playlist/aaea4e69-af51-495e-afc9-a9760146922b/d2c4e775-99ce-4c17-b04c-ac380133d68c/2c6993d0-eac8-4252-8c4e-ac380133d69a/podcast.rss",
                    "https://feeds.megaphone.fm/VMP5705694065",
                    "https://feeds.simplecast.com/54nAGcIl"
                    ];

               $this->artisan('process:podcast')
                    ->expectsQuestion('Please enter a valid rss feed url', "https://nosleeppodcast.libsyn.com/rss")
                    ->expectsConfirmation('Have you finished inputting all your rss feed urls?', 'no')
                    ->expectsQuestion('Please enter a valid rss feed url', "https://www.omnycontent.com/d/playlist/2b465d4a-14ee-4fbe-a3c2-ac46009a2d5a/b1907157-de93-4ea2-a952-ac700085150f/be1924e3-559d-4f7d-98e5-ac7000851521/podcast.rss")
                    ->expectsConfirmation('Have you finished inputting all your rss feed urls?', 'no')
                    ->expectsQuestion('Please enter a valid rss feed url', "https://www.omnycontent.com/d/playlist/aaea4e69-af51-495e-afc9-a9760146922b/43816ad6-9ef9-4bd5-9694-aadc001411b2/808b901f-5d31-4eb8-91a6-aadc001411c0/podcast.rss")
                    ->expectsConfirmation('Have you finished inputting all your rss feed urls?', 'no')
                    ->expectsQuestion('Please enter a valid rss feed url', "https://feeds.megaphone.fm/stuffyoushouldknow")
                    ->expectsConfirmation('Have you finished inputting all your rss feed urls?', 'no')
                    ->expectsQuestion('Please enter a valid rss feed url', "https://feeds.megaphone.fm/stuffyoumissedinhistoryclass")
                    ->expectsConfirmation('Have you finished inputting all your rss feed urls?', 'no')
                    ->expectsQuestion('Please enter a valid rss feed url', "https://www.omnycontent.com/d/playlist/aaea4e69-af51-495e-afc9-a9760146922b/d2c4e775-99ce-4c17-b04c-ac380133d68c/2c6993d0-eac8-4252-8c4e-ac380133d69a/podcast.rss")
                    ->expectsConfirmation('Have you finished inputting all your rss feed urls?', 'no')
                    ->expectsQuestion('Please enter a valid rss feed url', "https://feeds.megaphone.fm/VMP5705694065")
                    ->expectsConfirmation('Have you finished inputting all your rss feed urls?', 'no')
                    ->expectsQuestion('Please enter a valid rss feed url', "https://feeds.simplecast.com/54nAGcIl")
                    ->expectsConfirmation('Have you finished inputting all your rss feed urls?', 'yes')
                    ->expectsOutput('Started processing RSS feeds *******')
                    ->expectsOutput('Completed processing all RSS feeds')
                    ->doesntExpectOutput('No data found')
                    ->assertExitCode(1);


        foreach($feedUrls as $feedUrl) {
            $this->assertDatabaseHas('podcast', [
                'rss_feed_url' => $feedUrl
            ]);
        }


    }




     /**
     * Process podcast command with wrong rss feed url test.
     *
     * @return void
     */
    public function test_podcast_command_with_wrong_rss_feed_url()
    {

               $this->artisan('process:podcast')
                    ->expectsQuestion('Please enter a valid rss feed url', "A real url is meant to be here")
                    ->expectsConfirmation('Have you finished inputting all your rss feed urls?', 'yes')
                    ->expectsOutput('Started processing RSS feeds *******')
                    ->expectsOutput('Completed action with no RSS feed processed')
                    ->doesntExpectOutput('Completed processing all RSS feeds')
                    ->assertExitCode(1);

    }




}
