<?php

declare(strict_types=1);

namespace npds\modules\blog\support;

use npds\support\routing\url;
use npds\support\pixels\image;
use npds\system\config\Config;
use npds\support\language\language;
use npds\system\support\facades\Request;


class blog
{

    /**
     * [readnews description]
     *
     * @param   string  $blog_dir   [$blog_dir description]
     * @param   string  $op         [$op description]
     * @param   int     $perpage    [$perpage description]
     * @param   bool    $adminblog  [$adminblog description] 
     *
     * @return  string
     */
    public static function readnews(string $blog_dir, string $op, int $perpage, bool $adminblog): string
    {
        $content = '';

        $blog_file = $blog_dir . 'news.txt';
        
        if (!file_exists($blog_file)) {
            $fp = fopen($blog_file, 'w');
            fclose($fp);
        }

        $startpage = Request::query('startpage');

        $xnews = file($blog_file);
        $xnews = array_reverse($xnews);
        $startpage -= 1;
        $ubound = count($xnews);
        
        if ($startpage < 0 || $startpage >= $ubound / $perpage) {
            $startpage = 0;
        }

        settype($contentT, 'string');

        if ($ubound > $perpage) {
            $contentT .= '
            <nav>
                <ul class="pagination pagination-sm d-flex flex-wrap my-2">';
            
            for ($j = 1; $j <= ceil($ubound / $perpage); $j++) {
                $contentT .= ($j == $startpage + 1) ?
                    '
                 <li class=" page-item active"><a class="page-link" href="#">' . $j . '</a></li>' :
                    '
                 <li class="page-item"><a href="'. site_url('minisite.php?op=' . $op . '&amp;startpage=' . $j) .'" class="page-link blog_lien">' . $j . '</a></li>';
            }

            $contentT .= '
                </ul>
            </nav>';
        }

        if ($adminblog) {

            if (!$action = Request::query('action') or !$action = Request::input('action'))  {
                $action  = '';
            }

            // Suppression
            if (substr($action, 0, 1) == 'D') {
                static::blog_users_delete($op, $blog_file, $action, $xnews);
            }

            // Ajouter - Ecriture
            if (substr($action, 0, 3) == 'AOK') {
                static::blog_user_add_submit($op, $blog_file);
            }

            // Ajouter
            if (substr($action, 0, 1) == 'A') {
                $content .= static::blog_user_add($op);
            }

            // Modifier - Ecriture
            if (substr($action, 0, 3) == 'MOK') {
                static::blog_users_edite_submit($op, $blog_file, $xnews);
            }

            // Modifier
            if (substr($action, 0, 1) == 'M') {
                $content .= static::blog_users_edite($op, $action, $xnews, $xnews);
            }
        }
    
        // Output
        for ($i = $startpage * $perpage; $i < $startpage * $perpage + $perpage && $i < $ubound; $i++) {
            $crtsplit = explode('!;!', $xnews[$i]);

            $actionM = '<a class="" href="'. site_url('minisite.php?op=' . $op . '&amp;action=M' . $i) .'" title="' . translate("Modifier") . '" data-bs-toggle="tooltip" ><i class="fa fa-edit fa-lg me-1"></i></a>';
            $actionD = '<a class="" href="'. site_url('minisite.php?op=' . $op . '&amp;action=D' . $i) .'" title="' . translate("Effacer") . '" data-bs-toggle="tooltip"><i class="fas fa-trash fa-lg text-danger"></i></a>';
            
            $content .= '
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="card-title">' . language::aff_langue($crtsplit[1]) . '</h2>
                    <h6 class="card-subtitle text-muted">' . translate("Posté le ") . ' ' . $crtsplit[0] . '</h6>
                </div>
                <div class=" card-body">' . static::convert_ressources($op, $crtsplit[2]) . '</div>';
            
             if ($adminblog) {
                $content .= '
                <div class="card-footer">
                    ' . $actionM . '&nbsp;' . $actionD . '
                </div>';
            }

            $content .= '
            </div>';
        }

        if (substr($contentT, 13) != '') {
            $content .= substr($contentT, 13);
        };

        $content .= "\n";

        return $content;
    }

    /**
     * [blog_user_add description]
     *
     * @param   string  $op  [$op description]
     *
     * @return  string
     */
    private static function blog_user_add(string $op): string 
    {
        return '
        <form name="adminForm" method="post" action="'. site_url('minisite.php') .'">
            <div class="mb-3 row">
            <label class="form-label" for="title">' . translate("Titre") . '</label>
            <div class="col-sm-12">
                <input class="form-control" type="text" name="title" />
            </div>
            </div>
            <div class="mb-3 row">
            <label class="form-label" for="story">' . translate("Texte complet") . '</label>
            <div class="col-sm-12">
                <textarea class="tin form-control" name="story" rows="25"></textarea>
                    &nbsp;!blog_editeur!
            </div>
            </div>
            <div class="mb-3 row">
            <div class="col-sm-12">
                <input type="hidden" name="op" value="'. $op .'" />
                <input type="hidden" name="action" value="AOK" />
                <input class="btn btn-primary" type="submit" name="submit" value="' . translate("Valider") . '" />
            </div>
            </div>
        </form>';
    }

