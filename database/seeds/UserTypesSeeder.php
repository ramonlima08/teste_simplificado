<?php

use Illuminate\Database\Seeder;
use App\Models\UserTypes;

class UserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserTypes::create(['id'=>1, 'type' => 'Usuário']);
        UserTypes::create(['id'=>2, 'type' => 'Lojista']);

    }
}
