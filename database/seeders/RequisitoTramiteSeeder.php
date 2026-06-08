<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DesarrolloSocial\Tramite;


class RequisitoTramiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tramite::find(1)->requisitos()->sync([1,2,3,4,5]);
        Tramite::find(2)->requisitos()->sync([7,1,2]);
        Tramite::find(3)->requisitos()->sync([3,1,2,8,2]); 
        Tramite::find(4)->requisitos()->sync([9,1,2]);
        Tramite::find(5)->requisitos()->sync([3,1,2,5,7,9]);
        Tramite::find(6)->requisitos()->sync([3,10,1,2,11,7,12]);
    }
}
