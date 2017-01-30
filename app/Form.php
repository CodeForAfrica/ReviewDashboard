<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{

    /**
     * The users that belong to the form.
     */
    public function users()
    {
        /**
         * ROLES:
         * -----
         * 1 - Admin
         * 2 - Reviewer
         * 3 - Viewer
         */
        
        return $this->belongsToMany('App\User')->withPivot('role_id')->withTimestamps();
    }

    public function user_reviews($user){
        $reviews = $this->hasMany('App\Review')->where('user_id', $user->id);
        return $reviews;
    }


    /**
     * Get the responses for the form.
     */
    public function responses($user = false)
    {
        $responses = $this->hasMany('App\Response');

        $responses->reviewed = [];
        $responses->reviewed_not = [];
        $responses->reviewed_not_urls = [];

        if ($user){
            foreach ($responses->get() as $response){
                $review = $response->reviews()->where('user_id', $user->id)->first();
                if ($review != null){
                    if ($review->is_complete()){
                        array_push($responses->reviewed, $response);
                    } else {
                        array_push($responses->reviewed_not, $response);
                        array_push($responses->reviewed_not_urls, url('/response/'.$response->id));
                    }
                } else {
                    array_push($responses->reviewed_not, $response);
                    array_push($responses->reviewed_not_urls, url('/response/'.$response->id));
                }

            }
        }
        return $responses;
    }


    /**
     * Get the reviews for the form.
     */
    public function reviews($user = false)
    {
        if ($user){
            return $this->user_reviews($user);
        }
        return $this->hasMany('App\Review');
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



    public function getTopTenResponses()
    {
        $sorted_id = [];
        foreach ($this->reviews as $review) {
            // TODO: sort reviews
        };
    }
}
