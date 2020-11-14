<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use helpers\generateRandomString;


class Organization extends Model
{
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function unconfirmedUsers()
    {
        return $this->hasMany(User::class)->where('is_confirmed_in_organization', false);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function generateCode()
    {
        $s = new generateRandomString();

        return $s->generateRandomString(6) . '-' . mt_rand(100000, 999999);
    }

    public function generateTitle($name)
    {
        return 'Организация ' . $name;
    }
}
