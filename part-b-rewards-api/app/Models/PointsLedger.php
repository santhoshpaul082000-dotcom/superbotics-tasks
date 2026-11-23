<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointsLedger extends Model
{
    protected $table = 'points_ledger';

    protected $fillable = [
        'user_id',
        'redemption_id',
        'type',
        'points'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function redemption(): BelongsTo
    {
        return $this->belongsTo(Redemption::class);
    }
}
