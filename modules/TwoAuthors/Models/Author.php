<?php

namespace Modules\TwoAuthors\Models;

use Two\Database\ORM\Model as BaseModel;


class Author extends BaseModel
{

    /**
     * [$table description]
     *
     * @var [type]
     */
    protected $table = 'authors';

    /**
     * [$primaryKey description]
     *
     * @var [type]
     */
    protected $primaryKey = 'aid';

    /**
     * [$fillable description]
     *
     * @var [type]
     */
    protected $fillable = array('name', 'url', 'email', 'counter', 'radminfilem', 'radminsuper');

    /**
     * [$hidden description]
     *
     * @var [type]
     */
    protected $hidden = array('pwd', 'hashkey');
    
}
