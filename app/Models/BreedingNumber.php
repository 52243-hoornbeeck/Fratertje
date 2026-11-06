<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreedingNumber extends Model
{
    use SoftDeletes;
    protected $fillable = ['breeding_number', 'issue_year'];
    public function member(): BelongsTo { return $this->belongsTo(Member::class)->withTrashed(); }
}
