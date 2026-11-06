<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Address extends Model
{
    protected $fillable = ['street', 'house_number', 'postal_code', 'city'];
    public function member(): HasOne { return $this->hasOne(Member::class)->withTrashed(); }
}
