<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = ['member_type_id', 'address_id', 'first_name', 'last_name', 'birth_date', 'is_active'];

    protected function casts(): array
    {
        return ['birth_date' => 'date', 'is_active' => 'boolean'];
    }

    public function memberType(): BelongsTo { return $this->belongsTo(MemberType::class); }
    public function address(): BelongsTo { return $this->belongsTo(Address::class); }
    public function breedingNumber(): HasOne { return $this->hasOne(BreedingNumber::class); }
    public function getFullNameAttribute(): string { return $this->first_name.' '.$this->last_name; }
}
