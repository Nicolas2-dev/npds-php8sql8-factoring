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
    


    /**
     * [deletedroits description]
     *
     * @param   string   $del_dr_aid  [$del_dr_aid description]
     *
     * @return  void
     */
    function deletedroits(string $del_dr_aid): void
    {
        DB::table('droits')->where('d_aut_aid', $del_dr_aid)->delete();
    }

    /**
     * [updatedroits description]
     *
     * @param   string  $chng_aid  [$chng_aid description]
     *
     * @return  void
     */
    function updatedroits(string $chng_aid): void
    {
        foreach ($_POST as $y => $w) {
            if (stristr("$y", 'ad_d_')) {
                DB::table('droits')->insert(array(
                    'd_aut_aid' => $chng_aid,
                    'd_fon_fid' => $w,
                    'd_droits'  => 11111,
                ));
            }
        }
    }




}
