<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AstrologyVideo extends Model
{
    use HasFactory;
    protected $table = 'astrology_videos';
    protected $fillable = [
        'youtubeLink',
        'coverImage',
        'videoTitle',
        'createdBy',
        'modifiedBy'
    ];


    public function getCoverImageUrlAttribute()
    {
        if (!$this->coverImage) {
            return asset('build/assets/images/person.png');
        }

        if (Str::startsWith($this->coverImage, ['http://', 'https://'])) {
            return $this->coverImage;
        }

        return asset($this->coverImage);
    }
}
