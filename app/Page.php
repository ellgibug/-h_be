<?php

namespace App;

use helpers\generateRandomString;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateCode()
    {
        $s = new generateRandomString();

        return $s->generateRandomString(4) . '-' . mt_rand(1000, 9999);
    }
}
