<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    //
    /**
     * The users that belong to the form.
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
