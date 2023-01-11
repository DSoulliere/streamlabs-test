<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'subscription_id',
        'user_id',
        'plan_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */

    public function plan()
    {
        return $this->hasOne(plan::class);
    }


}
