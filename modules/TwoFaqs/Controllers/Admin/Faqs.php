<?php

namespace Modules\TwoFaqs\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Faqs extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'faqs';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'FaqAdmin';

        $this->f_titre = __d('two_', 'Faq');

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
     * [FaqAdmin description]
     *
     * @return  void
     */
    function FaqAdmin(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('faqs'));
        adminhead($f_meta_nom, $f_titre);

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_faqs', 'Liste des catégories') .'</h3>
        <table id="tad_faq" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-mobile-responsive="true" data-icons-prefix="fa" data-icons="icons" data-buttons-class="outline-secondary">
            <thead class="thead-infos">
                <tr>
                    <th data-sortable="true" data-halign="center" class="n-t-col-xs-10">'. __d('two_faqs', 'Catégories') .'</th>
                    <th data-halign="center" data-align="center" class="n-t-col-xs-2">'. __d('two_faqs', 'Fonctions') .'</th>
                </tr>
            </thead>
            <tbody>';

        $categories = DB::table('faqcategories')->select('id', 'categories')->orderBy('categories', 'ASC')->get();

        foreach($categories as $categorie) {
            echo '
                <tr>
                    <td>
                        <span title="ID : '. $categorie['id'] .'">
                            '. language::aff_langue($categorie['categories']) .'
                        </span>
                        <br />
                        <a href="'. site_url('admin.php?op=FaqCatGo&amp;id_cat='. $categorie['id']) .'" class="noir">
                            <i class="fa fa-level-up-alt fa-lg fa-rotate-90 " title="'. __d('two_faqs', 'Voir') .'" data-bs-toggle="tooltip"></i>&nbsp;&nbsp;'. __d('two_faqs', 'Questions & Réponses') .'&nbsp;
                        </a>
                    </td>
                    <td>
                        <a href="'. site_url('admin.php?op=FaqCatEdit&amp;id_cat='. $categorie['id']) .'">
                            <i class="fa fa-edit fa-lg me-2" title="'. __d('two_faqs', 'Editer') .'" data-bs-toggle="tooltip"></i>
                        </a>
                        <a href="'. site_url('admin.php?op=FaqCatDel&amp;id_cat='. $categorie['id']) .'&amp;ok=0">
                            <i class="fas fa-trash fa-lg text-danger" title="'. __d('two_faqs', 'Effacer') .'" data-bs-toggle="tooltip">
                        </a>
                    </td>
                </tr>';
        }

        echo '
            </tbody>
        </table>
        <hr />
        <h3 class="mb-3">'. __d('two_faqs', 'Ajouter une catégorie') .'</h3>
        <form id="adminfaqcatad" action="'. site_url('admin.php') .'" method="post">
            <fieldset>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="categories">'. __d('two_faqs', 'Nom') .'</label>
                    <div class="col-sm-12">
                    <textarea class="form-control" type="text" name="categories" id="categories" maxlength="255" placeholder="'. __d('two_faqs', 'Catégories') .'" rows="3" required="required" ></textarea>
                    <span class="help-block text-end"><span id="countcar_categories"></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                    <button class="btn btn-outline-primary col-12" type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. __d('two_faqs', 'Ajouter une catégorie') .'</button>
                    <input type="hidden" name="op" value="FaqCatAdd" />
                    </div>
                </div>
            </fieldset>
        </form>';

        $arg1 = '
            var formulid = ["adminfaqcatad"];
            inpandfieldlen("categories",255);';

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [FaqCatGo description]
     *
     * @param   int   $id_cat  [$id_cat description]
     *
     * @return  void
     */
    function FaqCatGo(int $id_cat): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('faqs'));
        adminhead($f_meta_nom, $f_titre);

        $lst_qr = '';

        $faq_categorie_id = DB::table('faqcategories')
            ->leftJoin('faqanswer', 'faqcategories.id', '=', 'faqanswer.id_categorie')
            ->select('faqanswer.id', 'faqanswer.question', 'faqanswer.answer', 'faqcategories.categories')
            ->where('faqcategories.id', $id_cat)->orderBy('faqanswer.id')
            ->get();

        foreach($faq_categorie_id as $faq) { 
            $faq_cat = language::aff_langue($faq['categories']);
            
            if (isset($faq['answer'])) {
                $answer = code::aff_code(language::aff_langue($faq['answer']));
                
                $lst_qr .= '
                <li id="qr_'. $faq['id'] .'" class="list-group-item">
                    <div class="topi">
                        <h5 id="q_'. $faq['id'] .'" class="list-group-item-heading">
                            <a class="" href="'. site_url('admin.php?op=FaqCatGoEdit&amp;id='. $faq['id']) .'" title="'. __d('two_faqs', 'Editer la question réponse') .'" data-bs-toggle="tooltip">
                                '. language::aff_langue($faq['question']) .'
                            </a>
                        </h5>
                        <p class="list-group-item-text">'. metalang::meta_lang($answer) .'</p>
                        <div id="shortcut-tools_'. $faq['id'] .'" class="n-shortcut-tools" style="display:none;">
                            <a class="text-danger btn" href="'. site_url('admin.php?op=FaqCatGoDel&amp;id='. $faq['id']) .'&amp;ok=0" >
                                <i class="fas fa-trash fa-2x" title="'. __d('two_faqs', 'Supprimer la question réponse') .'" data-bs-toggle="tooltip" data-bs-placement="left"></i>
                            </a>
                        </div>
                    </div>
                </li>';
            }
        }

        echo '
        <hr />
        <h3 class="mb-3">'. $faq_cat .'</h3>
        <h4>'. __d('two_faqs', 'Ajouter une question réponse') .'</h4>
        <form id="adminfaqquest" action="'. site_url('admin.php') .'" method="post" name="adminForm">
            <fieldset>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="question">'. __d('two_faqs', 'Question') .'</label>
                    <div class="col-sm-12">
                    <textarea class="form-control" type="text" name="question" id="question" maxlength="255"></textarea>
                    <span class="help-block text-end"><span id="countcar_question"></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="answer">'. __d('two_faqs', 'Réponse') .'</label>
                    <div class="col-sm-12">
                    <textarea class="tin form-control" id="answer" name="answer" rows="15"></textarea>
                    </div>
                </div>';

        echo editeur::aff_editeur("answer", "false");

        echo '
                <div class="mb-3 row">
                    <div class="col-sm-12 d-flex flex-row justify-content-start flex-wrap">
                    <input type="hidden" name="id_cat" value="' . $id_cat . '" />
                    <input type="hidden" name="op" value="FaqCatGoAdd" />
                    <button class="btn btn-primary mb-2 " type="submit"><i class="fa fa-plus-square fa-lg"></i>&nbsp;'. __d('two_faqs', 'Ajouter') .'</button>&nbsp;
                    <button class="btn btn-secondary mb-2 " href="'. site_url('admin.php?op=FaqAdmin') .'">'. __d('two_faqs', 'Retour en arrière') .'</button>
                    </div>
                </div>
            </fieldset>
        </form>
        <h4>'. __d('two_faqs', 'Liste des questions réponses') .'</h4>
        <ul class="list-group">
            '. $lst_qr .'
        </ul>
        <script type="text/javascript">
            //<![CDATA[
                $(document).ready(function() {
                    var topid="";
                    $(".topi").hover(function(){
                    topid = $(this).parent().attr("id");
                    topid = topid.substr(topid.search(/\d/))
                    $button=$("#shortcut-tools_"+topid);
                    $button.show();
                    }, function(){
                    $button.hide();
                });
                });
            //]]>
        </script>';

        $arg1 = '
            var formulid = ["adminfaqquest"];
            inpandfieldlen("question",255);
        ';

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [FaqCatEdit description]
     *
     * @param   int   $id_cat  [$id_cat description]
     *
     * @return  void
     */
    function FaqCatEdit(int $id_cat): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('faqs'));
        adminhead($f_meta_nom, $f_titre);

        $faq_categorie = DB::table('faqcategories')->select('categories')->find($id_cat);

        echo '
        <hr />
        <h3 class="mb-3">'. __d('two_faqs', 'Editer la catégorie') .'</h3>
        <h4><a href="'. site_url('admin.php?op=FaqCatGo&amp;id_cat='. $id_cat) .'">'. $faq_categorie['categories'] .'</a></h4>
        <form id="adminfaqcated" action="'. site_url('admin.php') .'" method="post">
            <fieldset>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="categories">'. __d('two_faqs', 'Nom') .'</label>
                    <div class="col-sm-12">
                    <textarea class="form-control" type="text" name="categories" id="categories" maxlength="255" rows="3" required="required" >'. $faq_categorie['categories'] .'</textarea>
                    <span class="help-block text-end"><span id="countcar_categories"></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                    <input type="hidden" name="op" value="FaqCatSave" />
                    <input type="hidden" name="old_id_cat" value="'. $id_cat .'" />
                    <input type="hidden" name="id_cat" value="'. $id_cat .'" />
                    <button class="btn btn-outline-primary col-12" type="submit"><i class="fa fa-check-square fa-lg"></i>&nbsp;'. __d('two_faqs', 'Sauver les modifications') .'</button>
                    </div>
                </div>
            </fieldset>
        </form>';

        $arg1 = '
            var formulid = ["adminfaqcated"];
            inpandfieldlen("categories",255);';

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [FaqCatGoEdit description]
     *
     * @param   int   $id  [$id description]
     *
     * @return  void
     */
    function FaqCatGoEdit(int $id): void
    {
        global $local_user_language, $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('faqs'));
        adminhead($f_meta_nom, $f_titre);

        $faq = DB::table('faqanswer')
            ->leftJoin('faqcategories', 'faqanswer.id_categorie', '=', 'faqcategories.id')
            ->select('faqanswer.question', 'faqanswer.answer', 'faqanswer.id_categorie', 'faqcategories.categories')
            ->where('faqanswer.id', $id)
            ->first();

        echo '
        <hr />
        <h3 class="mb-3">'. $faq['categories'] .'</h3>
        <h4>'. $faq['question'] .'</h4>
        <h4>'. __d('two_faqs', 'Prévisualiser') .'</h4>';
        echo '
        <label class="col-form-label" for="">
            '. language::aff_local_langue('', 'local_user_language', __d('two_faqs', 'Langue de Prévisualisation')) .'
        </label>
        <div class="card card-body mb-3">
        <p>'. language::preview_local_langue($local_user_language, $faq['question']) .'</p>';

        $answer = code::aff_code($faq['answer']);

        echo '<p>'. metalang::meta_lang(language::preview_local_langue($local_user_language, $answer)) .'</p>
        </div>';

        echo '
        <h4>'. __d('two_faqs', 'Editer Question & Réponse') .'</h4>
        <form id="adminfaqquested" action="'. site_url('admin.php') .'" method="post" name="adminForm">
            <fieldset>
                <div class="mb-3 row">
                    <label class="col-form-label col-12" for="question">'. __d('two_faqs', 'Question') .'</label>
                    <div class="col-sm-12">
                    <textarea class="form-control" type="text" name="question" id="question" maxlength="255">'. $faq['question'] .'</textarea>
                    <span class="help-block text-end"><span id="countcar_question"></span></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-12" for="answer">'. __d('two_faqs', 'Réponse') .'</label>
                    <div class="col-sm-12">
                    <textarea class="tin form-control" name="answer" rows="15">'. $answer .'</textarea>
                    </div>
                </div>
                '. editeur::aff_editeur('answer', '') .'
                <div class="mb-3 row">
                    <div class="col-sm-12 d-flex flex-row justify-content-center flex-wrap">
                    <input type="hidden" name="id" value="'. $id .'" />
                    <input type="hidden" name="op" value="FaqCatGoSave" />
                    <button class="btn btn-outline-primary col-sm-6 mb-2 " type="submit">'. __d('two_faqs', 'Sauver les modifications') .'</button>
                    <button class="btn btn-outline-secondary col-sm-6 mb-2 " href="'. site_url('admin.php?op=FaqCatGo&amp;id_cat='. $faq['id_categorie']) .'" >'. __d('two_faqs', 'Retour en arrière') .'</a>
                    </div>
                </div>
            </fieldset>
        </form>';

        $arg1 = '
            var formulid = ["adminfaqquested"];
            inpandfieldlen("question",255);
        ';

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [FaqCatSave description]
     *
     * @param   int     $old_id_cat  [$old_id_cat description]
     * @param   int     $id_cat      [$id_cat description]
     * @param   string  $categories  [$categories description]
     *
     * @return  void
     */
    function FaqCatSave(int $old_id_cat, int $id_cat, string $categories): void
    {
        if ($old_id_cat != $id_cat) {
            DB::table('faqanswer')->where('id', $old_id_cat)->update(array(
                'id_categorie'  => $id_cat,
            ));
        }

        DB::table('faqcategories')->where('id', $old_id_cat)->update(array(
            'id'            => $id_cat,
            'categories'    => stripslashes(str::FixQuotes($categories)),
        ));

        Header('Location: '. site_url('admin.php?op=FaqAdmin'));
    }

    /**
     * [FaqCatGoSave description]
     *
     * @param   int     $id        [$id description]
     * @param   string  $question  [$question description]
     * @param   string  $answer    [$answer description]
     *
     * @return  void
     */
    function FaqCatGoSave(int $id, string $question, string $answer): void
    {
        DB::table('faqanswer')->where('id', $id)->update(array(
            'question'  => stripslashes(str::FixQuotes($question)),
            'answer'    => stripslashes(str::FixQuotes($answer)),
        ));

        Header('Location: '. site_url('admin.php?op=FaqCatGoEdit&id='. $id));
    }

    /**
     * [FaqCatAdd description]
     *
     * @param   string  $categories  [$categories description]
     *
     * @return  void
     */
    function FaqCatAdd(string $categories): void
    {
        DB::table('faqcategories')->insert(array(
            'categories' => stripslashes(str::FixQuotes($categories)),
        ));

        Header('Location: '. site_url('admin.php?op=FaqAdmin'));
    }

    /**
     * [FaqCatGoAdd description]
     *
     * @param   int     $id_cat    [$id_cat description]
     * @param   string  $question  [$question description]
     * @param   string  $answer    [$answer description]
     *
     * @return  void
     */
    function FaqCatGoAdd(int $id_cat, string $question, string $answer): void
    {
        DB::table('faqanswer')->insert(array(
            'id_categorie' => $id_cat,
            'question'     => stripslashes(str::FixQuotes($question)),
            'answer'       => stripslashes(str::FixQuotes($answer)),
        ));

        Header('Location: '. site_url('admin.php?op=FaqCatGo&id_cat='. $id_cat));
    }

    /**
     * [FaqCatDel description]
     *
     * @param   int   $id_cat  [$id_cat description]
     * @param   int   $ok      [$ok description]
     *
     * @return  void
     */
    function FaqCatDel(int $id_cat, int $ok = 0): void 
    {
        if ($ok == 1) {
            DB::table('faqcategories')->where('id', $id_cat)->delete();
            DB::table('faqanswer')->where('id_categorie', $id_cat)->delete();

            Header('Location: '. site_url('admin.php?op=FaqAdmin'));
        } else {
            global $f_meta_nom, $f_titre;

            include("themes/default/header.php");

            GraphicAdmin(manuel('faqs'));
            adminhead($f_meta_nom, $f_titre);

            echo '
            <hr />
            <div class="alert alert-danger">
                <p><strong>' . __d('two_faqs', 'ATTENTION : êtes-vous sûr de vouloir effacer cette FAQ et toutes ses questions ?') . '</strong></p>
                <a href="'. site_url('admin.php?op=FaqCatDel&amp;id_cat='. $id_cat .'&amp;ok=1') .'" class="btn btn-danger btn-sm">
                    '. __d('two_faqs', 'Oui') .'
                </a>
                &nbsp;
                <a href="'. site_url('admin.php?op=FaqAdmin') .'" class="btn btn-secondary btn-sm">
                    '. __d('two_faqs', 'Non') .'
                </a>
            </div>';

            include("themes/default/footer.php");
        }
    }

    /**
     * [FaqCatGoDel description]
     *
     * @param   int   $id  [$id description]
     * @param   int   $ok  [$ok description]
     *
     * @return  void
     */
    function FaqCatGoDel(int $id, int $ok = 0): void
    {
        if ($ok == 1) {
            DB::table('faqanswer')->where('id', $id)->delete();
            
            Header('Location: '. site_url('admin.php?op=FaqAdmin'));
        } else {
            global $f_meta_nom, $f_titre;

            include("themes/default/header.php");

            GraphicAdmin(manuel('faqs'));
            adminhead($f_meta_nom, $f_titre);

            echo '
            <hr />
            <div class="alert alert-danger">
                <p><strong>'. __d('two_faqs', 'ATTENTION : êtes-vous sûr de vouloir effacer cette question ?') .'</strong></p>
                <a href="'. site_url('admin.php?op=FaqCatGoDel&amp;id='. $id .'&amp;ok=1') .'" class="btn btn-danger btn-sm">
                    '. __d('two_faqs', 'Oui') .'
                </a>
                &nbsp;
                <a href="'. site_url('admin.php?op=FaqAdmin') .'" class="btn btn-secondary btn-sm">
                    '. __d('two_faqs', 'Non') .'
                </a>
            </div>';

            include("themes/default/footer.php");
        }
    }

}