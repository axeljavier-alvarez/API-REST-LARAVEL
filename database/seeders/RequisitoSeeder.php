<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DesarrolloSocial\Requisito;

class RequisitoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $requisitos = [
            'Fotocopia del boleto de ornato',
            'Fotocopia de recibo agua, luz o teléfono del lugar de su residencia',
            'Fotocopia simple de su Documento Personal de Identificación',
            'Fotocopia simple de cédula, cierre de pensum o título que acredite su profesión u oficio a presentar',
            'Cargas familiares',
            'Constancia trámite DPI',
            'Certificación de nacimiento extendida por RENAP',
            'Negativa de nacimiento del menor de edad',
            'Negativa de nacimiento extendida por el Registro Nacional de las Personas RENAP',
            'Resolución judicial que conste la detención de una persona. (prevención policial, auto de procesamiento etc.',
            'Resolución judicial que ordene el procesamiento de niños y/o adolescentes en conflicto con la ley penal',
            'Fotocopia simple del Documento Personal de Identificación de quien se encuentra privado de libertad'
        ];

        foreach($requisitos as $nombre){
            Requisito::firstOrCreate([
                'nombre' => $nombre
            ]);
        }
    }
}
