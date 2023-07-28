<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Region;

class Weather extends Model
{
    use HasFactory;

    // weather:regions 1:1
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
