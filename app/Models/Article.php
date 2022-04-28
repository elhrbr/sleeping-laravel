<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cviebrock\EloquentSluggable\Sluggable;
use Spatie\Translatable\HasTranslations;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property integer $id
 * @property string $title
 * @property string $slug
 * @property string $excerpt
 * @property string $body
 * @property string $thumbnail
 * @property string $image
 * @property string $created_at
 * @property string $updated_at
 */
class Article extends Model
{
    use HasFactory, HasTranslations, Sluggable;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';


    /**
     * @var array
     */

    public array $translatable = ['title', 'slug', 'excerpt', 'body'];


    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getTitlesAttribute()
    {
        return $this->getTranslations('title');
    }

    public function getSlugsAttribute()
    {
        return $this->getTranslations('title');
    }

    public function getBodiesAttribute()
    {
        return $this->getTranslations('title');
    }

    public function getExcerptsAttribute()
    {
        return $this->getTranslations('title');
    }





}
