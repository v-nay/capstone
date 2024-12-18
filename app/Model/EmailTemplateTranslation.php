<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmailTemplateTranslation extends Model
{
    protected $fillable = [
        'email_template_id', 'language_code', 'subject', 'template',
    ];
}
