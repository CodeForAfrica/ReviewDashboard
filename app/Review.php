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
        'response_id', 'user_id', 'form_id'
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
     * Get the form that is tied to the review.
     */
    public function form()
    {
        return $this->belongsTo('App\Form');
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

    public function is_complete()
    {
        $is_complete = true;
        $ratings_config = $this->form->ratings_config;
        foreach ((array)$this->feedback as $index => $feedback){
            if (trim($ratings_config[$index]['title']) == 'NEED TO RECUSE YOURSELF?'){
                if ($feedback == 'yes') { $is_complete = true; break; };
            };
            if ($ratings_config[$index]['required'] == 'yes'
                && trim($feedback) == ''){ $is_complete = false; };
        }
        return $is_complete;
    }
}
