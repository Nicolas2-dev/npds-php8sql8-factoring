<?php

namespace Modules\TwoBlock\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class LeftBlock extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'leftblocks';


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
     * [makelblock description]
     *
     * @param   string  $title    [$title description]
     * @param   string  $content  [$content description]
     * @param   int     $members  [$members description]
     * @param   string  $Mmember  [$Mmember description]
     * @param   string|int     $Lindex   [$Lindex description]
     * @param   int     $Scache   [$Scache description]
     * @param   string  $BLaide   [$BLaide description]
     * @param   int     $SHTML    [$SHTML description]
     * @param   int     $css      [$css description]
     *
     * @return  void
     */
    function makelblock(string $title, string $content, int $members, string $Mmember, string|int $Lindex, int $Scache, string $BLaide, int $SHTML, int $css): void
    {
        if (is_array($Mmember) and ($members == 1)) {
            $members = implode(',', $Mmember);
            if ($members == 0) {
                $members = 1;
            }
        }

        if (empty($Lindex)) {
            $Lindex = 0;
        }

        $title = stripslashes(str::FixQuotes($title));
        $content = stripslashes(str::FixQuotes($content));

        if ($SHTML != 'ON') {
            $content = strip_tags(str_replace('<br />', '\n', $content));
        }

        DB::table('lblocks')->insert(array(
            'title'     => $title,
            'content'   => $content,
            'member'    => $members,
            'Lindex'    => $Lindex,
            'cache'     => $Scache,
            'actif'     => 1,
            'css'       => $css,
            'aide'      => $BLaide,
        ));

        global $aid;
        logs::Ecr_Log('security', "MakeLeftBlock(" . language::aff_langue($title) . ") by AID : $aid", "");

        Header('Location: '. site_url('admin.php?op=blocks'));
    }

    /**
     * [changelblock description]
     *
     * @param   int     $id       [$id description]
     * @param   string  $title    [$title description]
     * @param   string  $content  [$content description]
     * @param   int     $members  [$members description]
     * @param   string  $Mmember  [$Mmember description]
     * @param   int     $Lindex   [$Lindex description]
     * @param   int     $Scache   [$Scache description]
     * @param   int|string     $Sactif   [$Sactif description]
     * @param   string  $BLaide   [$BLaide description]
     * @param   int     $css      [$css description]
     *
     * @return  void
     */
    function changelblock(int $id, string $title, string $content, int $members, string $Mmember, int $Lindex, int $Scache, int|string $Sactif, string $BLaide, int $css): void
    {
        if (is_array($Mmember) and ($members == 1)) {
            $members = implode(',', $Mmember);
            if ($members == 0) {
                $members = 1;
            }
        }

        if (empty($Lindex)) { 
            $Lindex = 0;
        }

        $title = stripslashes(str::FixQuotes($title));

        if ($Sactif == 'ON') { 
            $Sactif = 1;
        } else {
            $Sactif = 0;
        }

        if ($css) { 
            $css = 1;
        } else {
            $css = 0;
        }

        $content = stripslashes(str::FixQuotes($content));
        $BLaide = stripslashes(str::FixQuotes($BLaide));

        DB::table('lblocks')->where('id', $id)->update(array(
            'title'     => $title,
            'content'   => $content,
            'member'    => $members,
            'Lindex'    => $Lindex,
            'cache'     => $Scache,
            'actif'     => $Sactif,
            'aide'      => $BLaide,
            'css'       => $css,

        ));

        global $aid;
        logs::Ecr_Log('security', "ChangeLeftBlock(" . language::aff_langue($title) . " - $id) by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=blocks'));
    }

    /**
     * [changedroitelblock description]
     *
     * @param   int     $id       [$id description]
     * @param   string  $title    [$title description]
     * @param   string  $content  [$content description]
     * @param   int     $members  [$members description]
     * @param   int     $Mmember  [$Mmember description]
     * @param   int     $Lindex   [$Lindex description]
     * @param   int     $Scache   [$Scache description]
     * @param   int     $Sactif   [$Sactif description]
     * @param   string  $BLaide   [$BLaide description]
     * @param   int     $css      [$css description]
     *
     * @return  void
     */
    function changedroitelblock(int $id, string $title, string $content, int $members, int $Mmember, int $Lindex, int $Scache, int $Sactif, string $BLaide, int $css): void
    {
        if (is_array($Mmember) and ($members == 1)) {
            $members = implode(',', $Mmember);
            if ($members == 0) {
                $members = 1;
            }
        }

        if (empty($Lindex)) {
            $Lindex = 0;
        }

        $title = stripslashes(str::FixQuotes($title));

        if ($Sactif == 'ON') {
            $Sactif = 1;
        } else {
            $Sactif = 0;
        }

        if ($css) {
            $css = 1;
        } else {
            $css = 0;
        }

        $content = stripslashes(str::FixQuotes($content));
        $BLaide = stripslashes(str::FixQuotes($BLaide));

        DB::table('rblocks')->insert(array(
            'title'     => $title,
            'content'   => $content,
            'member'    => $members,
            'Rindex'    => $Lindex,
            'cache'     => $Scache,
            'actif'     => $Sactif,
            'css'       => $css,
            'aide'      => $BLaide,
        ));

        DB::table('lblocks')->where('id', $id)->delete();

        global $aid;
        logs::Ecr_Log('security', "MoveLeftBlockToRight(" . language::aff_langue($title) . " - $id) by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=blocks'));
    }

    /**
     * [deletelblock description]
     *
     * @param   int   $id  [$id description]
     *
     * @return  void
     */
    function deletelblock(int $id): void
    {
        DB::table('lblocks')->where('id', $id)->delete();

        global $aid;
        logs::Ecr_Log('security', "DeleteLeftBlock($id) by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=blocks'));
    }


}