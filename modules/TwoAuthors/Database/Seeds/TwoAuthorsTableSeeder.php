<?php

namespace Modules\TwoAuthors\Database\Seeds;

use Two\Database\Seeder;
use Two\Database\ORM\Model;
use Modules\TwoAuthors\Models\Author;


class TwoAuthorsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate the table before seeding.
        Author::truncate();

        //
        $user = Author::create(array(
            'aid'             => 'Root',
            'name'            => 'Root',
            'url'             => '',
            'email'           => 'root@npds.org',
            'pwd'             => 'd.8V.L9nSMMvE',
            'hashkey'         => 0,
            'counter'         => 0,
            'radminfilem'     => 0,
            'radminsuper'     => 1,
        ));
    }

}
