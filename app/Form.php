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

    /**
     * Get the responses for the form.
     */
    public function responses()
    {
        return $this->hasMany('App\Response');
    }


    /**
     * Get the form's response headers
     *
     * @param  string  $value
     * @return string
     */
    public function getResponsesHeadersAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Get the form's ratings config
     *
     * @param  string  $value
     * @return string
     */
    public function getRatingsConfigAttribute($value)
    {
        return json_decode($value, true);
    }


    /**
     * Set the form's ratings config
     *
     * @param  string  $value
     * @return string
     */
    public function setRatingsConfigAttribute($value)
    {
        $this->attributes['ratings_config'] = json_encode($value);
    }
}
