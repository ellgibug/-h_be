<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const ROOT = 'root';
    const ADMIN = 'admin';
    const USER = 'user';



    public function user()
    {
        return $this->hasOne(User::class);
    }

}
