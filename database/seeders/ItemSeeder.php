<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $items = [
            'Colchoneta',
            'Pelota de fútbol',
            'Pelota de yoga',
            'Pesas',
            'Bandas elásticas',
            'Conos',
            'Escalera de agilidad',
            'Paracaídas de velocidad',
            'Barras portátiles',
            'Cuerda para saltar',
            'Mini trampolín',
            'Aros de coordinación',
            'Step',
            'Cinta elástica',
            'Cronómetro',
            'Tablet o celular para seguimiento',
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(['name' => $item]);
        }
    }
}