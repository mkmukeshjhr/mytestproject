<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class AstrotalkInNews extends Model
{
    use HasFactory;
    protected $table = 'astrotalk_in_news';
    protected $fillable = [
        'newsDate',
        'channel',
        'link',
        'bannerImage',
        'description',
        'createdBy',
        'modifiedBy'
    ];

    public function getBannerImageUrlAttribute()
    {
        if (!$this->bannerImage) {
            return asset('build/assets/images/person.png');
        }

        if (Str::startsWith($this->bannerImage, ['http://', 'https://'])) {
            return $this->bannerImage;
        }

        return asset($this->bannerImage);
    }
}
