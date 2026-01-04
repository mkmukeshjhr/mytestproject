<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;
    protected $table = 'blogs';
    protected $fillable = [
        'title',
        'blogImage',
        'description',
        'author',
        'viewer',
        'createdBy',
        'modifiedBy',
        'postedOn',
        'extension',
        'slug',
    ];

    public function getBlogImageUrlAttribute()
    {
        if (!$this->blogImage) {
            return asset('build/assets/images/person.png');
        }

        if (Str::startsWith($this->blogImage, ['http://', 'https://'])) {
            return $this->blogImage;
        }

        return asset($this->blogImage);
    }
}
