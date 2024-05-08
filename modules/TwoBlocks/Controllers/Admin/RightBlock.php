<?php

namespace Modules\TwoBlock\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class RightBlock extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'rightblocks';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'blocks';

        $this->f_titre = __d('two_blocks', 'Gestion des blocs');

        parent::initialize($request);
    }

    /**
     * 
     */
    public function index(Request $request)
    {

        return $this->createView()
            ->shares('title', __d('two_', ''));
    }

    /**
     * [makerblock description]
     *
     * @param   string  $title    [$title description]
     * @param   string  $content  [$content description]
     * @param   int     $members  [$members description]
     * @param   string  $Mmember  [$Mmember description]
     * @param   int     $Rindex   [$Rindex description]
     * @param   int     $Scache   [$Scache description]
     * @param   string  $BRaide   [$BRaide description]
     * @param   int     $SHTML    [$SHTML description]
     * @param   int     $css      [$css description]
     *
     * @return  void
     */
    function makerblock(string $title, string $content, int $members, string $Mmember, int $Rindex, int $Scache, string $BRaide, int $SHTML, int $css): void
    {
        if (is_array($Mmember) and ($members == 1)) {
            $members = implode(',', $Mmember);
            if ($members == 0) { 
                $members = 1;
            }
        }

        if (empty($Rindex)) {
            $Rindex = 0;
        }

        // $title = stripslashes(str::FixQuotes($title));
        // $content = stripslashes(str::FixQuotes($content));

        $title = stripslashes($title);
        $content = stripslashes($content);

        if ($SHTML != 'ON') {
            $content = strip_tags(str_replace('<br />', "\n", $content));
        }

        DB::table('rblocks')->insert(array(
            'title'       => $title,
            'content'     => $content,
            'memeber'     => $members,
            'Rindex'      => $Rindex,
            'cache'       => $Scache,
            'actif'       => 1,
            'css'         => $css,
            'aide'        => $BRaide
        ));

        global $aid;
        logs::Ecr_Log('security', "MakeRightBlock(". language::aff_langue($title) .") by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=blocks'));
    }

    /**
     * [changerblock description]
     *
     * @param   int     $id       [$id description]
     * @param   string  $title    [$title description]
     * @param   string  $content  [$content description]
     * @param   int     $members  [$members description]
     * @param   string  $Mmember  [$Mmember description]
     * @param   int     $Rindex   [$Rindex description]
     * @param   int     $Scache   [$Scache description]
     * @param   string  $Sactif   [$Sactif description]
     * @param   string  $BRaide   [$BRaide description]
     * @param   int     $css      [$css description]
     *
     * @return  void
     */
    function changerblock(int $id, string $title, string $content, int $members, string $Mmember, int $Rindex, int $Scache, string $Sactif, string $BRaide, int $css): void
    {
        if (is_array($Mmember) and ($members == 1)) {
            $members = implode(',', $Mmember);
            if ($members == 0) { 
                $members = 1;
            }
        }

        if (empty($Rindex)) { 
            $Rindex = 0;
        }

        //$title = stripslashes(str::FixQuotes($title));
        $title = stripslashes($title);

        if ($Sactif == 'ON') { 
            $Sactif = 1;
        } else {
            $Sactif = 0;
        }

        //$content = stripslashes(str::FixQuotes($content));
        $content = stripslashes($content);
        
        DB::table('rblocks')->where('id', $id)->update(array(
            'title'       => $title,
            'content'     => $content,
            'member'      => $members,
            'Rindex'      => $Rindex,
            'cache'       => $Scache,
            'actif'       => $Sactif,
            'css'         => $css,
            'aide'        => $BRaide
        ));

        global $aid;
        logs::Ecr_Log('security', "ChangeRightBlock(". language::aff_langue($title) ." - $id) by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=blocks'));
    }

    /**
     * [changegaucherblock description]
     *
     * @param   int     $id       [$id description]
     * @param   string  $title    [$title description]
     * @param   string  $content  [$content description]
     * @param   int     $members  [$members description]
     * @param   string  $Mmember  [$Mmember description]
     * @param   int     $Rindex   [$Rindex description]
     * @param   int     $Scache   [$Scache description]
     * @param   string  $Sactif   [$Sactif description]
     * @param   string  $BRaide   [$BRaide description]
     * @param   int     $css      [$css description]
     *
     * @return  void
     */
    function changegaucherblock(int $id, string $title, string $content, int $members, string $Mmember, int $Rindex, int $Scache, string $Sactif, string $BRaide, int $css): void
    {
        if (is_array($Mmember) and ($members == 1)) {
            $members = implode(',', $Mmember);
            if ($members == 0) {
                $members = 1;
            }
        }

        if (empty($Rindex)) {
            $Rindex = 0;
        }

        //$title = stripslashes(str::FixQuotes($title));
        $title = stripslashes($title);

        if ($Sactif == 'ON') { 
            $Sactif = 1;
        } else {
            $Sactif = 0;
        }

        //$content = stripslashes(str::FixQuotes($content));
        $content = stripslashes($content);

        DB::table('lblocks')->insert(array(
            'title'         => $title,
            'content'       => $content,
            'member'        => $members,
            'Lindex'        => $Rindex,
            'cache'         => $Scache,
            'actif'         => $Sactif,
            'css'           => $css,
            'aide'          => $BRaide
        ));

        DB::table('rblocks')->where('id', $id)->delete();

        global $aid;
        logs::Ecr_Log('security', "MoveRightBlockToLeft(". language::aff_langue($title) ." - $id) by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=blocks'));
    }

    /**
     * [deleterblock description]
     *
     * @param   int   $id  [$id description]
     *
     * @return  void
     */
    function deleterblock(int $id): void
    {
        DB::table('rblocks')->where('id', $id)->delete();

        global $aid;
        logs::Ecr_Log('security', "DeleteRightBlock($id) by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=blocks'));
    }


}