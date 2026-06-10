<?php

namespace App\Models\DesarrolloSocial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisito extends Model
{
    use HasFactory;
    protected $table = 'requisitos';
    public $timestamps = false;
    protected $fillable = ['nombre'];

    public function tramites()
    {
        return $this->belongsToMany(
            Tramite::class,
            'requisitos_tramites',
            'requisito_id',
            'tramite_id'
        );
    }

}
