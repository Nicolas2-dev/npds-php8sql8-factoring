<?php

namespace Modules\TwoBlock\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class AdminBlock extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'adminblock';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'ablock';

        $this->f_titre = __d('two_blocks', 'Bloc Administration');

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
     * [ablock description]
     *
     * @return  void
     */
    function ablock(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('adminblock'));
        adminhead($f_meta_nom, $f_titre);

        echo '
            <hr />
            <h3 class="mb-3">'. __d('two_blocks', 'Editer le Bloc Administration') .'</h3>';

        $block = DB::table('block')->select('title', 'content')->find(2);

        if (!empty($block)) {
            echo '
            <form id="adminblock" action="'. site_url('admin.php') .'" method="post" class="needs-validation">
                <div class="form-floating mb-3">
                <textarea class="form-control" type="text" name="title" id="title" maxlength="1000" style="height:70px;">'. $block['title'] .'</textarea>
                <label for="title">'. __d('two_blocks', 'Titre') .'</label>
                <span class="help-block text-end"><span id="countcar_title"></span></span>
                </div>
                <div class="form-floating mb-3">
                <textarea class="form-control" type="text" rows="25" name="content" id="content" style="height:170px;">'. $block['content'] .'</textarea>
                <label for="content">'. __d('two_blocks', 'Contenu') .'</label>
                </div>
                <input type="hidden" name="op" value="changeablock" />
                <button class="btn btn-primary btn-block" type="submit">'. __d('two_blocks', 'Valider') .'</button>
            </form>';

            $arg1 = '
            var formulid = ["adminblock"];
            inpandfieldlen("title",1000);';
        }

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [changeablock description]
     *
     * @param   string  $title    [$title description]
     * @param   string  $content  [$content description]
     *
     * @return  void
     */
    function changeablock(string $title, string $content): void
    {
        $title = stripslashes(str::FixQuotes($title));
        $content = stripslashes(str::FixQuotes($content));

        DB::table('block')->where('id', 2)->update(array(
            'title'     => $title,
            'content'   => $content,
        ));

        global $aid;
        logs::Ecr_Log('security', "ChangeAdminBlock() by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=adminMain'));
    }


}