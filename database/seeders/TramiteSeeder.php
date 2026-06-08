<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DesarrolloSocial\Tramite;

class TramiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tramites = [
            'Magisterio', 
            'Solicitar DPI al Registro Nacional de las Personas',
            'Inscripción extemporánea de un menor de edad ante el Registro Nacional de las Personas',
            'Inscripción extemporánea de un mayor de edad ante el Registro Nacional de las Personas',
            'Tramites legales en materia civil',
            'Tramites legales en materia penal, si una persona se encuentra privada de libertad'
        ];

        foreach($tramites as $nombre){
            Tramite::firstOrCreate([
                'nombre' => $nombre
            ]);
        }

    }
}
