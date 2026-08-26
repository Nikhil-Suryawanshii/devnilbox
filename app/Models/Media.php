<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function srcUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->src && \App\Support\PublicMedia::exists($this->src)) {
                    return \App\Support\PublicMedia::url($this->src);
                }

                // return asset('default/default.jpg');
                                return \App\Support\PublicMedia::url(null);

            },
        );
    }
}
