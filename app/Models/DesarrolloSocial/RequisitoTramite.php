<?php

namespace App\Models\DesarrolloSocial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequisitoTramite extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'requisitos_tramites';
    protected $fillable = [
        'requisito_id',
        'tramite_id'
    ];

    public function requisito()
    {
        return $this->belongsTo(Requisito::class, 'requisito_id');
    }

    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'tramite_id');
    }


    public function detallesSolicitud()
    {
        return $this->hasMany(DetalleSolicitud::class, 'requisito_tramite_id');
    }
}
