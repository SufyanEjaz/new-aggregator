<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    // Hide timestamps from API responses
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'source_id', 'author', 'title', 'slug', 'url', 'url_to_image', 'description', 'content','language' ,'published_at',
    ];

    public function preferences()
    {
        return $this->hasOne(Preference::class);
    }

    // Relationship with Source
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    // Relationship with Categories
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'article_category', 'article_id', 'category_id');
    }
}
