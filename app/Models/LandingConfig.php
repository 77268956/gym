<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingConfig extends Model
{
    protected $fillable = [
        'hero_title', 'hero_subtitle', 'hero_image',
        'about_text', 'about_image', 'services',
        'hero_slides', 'equipment', 'trainers', 'facilities',
        'equipment_heading', 'equipment_intro',
        'trainers_heading', 'trainers_intro',
        'facilities_heading', 'facilities_intro',
        'contact_phone', 'contact_email', 'contact_address',
        'contact_facebook', 'contact_instagram', 'contact_whatsapp',
        'primary_color', 'secondary_color',
    ];

    protected $casts = [
        'services' => 'array',
        'hero_slides' => 'array',
        'equipment' => 'array',
        'trainers' => 'array',
        'facilities' => 'array',
    ];
}
