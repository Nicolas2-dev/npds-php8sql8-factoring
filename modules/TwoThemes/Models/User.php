<?php

namespace Modules\TwoThemes\Models;

use Two\Database\ORM\Model as BaseModel;


class User extends BaseModel
{
    protected $table = 'users';

    protected $primaryKey = 'uid';


    public static function getUserMailAndLanguage($id)
    {
        return static::select('email', 'user_langue')->where('uid', $id)->first();
    }


}
