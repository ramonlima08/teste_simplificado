<?php

use Illuminate\Database\Seeder;
use App\Models\TransactionType;

class TransactionTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionType::create(['id'=>1, 'type' => 'Envio para Usuário']);
        TransactionType::create(['id'=>2, 'type' => 'Envio para Lojista']);
    }
}
