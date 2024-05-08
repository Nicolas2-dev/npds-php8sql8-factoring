<?php

namespace Modules\TwoReviews\Controllers\Admin;


use Two\Http\Request;

use Modules\TwoCore\Core\AdminController;


class Reviews extends AdminController
{

    /**
     * 
     */
    protected $pdst = 0;

    /**
     * 
     */
    protected $hlpfile = 'reviews';


    /**
     * Method executed before any action.
     */
    protected function initialize(Request $request)
    {
        $this->f_meta_nom = 'reviews';

        $this->f_titre = __d('two_reviews', 'Critiques');

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
     * [mod_main description]
     *
     * @param   string  $title        [$title description]
     * @param   string  $description  [$description description]
     *
     * @return  void
     */
    function mod_main(string $title, string $description): void
    {
        DB::table('reviews_main')->update(array(
            'title'         => stripslashes(str::FixQuotes($title)),
            'description'   => stripslashes(str::FixQuotes($description)),
        ));

        Header('Location: '. site_url('admin.php?op=reviews'));
    }

    /**
     * [reviews description]
     *
     * @return  void
     */
    function reviews(): void
    {
        global $f_meta_nom, $f_titre;

        include("themes/default/header.php");

        GraphicAdmin(manuel('reviews'));
        adminhead($f_meta_nom, $f_titre);

        $main = DB::table('reviews_main')->select('title', 'description')->first();

        echo '
        <hr />
        <h3>'. __d('two_reviews', 'Configuration de la page') .'</h3>
        <form id="reviewspagecfg" class="" action="'. site_url('admin.php') .'" method="post">
            <fieldset>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="tit_cri">'. __d('two_reviews', 'Titre de la Page des Critiques') .'</label>
                    <div class="col-sm-12">
                    <input class="form-control" type="text" id="tit_cri" name="title" value="'. $main['title'] .'" maxlength="100" />
                    <span class="help-block text-end" id="countcar_tit_cri"></span>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-form-label col-sm-12" for="description">'. __d('two_reviews', 'Description de la Page des Critiques') .'</label>
                    <div class="col-sm-12">
                    <textarea class="form-control" id="description" name="description" rows="10">'. $main['description'] .'</textarea>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-12">
                    <input type="hidden" name="op" value="mod_main" />
                    <button class="btn btn-primary col-12" type="submit"><i class="fa fa-check-square fa-lg"></i>&nbsp;'. __d('two_reviews', 'Sauver les modifications') .'</button>
                    </div>
                </div>
            </fieldset>
        </form>
        <hr />';

        $reviews_add = DB::table('reviews_add')->select('id', 'date', 'title', 'text', 'reviewer', 'email', 'score', 'url', 'url_title')->orderBy('id')->get();

        echo '<h3>'. __d('two_reviews', 'Critiques en attente de validation') .'<span class="badge bg-danger float-end">'. count($reviews_add) .'</span></h3>';

        $jsfvc = '';
        $jsfvf = '';

        if ($reviews_add > 0) {
            foreach ($reviews_add as $add) {
                $title = stripslashes($add['title']);
                $text = stripslashes($add['text']);
                
                echo '
        <h4 class="my-3">'. __d('two_reviews', 'Ajouter la critique N° : ') .' '. $add['id'] .'</h4>
        <form id="reviewsaddcr'. $add['id'] .'" action="'. site_url('admin.php') .'" method="post">
        <input type="hidden" name="id" value="'. $add['id'] .'" />
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="reviewdate">'. __d('two_reviews', 'Date') .'</label>
                <div class="col-sm-8">
                    <div class="input-group">
                    <span class="input-group-text"><i class="far fa-calendar-check fa-lg"></i></span>
                    <input class="form-control reviewdate-js" type="text" id="reviewdate" name="date" value="'. $add['date'] .'" maxlength="10" required="required" />
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="title'. $add['id'] .'">'. __d('two_reviews', 'Nom du produit') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="title'. $add['id'] .'" name="title" value="'. $title .'" maxlength="40" required="required" />
                    <span class="help-block text-end" id="countcar_title'. $add['id'] .'"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="text'. $add['id'] .'">'. __d('two_reviews', 'Texte') .'</label>
                <div class="col-sm-8">
                    <textarea class="form-control" id="text'. $add['id'] .' name="text" rows="6">'. $text .'</textarea>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="reviewer'. $add['id'] .'">'. __d('two_reviews', 'Le critique') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="reviewer'. $add['id'] .'" name="reviewer" value="'. $add['reviewer'] .'" maxlength="20" required="required" />
                    <span class="help-block text-end" id="countcar_reviewer'. $add['id'] .'"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="email'. $add['id'] .'">'. __d('two_reviews', 'E-mail') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="email" id="email'. $add['id'] .'" name="email" value="'. $add['email'] .'" maxlength="60" required="required" />
                    <span class="help-block text-end" id="countcar_email'. $add['id'] .'"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="score'. $add['id'] .'">'. __d('two_reviews', 'Note') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="number" id="score'. $add['id'] .'" name="score" value="'. $add['score'] .'"  min="1" max="10" />
                </div>
            </div>';

                if ($add['url'] != '') {
                    echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="url'. $add['id'] .'">'. __d('two_reviews', 'Liens relatifs') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="url" id="url'. $add['id'] .'" name="url" value="'. $add['url'] .'" maxlength="100" />
                    <span class="help-block text-end" id="countcar_url'. $add['id'] .'"></span>
                </div>
            </div>
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4 " for="url_title'. $add['id'] .'">'. __d('two_reviews', 'Titre du lien') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="url_title'. $add['id'] .'" name="url_title" value="'. $add['url_title'] .'" maxlength="50" />
                    <span class="help-block text-end" id="countcar_url_title'. $add['id'] .'"></span>
                </div>
            </div>';
                }

                echo '
            <div class="mb-3 row">
                <label class="col-form-label col-sm-4" for="cover'. $add['id'] .'">'. __d('two_reviews', 'Image de garde') .'</label>
                <div class="col-sm-8">
                    <input class="form-control" type="text" id="cover'. $add['id'] .'" name="cover" maxlength="100" />
                    <span class="help-block">150*150 pixel => images/covers<span class="float-end ms-1" id="countcar_cover'. $add['id'] .'"></span></span>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-sm-8 ms-sm-auto">
                    <input type="hidden" name="op" value="add_review">
                    <button class="btn btn-primary" type="submit">'. __d('two_reviews', 'Ajouter') .'</button>
                    <a href="'. site_url('admin.php?op=deleteNotice&amp;id='. $add['id'] .'&amp;op_back=reviews') .'" class="btn btn-danger" role="button">'. __d('two_reviews', 'Supprimer') .'</a>
                </div>
            </div>
        </form>';

                $jsfvf .= ',"reviewsaddcr'. $add['id'] .'"';
                $jsfvc .= '
                inpandfieldlen("title'. $add['id'] .'",40);
                inpandfieldlen("reviewer'. $add['id'] .'",20);
                inpandfieldlen("email'. $add['id'] .'",60);
                inpandfieldlen("url'. $add['id'] .'",100);
                inpandfieldlen("url_title'. $add['id'] .'",50);
                inpandfieldlen("cover'. $add['id'] .'",100);';
            }

            $arg1 = '
                var formulid = ["reviewspagecfg"'. $jsfvf .'];
                inpandfieldlen("tit_cri",100);'. $jsfvc;


            echo '
        <script type="text/javascript" src="assets/shared/flatpickr/dist/flatpickr.min.js"></script>
        <script type="text/javascript" src="assets/shared/flatpickr/dist/l10n/'. language::language_iso(1, '', '') .'.js"></script>
        <script type="text/javascript">
        //<![CDATA[
            $(document).ready(function() {
                $("<link>").appendTo("head").attr({type: "text/css", rel: "stylesheet",href: "assets/shared/flatpickr/dist/themes/npds.css"});
            })
            flatpickr(".reviewdate-js", {
                altInput: true,
                altFormat: "l j F Y",
                dateFormat:"Y-m-d",
                "locale": "'. language::language_iso(1, '', '') .'",
            });
        //]]>
        </script>';
        } else {
            echo '
            <div class="alert alert-success my-3">'. __d('two_reviews', 'Aucune critique à ajouter') .'</div>';

            $arg1 = '
            var formulid = ["reviewspagecfg"];
            inpandfieldlen("tit_cri",100);';
        }

        echo '
        <hr />
        <p><a href="'. site_url('reviews.php?op=write_review') .'" >'. __d('two_reviews', 'Cliquer ici pour proposer une Critique.') .'</a></p>
        <hr />
        <h3 class="my-3">'. __d('two_reviews', 'Effacer / Modifier une Critique') .'</h3>
        <div class="alert alert-success">'
            . __d('two_reviews', 'Vous pouvez simplement Effacer / Modifier les Critiques en naviguant sur') .' <a href="'. site_url('reviews.php') .'" >'. site_url('reviews.php') .'</a> '. __d('two_reviews', 'en tant qu\'Administrateur.') .'
        </div>';

        css::adminfoot('fv', '', $arg1, '');
    }

    /**
     * [add_review description]
     *
     * @param   int     $id         [$id description]
     * @param   int     $date       [$date description]
     * @param   string  $title      [$title description]
     * @param   string  $text       [$text description]
     * @param   string  $reviewer   [$reviewer description]
     * @param   string  $email      [$email description]
     * @param   int     $score      [$score description]
     * @param   string  $cover      [$cover description]
     * @param   string  $url        [$url description]
     * @param   string  $url_title  [$url_title description]
     *
     * @return  void
     */
    function add_review(int $id, int $date, string $title, string $text, string $reviewer, string $email, int $score, string $cover, string $url, string $url_title): void
    {
        DB::table('reviews')->insert(array(
            'date'          => $date,
            'title'         => stripslashes(str::FixQuotes($title)),
            'text'          => stripslashes(str::FixQuotes($text)),
            'reviewer'      => stripslashes(str::FixQuotes($reviewer)),
            'email'         => stripslashes(str::FixQuotes($email)),
            'score'         => $score,
            'cover'         => $cover,
            'url'           => $url,
            'url_title'     => $url_title,
            'hits'          => 1,
        ));

        DB::table('reviews_add')->where('id', $id)->delete();

        Header('Location: '. site_url('admin.php?op=reviews'));
    }

}