<?php
/**
 * Two - User
 *
 * @author  Nicolas Devoy
 * @email   nicolas.l.devoy@gmail.com 
 * @version 1.0.0
 * @date    07 Mai 2024
 */

namespace App\Models;

use Two\Auth\UserTrait;
use Two\Auth\UserInterface;
use Two\Database\ORM\Model as BaseModel;
use Two\Foundation\Auth\Access\AuthorizableTrait;


class User extends BaseModel implements UserInterface
{
    use UserTrait, AuthorizableTrait;

    //
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = array('username', 'password', 'realname', 'email', 'activation_code');

    protected $hidden = array('password', 'remember_token');

}
