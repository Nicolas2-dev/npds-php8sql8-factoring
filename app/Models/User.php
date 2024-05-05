<?php

namespace App\Models;

use Npds\Auth\UserInterface;
use Npds\Auth\UserTrait;
use Npds\Database\ORM\Model;



class User extends Model implements UserInterface
{
    use UserTrait;

    //
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = array('username', 'password', 'realname', 'email', 'activated', 'activation_code');

    protected $hidden = array('password', 'remember_token', 'activation_code');
};
