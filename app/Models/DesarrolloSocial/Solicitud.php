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
        'estado_id',
        'razon',
        'tramite_id',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    public function detallesSolicitudes()
    {
        return $this->hasMany(
            DetalleSolicitud::class, 'solicitud_id'
        );
    }

    public function bitacoras(){
        return $this->hasMany(Bitacora::class, 'solicitud_id');
    }

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    
}
