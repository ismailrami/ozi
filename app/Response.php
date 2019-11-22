<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Ansewer extends Eloquent{

    protected $connection = 'mongodb';

    protected $collection = 'ansewers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ansewer',
        'is_right',
        'mark',
    ];
}
