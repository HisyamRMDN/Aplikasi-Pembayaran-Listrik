<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembayaran extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_pembayaran';

    protected $fillable = [
        'id_tagihan',
        'id_pelanggan',
        'tanggal_pembayaran',
        'bulan_bayar',
        'biaya_admin',
        'total_bayar',
        'status',
        'dibatalkan_pada',
        'alasan_batal',
        'id_user_batal',
        'id_user'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'dibatalkan_pada' => 'datetime',
        'biaya_admin' => 'decimal:2',
        'total_bayar' => 'decimal:2'
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan', 'id_tagihan');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function userPembatal()
    {
        return $this->belongsTo(User::class, 'id_user_batal', 'id_user');
    }

    /** Pembayaran yang masih berlaku (belum dibatalkan). */
    public function scopeAktif($query)
    {
        return $query->where('status', 'berhasil');
    }

    public function isBatal(): bool
    {
        return $this->status === 'batal';
    }
}
