<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarif extends Model
{

    use HasFactory;
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
