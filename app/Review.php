<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'response_id', 'user_id',
    ];


    /**
     * Get the response that is tied to the review.
     */
    public function response()
    {
        return $this->belongsTo('App\Response');
    }

    /**
     * Get the user that created the review.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }



    /**
     * Get the reviews's feedback
     *
     * @param  string  $value
     * @return string
     */
    public function getFeedbackAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Set the form's ratings config
     *
     * @param  string  $value
     * @return string
     */
    public function setFeedbackAttribute($value)
    {
        $this->attributes['feedback'] = json_encode($value);
    }
}
