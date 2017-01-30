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
     * Get the reviews that have been given to this response.
     */
    public function reviews($user = false)
    {
        if ($user){
            $review = $this->hasMany('App\Review')->where('user_id', $user->id)->first();
            if ($review){ if (count($review->feedback) == 0){ $review = null; }; };
            return $review;
        }
        return $this->hasMany('App\Review');
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
