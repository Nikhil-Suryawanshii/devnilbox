<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class ShopPost extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    
    protected $appends = ['thumbnails'];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'shop_post_media');
    }

    /**
     * Generate thumbnails for the medias.
     */
    public function thumbnails(): Attribute
    {
        return Attribute::make(
            get: function () {
                $thumbnails = collect([]);
                foreach ($this->media as $media) {
                    $thumbnail = asset('default/default.jpg');
                    if ($media && Storage::exists($media->src)) {
                        $thumbnail = Storage::url($media->src);
                    }
                    $thumbnails[] = (object) [
                        'id' => $media?->id,
                        'thumbnail' => $thumbnail,
                        'url' => null,
                        'type' => $media->type ?? 'image',
                    ];
                }
                return $thumbnails;
            }
        );
    }
}
