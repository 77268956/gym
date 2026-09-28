<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingConfig extends Model
{
    protected $fillable = [
        'hero_title', 'hero_subtitle', 'hero_image',
        'about_text', 'about_image', 'services',
        'contact_phone', 'contact_email', 'contact_address',
        'contact_facebook', 'contact_instagram', 'contact_whatsapp',
        'primary_color',
    ];

    protected $casts = [
        'services' => 'array',
    ];
}
