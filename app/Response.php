<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['data'];

    /**
     * Get the form that owns the response.
     */
    public function form()
    {
        return $this->belongsTo('App\Form');
    }


    /**
     * Get the response's data
     *
     * @param  string  $value
     * @return string
     */
    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }
}
