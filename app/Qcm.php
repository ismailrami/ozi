<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Qcm extends Eloquent{

    protected $connection = 'mongodb';

    protected $collection = 'qcms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question',
        'type',
        'mark',
    ];
}
