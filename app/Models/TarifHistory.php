<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifHistory extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_tarif_history';

    protected $fillable = [
        'id_tarif',
        'tarif_lama',
        'tarif_baru',
        'id_user',
    ];

    protected $casts = [
        'tarif_lama' => 'decimal:2',
        'tarif_baru' => 'decimal:2',
    ];

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'id_tarif', 'id_tarif');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
