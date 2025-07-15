<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{
    protected $primaryKey = 'id_tarif';

    protected $fillable = [
        'daya',
        'tarif_per_kwh'
    ];

    protected $casts = [
        'tarif_per_kwh' => 'decimal:2'
    ];

    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class, 'id_tarif', 'id_tarif');
    }
}
