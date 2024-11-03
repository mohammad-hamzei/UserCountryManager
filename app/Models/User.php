<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'country_id', 'currency_id', 'email'];
//    protected $hidden = ['country_id', 'currency_id', 'created_at', 'updated_at'];

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function getCountryNameAttribute()
    {
        return $this->country ? $this->country->name : null;
    }

    public function getCurrencyCodeAttribute()
    {
        return $this->currency ? $this->currency->code : null;
    }
}
