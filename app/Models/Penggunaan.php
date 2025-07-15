<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penggunaan extends Model
{
    protected $primaryKey = 'id_penggunaan';

    protected $fillable = [
        'id_pelanggan',
        'bulan',
        'tahun',
        'meter_awal',
        'meter_akhir'
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'meter_awal' => 'integer',
        'meter_akhir' => 'integer'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'id_penggunaan', 'id_penggunaan');
    }

    public function getJumlahMeterAttribute()
    {
        return $this->meter_akhir - $this->meter_awal;
    }
}
