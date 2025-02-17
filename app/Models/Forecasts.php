<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Forecasts extends Model
{
    protected $table = 'forecasts';
    protected $fillable = ['beach_id', 'forecast'];

    // Definir que 'forecast' é um campo do tipo JSON
    protected $casts = [
        'forecast' => 'array', // Isso permite que o JSON seja convertido para um array automaticamente
    ];

    protected $hidden = ['updated_at'];
}
