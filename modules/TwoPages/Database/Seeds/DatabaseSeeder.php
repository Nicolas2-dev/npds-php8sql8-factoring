<?php

namespace Modules\TwoPages\Database\Seeds;

use Two\Database\ORM\Model;
use Two\Database\Seeder;


class DatabaseSeeder extends Seeder
{

    /**
     * Run the Database Seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call('Modules\TwoPages\Database\Seeds\FoobarTableSeeder');
    }
}
