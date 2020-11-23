<?php

namespace App;

use helpers\generateRandomString;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public static function generateCode()
    {
        $s = new generateRandomString();

        return $s->generateRandomString(4) . '-' . mt_rand(100000, 999999);
    }
}
