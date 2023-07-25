<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Prefecture;
use App\Models\Weather;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'prefecture_id',
        'region',
        'weather_id'
    ];

    // regions:prefectures 多:1
    public function prefectures()
    {
        return $this->belongsTo(Prefecture::class);
    }

    // weather:regions 1:1
    public function weather()
    {
        return $this->belongsTo(Weather::class);
    }
}
