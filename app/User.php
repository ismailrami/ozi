<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    protected $connection = 'mongodb';
    
    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 
        'first_name',
        'last_name',
        'city',
        'mobile',
        'driver_licence',
        'function',
        'source',
        'pre_sal',
        'act_sal',
        'mobility',
        'comm_rh',
        'comm_ref',
        'disponibility',
        'status'
    ];
}
