<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    protected $table = 'reminders';

    protected $fillable = [
        'customer_id',
        'user_id',
        'sales_agency',
        'total_ssl',
        'jenis_reminder',
        'status',
        'keterangan',
        'tanggal_reminder',
    ];

    protected $casts = [
        'tanggal_reminder' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            Customer::class,
            'customer_id',
            'id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'Selesai' => 'success',
            'Proses' => 'warning',
            'Pending' => 'secondary',
            default => 'danger',
        };
    }
}