<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PodcastEpisode extends Model {
    use HasFactory;

    protected $table = 'podcast_episode';

    public $timestamps = true;

    protected $fillable = ['title', 'description', 'audio_url', 'podcast_guid', 'date_published', 'podcast_id'];

    public function podcast() {
        return $this->belongsTo(Podcast::class);
    }
    

}

