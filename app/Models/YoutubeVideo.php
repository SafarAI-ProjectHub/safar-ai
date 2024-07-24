<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YoutubeVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'video_id',
        'description',
        'thumbnail_url',
        'view_count',
        'like_count',
        'comment_count',
        'age_group',
    ];
}