<?php

namespace App\Models\DesarrolloSocial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    use HasFactory;
    protected $table = 'tramites';
    protected $fillable = ['nombre'];
    public $timestamps = false;

    public function requisitos()
    {
        return $this->belongsToMany(
            Requisito::class,
            'requisitos_tramites',
            'tramite_id',
            'requisito_id'
        );
    }

    // Un trámite tiene muchas solicitudes asociadas
    public function solicitudes()
    {
        return $this->hasMany(
            Solicitud::class, 'tramite_id'
        );
    }
}