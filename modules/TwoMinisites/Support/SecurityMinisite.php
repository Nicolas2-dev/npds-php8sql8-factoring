<?php

declare(strict_types=1);

namespace Modules\TwoMiniste\Support;

use Two\Support\Facades\Config;
use Two\Support\Facades\Request;
use Modules\TwoCore\Support\Security;
use Shared\TinyMce\Support\Facades\TinyMce as Editeur;


class SecurityMinisite extends Security
{

    /**
     * [MNSremoveHack description]
     *
     * @param   [type]  $Xstring  [$Xstring description]
     *
     * @return  [type]            [return description]
     */
    public static function removeMinisite($Xstring)
    {
        static $blog_editor;

        if (Config::get('two_core::config.tiny_mce')) {
            if (!$blog_editor) {
                
                // ont initialise le theme full pour l'editeur tinymce
                Editeur::setTinyMceTheme('full');

                $blog_editor = Editeur::aff_editeur('tiny_mce', 'begin') . Editeur::aff_editeur('story', 'false') . Editeur::aff_editeur('tiny_mce', 'end');
            }
        }

        $npds_forbidden_words = array();
        $npds_forbidden_words = static::prepareForbidden($npds_forbidden_words);
        $npds_forbidden_words = array_merge($npds_forbidden_words, static::minisiteSanitize(Request::input('op'), $blog_editor));

        $Xstring = preg_replace(array_keys($npds_forbidden_words), array_values($npds_forbidden_words), $Xstring);

        return $Xstring;
    }

    /**
     * [minisiteSanitize description]
     *
     * @return  [type]  [return description]
     */
    public function minisiteSanitize($op, $blog_editor)
    {
        return array(
            "'from:'i"                      => "!from:!",
            "'subject:'i"                   => "!subject:!",
            "'bcc:'i"                       => "!bcc:!",
            "'mime-version:'i"              => "!mime-version:!",
            "'base64'i"                     => "base_64",
            "'content-type:'i"              => "!content-type:!",
            "'content-transfer-encoding:'i" => "!content-transfer-encoding:!",
            "'content-disposition:'i"       => "!content-disposition:!",
            "'content-location:'i"          => "!content-location:!",
            "'include'i"                    => "!include!",
            "'<script'i"                    => "&lt;script",
            "'</script'i"                   => "&lt;/script",
            "'javascript'i"                 => "!javascript!",
            "'embed'i"                      => "!embed!",
            "'iframe'i"                     => "!iframe!",
            "'refresh'i"                    => "!refresh!",
            "'document\.cookie'i"           => "!document.cookie!",
            "'onload'i"                     => "!onload!",
            "'onstart'i"                    => "!onstart!",
            "'onerror'i"                    => "!onerror!",
            "'onkey'i"                      => "!onkey!",
            "'onmouse'i"                    => "!onmouse!",
            "'onclick'i"                    => "!onclick!",
            "'ondblclick'i"                 => "!ondblclick!",
            "'onhelp'i"                     => "!onhelp!",
            "'onmousedown'i"                => "!onmousedown!",
            "'onmousemove'i"                => "!onmousemove!",
            "'onmouseout'i"                 => "!onmouseout!",
            "'onmouseover'i"                => "!onmouseover!",
            "'onmouseup'i"                  => "!onmouseup!",
            "'onblur'i"                     => "!onblur!",
            "'onafterupdate'i"              => "!onafterupdate!",
            "'onbeforeupdate'i"             => "!onbeforeupdate!",
            "'onkeydown'i"                  => "!onkeydown!",
            "'onkeypress'i"                 => "!onkeypress!",
            "'onkeyup'i"                    => "!onkeyup!",
            "'onfocus'i"                    => "!onfocus!",
            "'onunload'i"                   => "!onunload!",
            "'jscript'i"                    => "!jscript!",
            "'vbscript'i"                   => "!vbscript!",
            "'pearlscript'i"                => "!pearlscript!",
            "'&#(8216|x2018);'i"            => chr(39),
            "'&#(8217|x2019);'i"            => chr(39),
            "'&#39;'i"                      => '\\\'',
            "'&#(8220|x201C);'i"            => chr(34),
            "'&#(8221|x201D);'i"            => chr(34),
            "'&#160;'i"                     => '&nbsp;',
            "'.htaccess'i"                  => "",
            "'!blog_editeur!'i"             => $blog_editor,
            "'!l_blog_ajouterOK!'i"         => '<a class="list-group-item list-group-item-action" href="'. site_url('minisite.php?op='. $op .'&amp;action=A') .'"><i class="fas fa-pencil-alt fa-lg me-2"></i>'. translate("Ajouter un article") .'</a>',
            "'\<\?php'i"                    => "&lt;?php",
            "'\<\?'i"                       => "&lt;?",
            "'\?\>'i"                       => "?&gt;",
            "'\<\%'i"                       => "&lt;%",
            "'\%\>'i"                       => "%&gt;"
        );
    }    

}