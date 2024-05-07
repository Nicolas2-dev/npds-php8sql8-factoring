<?php
/**
 * Two - DatabaseSeeder
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

namespace App\Database\Seeds;

use Two\Database\ORM\Model;
use Two\Database\Seeder;


class DatabaseSeeder extends Seeder
{

    /**
     * Exécutez les graines de la base de données.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        //
        $this->call('App\Database\Seeds\UsersTableSeeder');
    }
}
