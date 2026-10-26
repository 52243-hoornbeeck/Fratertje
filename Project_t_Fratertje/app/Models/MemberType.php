<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberType extends Model
{
    protected $fillable = ['name', 'description'];
    public function members(): HasMany { return $this->hasMany(Member::class); }
    public function rates(): HasMany { return $this->hasMany(MemberTypeRate::class); }
    public function requiresBreedingNumber(): bool { return in_array($this->code, ['adult', 'youth'], true); }
    public function rateForYear(int $year): ?MemberTypeRate
    {
        return $this->rates()->where('year', '<=', $year)->orderByDesc('year')->first();
    }
}