    /**
     * [blog_user_add_submit description]
     *
     * @param   string  $op         [$op description]
     * @param   string  $blog_file  [$blog_file description]
     *
     * @return  void
     */
    private static function blog_user_add_submit(string $op, string $blog_file): void 
    {
        $title = Request::input('title'); 
        $story = Request::input('story'); 

        @copy($blog_file, $blog_file . '.bak');

        $fp = fopen($blog_file, "a");
        if (!Config::get('npds.tiny_mce')) {
            $formatted = str_replace("\r\n", '<br />', $story);
            $formatted = str_replace('<img', '<img class="img-fluid" ', $story);
            $formatted = str_replace("\n", '<br />', $formatted);
        } else {
            $formatted = str_replace("\r\n", '', $story);
            $formatted = str_replace("\n", '', $formatted);
        }

        $newsto = date("d m Y") . '!;!' . $title . '!;!' . $formatted;
        $newsto = image::dataimagetofileurl($newsto, 'storage/users_private/' . $op . '/mns');

        fwrite($fp, StripSlashes($newsto) . "\n");
        fclose($fp);

        url::redirect_url('minisite.php?op='. $op);
    }

    /**
     * [blog_users_edite description]
     *
     * @param   string  $op      [$op description]
     * @param   string  $action  [$action description]
     * @param   array   $xnews   [$xnews description]
     *
     * @return  string
     */
    private static function blog_users_edite(string $op, string $action, array $xnews): string
    {
        $index = substr($action, 1);
        $crtsplit = explode("!;!", $xnews[$index]);
        $videoprovider = array('yt', 'vm', 'dm');

        foreach ($videoprovider as $v) {
            $crtsplit[2] = preg_replace('#(' . $v . ')_(video)\((.*[^\)])\)#m', '[\2_\1]\3[/\2_\1]', $crtsplit[2]);
        }

        return '
        <form name="adminForm" method="post" action="'. site_url('minisite.php') .'">
            <div class="mb-3">
            <label class="form-label" for="title">' . translate("Titre") . '</label>
            <input class="form-control" type="text" name="title" value="' . $crtsplit[1] . '" />
            </div>
            <div class="mb-3">
            <label class="form-label" for="story" >' . translate("Texte complet") . '</label>
            <textarea class="tin form-control" name="story" rows="25">' . str_replace("\n", "", $crtsplit[2]) . '</textarea>
                &nbsp;!blog_editeur!
            </div>
            <div class="mb-3">
            <input type="hidden" name="op" value="'. $op .'" />
            <input type="hidden" name="action" value="MOK" />
            <input type="hidden" name="index" value="'. $index .'" />
            <input class="btn btn-primary" type="submit" name="submit" value="' . translate("Valider") . '" />
            </div>
        </form>
        #v_yt#';
    }

    /**
     * [blog_users_edite_submit description]
     *
     * @param   string  $op         [$op description]
     * @param   string  $blog_file  [$blog_file description]
     *
     * @return  void
     */
    private static function blog_users_edite_submit(string $op, string $blog_file, array $xnews): void
    {
        $title = Request::input('title'); 
        $story = Request::input('story'); 
        $index = Request::input('index');

        @copy($blog_file, $blog_file . ".bak");
        
        if (!Config::get('npds.tiny_mce')) {
            $formatted = str_replace("\r\n", '<br />', $story);
            $formatted = str_replace('<img', '<img class="img-fluid" ', $story); // a revoir ??
            $formatted = str_replace("\n", '<br />', $formatted);
        } else {
            $formatted = str_replace("\r\n", '', $story);
            $formatted = str_replace("\n", '', $formatted);
        }

        $newsto = date("d m Y") . '!;!' . $title . '!;!' . $formatted;
        $newsto = image::dataimagetofileurl($newsto, 'storage/users_private/' . $op . '/mns');

        $xnews[$index] = StripSlashes($newsto) . "\n";        
        $xnews = array_reverse($xnews);

        $fp = fopen($blog_file, "w");
        for ($j = 0; $j < count($xnews); $j++) {
            fwrite($fp, $xnews[$j]);
        }
        fclose($fp);

       url::redirect_url('minisite.php?op='. $op);
    }

    /**
     * [blog_users_delete description]
     *
     * @param   string  $op         [$op description]
     * @param   string  $blog_file  [$blog_file description]
     * @param   string  $action     [$action description]
     * @param   array   $xnews      [$xnews description]
     *
     * @return  void
     */
    private static function blog_users_delete(string $op, string $blog_file, string $action, array $xnews): void
    {
        @copy($blog_file, $blog_file . '.bak');

        $index = substr($action, 1);

        unset($xnews[$index]);

        $xnews = array_reverse($xnews);

        $fp = fopen($blog_file, "w");
        for ($j = 0; $j < count($xnews); $j++) {
            fwrite($fp, $xnews[$j]);
        }
        fclose($fp);

        url::redirect_url('minisite.php?op='. $op);
    }

    /**
     * [convert_ressources description]
     *
     * @param   string  $op        [$op description]
     * @param   string  $Xcontent  [$Xcontent description]
     *
     * @return  string
     */
    public static function convert_ressources(string $op, string $Xcontent): string 
    {
        for ($i = 0; $i < strlen($Xcontent); $i++) {
            if (strtoupper(substr($Xcontent, $i, 4)) == "src=") {
                if ((strtoupper(substr($Xcontent, $i + 4, 3)) != "HTT") 
                and (strtoupper(substr($Xcontent, $i + 4, 4)) != "\"HTT")) {
                    $Xcontent = substr_replace($Xcontent, 'src='. site_url('getfile.php?att_id='. $op .'&amp;apli=minisite&amp;att_type=&amp;att_size=&amp;att_name='), $i, 4);
                }
                $i = $i + 4;
            }
        }

        return language::aff_langue($Xcontent);
    }

}
