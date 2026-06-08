<?php

namespace App\Models\DesarrolloSocial;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Bitacora extends Model
{
    use HasFactory;
    protected $table = 'bitacoras';

    protected $fillable = [
        'solicitud_id',
        'user_id',
        'evento',
        'descripcion'
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
