<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pelanggan extends Authenticatable
{
    use Notifiable;
    use HasFactory;
    protected $primaryKey = 'id_pelanggan';

    protected $fillable = [
        'username',
        'password',
        'nomor_kwh',
        'nama_pelanggan',
        'alamat',
        'id_tarif'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    // Override username field for authentication
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'id_tarif', 'id_tarif');
    }

    public function penggunaans()
    {
        return $this->hasMany(Penggunaan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'id_pelanggan', 'id_pelanggan');
    }
}
