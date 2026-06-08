<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DesarrolloSocial\Estado;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            'Pendiente',
            'Analisis',
            'Visita asignada',
            'Visita realizada',
            'Por autorizar',
            'Emitido',
            'Autorizado',
            'Previo',
            'Rechazado'
        ];

        foreach($estados as $nombre){
            Estado::firstOrCreate([
                'nombre' => $nombre
            ]);
        }
    }
}
