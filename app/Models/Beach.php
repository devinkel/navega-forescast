<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beach extends Model
{

    protected $table = 'beaches';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'beach_name',
        'latitude',
        'longitude'
    ];

    public function forecasts()
    {
        return $this->hasMany(Forecasts::class);
    }
}
