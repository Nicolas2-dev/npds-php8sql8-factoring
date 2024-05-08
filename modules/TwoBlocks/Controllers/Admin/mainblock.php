<?php

namespace Modules\TwoBlock\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class MainBlock extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'mainblock';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'mblock';

        $this->f_titre = __d('two_blocks', 'Bloc Principal');

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
     * [mblock description]
     *
     * @return  void
     */
    function mblock():  void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('mainblock'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3>'. __d('two_blocks', 'Edition du Bloc Principal') .'</h3>';

        $block = DB::table('block')->select('title', 'content')->find(1);

        if (!empty($block)) {

            echo '
            <form id="fad_mblock" action="'. site_url('admin.php') .'" method="post">
                <div class="form-floating mb-3">
                    <textarea class="form-control" type="text" id="title" name="title" maxlength="1000" placeholder="'. __d('two_blocks', 'Titre :') .'" style="height:70px;">'. $block['title'] .'</textarea>
                    <label for="title">'. __d('two_blocks', 'Titre') .'</label>
                    <span class="help-block text-end"><span id="countcar_title"></span></span>
                </div>
                <div class="form-floating mb-3">
                    <textarea class="form-control" id="content" name="content" style="height:170px;">'. $block['content'] .'</textarea>
                    <label for="content">'. __d('two_blocks', 'Contenu') .'</label>
                </div>
                <input type="hidden" name="op" value="changemblock" />
                <button class="btn btn-primary btn-block" type="submit">'. __d('two_blocks', 'Valider') .'</button>
            </form>
            <script type="text/javascript">
                //<![CDATA[
                    $(document).ready(function() {
                    inpandfieldlen("title",1000);
                    });
                //]]>
            </script>';
        }

        css::adminfoot('fv', '', '', '');
    }

    /**
     * [changemblock description]
     *
     * @param   string  $title    [$title description]
     * @param   string  $content  [$content description]
     *
     * @return  void              [return description]
     */
    function changemblock(string $title, string $content): void
    {
        $title = stripslashes(str::FixQuotes($title));
        $content = stripslashes(str::FixQuotes($content));

        DB::table('block')->where('id', 1)->update(array(
            'title'     => $title,
            'content'   => $content,
        ));

        global $aid;
        logs::Ecr_Log('security', "ChangeMainBlock(" . language::aff_langue($title) . ") by AID : $aid", '');

        Header('Location: '. site_url('admin.php?op=adminMain'));
    }


}