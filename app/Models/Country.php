<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Define a one-to-many relationship with Currency.
     */
    public function currencies()
    {
        return $this->hasMany(Currency::class);
    }
}
