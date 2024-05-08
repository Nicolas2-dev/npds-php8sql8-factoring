<?php

namespace Modules\TwoBlocks\Support\Traits;

use Two\Support\Facades\DB;


trait PdstBlocksTrait 
{

    /**
     * [pdstBlock description]
     *
     * @param   [type]  $pdst  [$pdst description]
     *
     * @return  [type]         [return description]
     */
    public function pdstBlock($pdst)
    {
        $nb_blg_actif = DB::table('lblocks')->select('*')->where('actif', 1)->get();

        $nb_bld_actif = DB::table('rblocks')->select('*')->where('actif', 1)->get();

        if ($nb_blg_actif == 0) {
            switch ($pdst) {
                case '0':
                    $pdst = '-1';
                    break;
        
                case '1':
                    $pdst = '2';
                    break;
        
                case '3':
                    $pdst = '5';
                    break;
        
                case '4':
                    $pdst = '2';
                    break;
        
                case '6':
                    $pdst = '-1';
                    break;
            }
        }
        
        if ($nb_bld_actif == 0) {
            switch ($pdst) {
                case '1':
                    $pdst = '0';
                    break;
        
                case '2':
                    $pdst = '-1';
                    break;
        
                case '3':
                    $pdst = '0';
                    break;
        
                case '4':
                    $pdst = '6';
                    break;
        
                case '5':
                    $pdst = '-1';
                    break;
            }
        }

        return $pdst;
    }

}
