<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name', 'font_defs', 'image_defs', 'colors_defs', 'notes', 'typekit_fonts', 'google_fonts', 'web_fonts',
        ];

    protected $hidden = [];
}
