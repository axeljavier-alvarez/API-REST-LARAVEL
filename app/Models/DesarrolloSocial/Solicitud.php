<?php

namespace App\Models\DesarrolloSocial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Solicitud extends Model
{
    use HasFactory;
    protected $table = 'solicitudes';

    protected $fillable = [
        'no_solicitud',
        'anio',
        'nombres',
        'apellidos',
        'email',
        'telefono',
        'cui',
        'domicilio',
        'observaciones',
        'razon'
    ];
}
