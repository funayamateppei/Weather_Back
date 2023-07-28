<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Region;

class Prefecture extends Model
{
    use HasFactory;

    // prefectures:regions 1:多
    public function regions()
    {
        return $this->hasMany(Region::class);
    }
}
