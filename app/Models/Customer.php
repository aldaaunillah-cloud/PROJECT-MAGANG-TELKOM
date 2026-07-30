<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        // Data utama
        'snd',
        'snd_group',
        'ncli',
        'nama',
        'alamat',
        'sto',
        'datel',
        'agency',
        'sales',
        'billing_ke',
        'saldo',
        'status_bayar',
        'tag_total',
        'produk',
        'eksepsi_desc',
        'desc_newbill',
        'usage_desc',
        'umur_customer',
        
        // Data pembayaran
        'paid_l11',
        'tgl_paid',
        'paid_rp',
        'coll_agent',
        'tgl_klaim',
        'amount_klaim',
        'user_klaim',
        'tgl_paid_n1',
        
        // Data agency
        'agency_psb',
        'sales_agency',
        'ppp',
        'caring_mybrains',
        
        // File
        'ssl_file',
    ];

    protected $casts = [
        'tgl_klaim' => 'date',
        'tgl_paid' => 'date',
        'tgl_paid_n1' => 'date',
        'saldo' => 'decimal:2',
        'tag_total' => 'decimal:2',
        'billing_ke' => 'integer',
        'umur_customer' => 'integer',
        'paid_rp' => 'decimal:2',
        'amount_klaim' => 'decimal:2',
    ];

    // ============== SCOPES ==============
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status_bayar', $status);
        }
        return $query;
    }

    public function scopeByDatel($query, $datel)
    {
        if ($datel) {
            return $query->where('datel', $datel);
        }
        return $query;
    }

    public function scopeByAgency($query, $agency)
    {
        if ($agency) {
            return $query->where('agency', $agency);
        }
        return $query;
    }

    public function scopeBySales($query, $sales)
    {
        if ($sales) {
            return $query->where('sales', $sales);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('snd', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('sales', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeApplyFilters($query, $filters)
    {
        if (!empty($filters['datel'])) {
            $query->where('datel', $filters['datel']);
        }
        if (!empty($filters['agency'])) {
            $query->where('agency', $filters['agency']);
        }
        if (!empty($filters['sales'])) {
            $query->where('sales', $filters['sales']);
        }
        if (!empty($filters['status'])) {
            $query->where('status_bayar', $filters['status']);
        }
        return $query;
    }

    // ============== ACCESSORS ==============
    public function getStatusBadgeAttribute()
    {
        return match ($this->status_bayar) {
            'Sdh Bayar', 'Lunas' => 'success',
            'Menunggu', 'Proses' => 'warning',
            default => 'danger',
        };
    }

    // ============== RELATIONS ==============
    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }
}