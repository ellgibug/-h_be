<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
