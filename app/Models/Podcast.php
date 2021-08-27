<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Podcast extends Model {
    use HasFactory;

    protected $table = 'podcast';

    public $timestamps = true;

    protected $fillable = ['title', 'art_work_url', 'rss_feed_url', 'description', 'language', 'website_url'];

    public function episodes() {
        return $this->hasMany(PodcastEpisode::class);
    }
    

}

