<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberTypeRate extends Model
{
    protected $fillable = ['year', 'amount_cents'];
    protected function casts(): array { return ['year' => 'integer', 'amount_cents' => 'integer']; }
    public function memberType(): BelongsTo { return $this->belongsTo(MemberType::class); }
}
