<?php

namespace App\Models\DesarrolloSocial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estado extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'estados';

    protected $fillable = ['nombre'];

    // muchas solicitudes pertenecen a un solo estado
    public function solicitudes()
    {
        return $this->hasMany(
            Solicitud::class, 'estado_id'
        );
    }
}
