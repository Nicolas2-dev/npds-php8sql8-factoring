<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2023 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\logs\logs;
use npds\support\assets\css;
use npds\system\config\Config;

if (!function_exists('admindroits')) {
    include('die.php');
}

$f_meta_nom = 'MetaTagAdmin';
$f_titre = __d('two_core', 'Administration des MétaTags');

//==> controle droit
admindroits($aid, $f_meta_nom);
//<== controle droit

/**
 * [MetaTagAdmin description]
 *
 * @param   bool   $meta_saved  [$meta_saved description]
 * @param   false               [ description]
 *
 * @return  void
 */
function MetaTagAdmin(bool $meta_saved = false): void 
{
    global $f_meta_nom, $f_titre;

    $tags = GetMetaTags("storage/meta/meta.php");

    include("themes/default/header.php");

    GraphicAdmin(manuel('metatags'));
    adminhead($f_meta_nom, $f_titre);

    $sel = ' selected="selected"';

    echo '
    <hr />';

    if ($meta_saved) {
        echo '
        <div class="alert alert-success">
            '. __d('two_core', 'Vos MétaTags ont été modifiés avec succès !') .'
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }

    echo '
    <form id="metatagsadm" action="'. site_url('admin.php') .'" method="post">
        <div class="form-floating mb-3">
            <input class="form-control" id="newtagauthor" type="text" name="newtag[author]" value="'. $tags['author'] .'" maxlength="100">
            <label for="newtagauthor">'. __d('two_core', 'Auteur(s)') .'</label>
            <span class="help-block">'. __d('two_core', '(Ex. : nom du webmaster)') .'<span class="float-end ms-1" id="countcar_newtagauthor"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" id="newtagowner" type="text" name="newtag[owner]" value="'. $tags['owner'] .'" maxlength="100" />
            <label for="newtagowner">'. __d('two_core', 'Propriétaire') .'</label>
            <span class="help-block">'. __d('two_core', '(Ex. : nom de votre compagnie/service)') .'<span class="float-end ms-1" id="countcar_newtagowner"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" id="newtagreplyto" type="email" name="newtag[reply-to]" value="'. $tags['reply-to'] .'" maxlength="100" />
            <label for="newtagreplyto">'. __d('two_core', 'Adresse e-mail principale') .'</label>
            <span class="help-block">'. __d('two_core', '(Ex. : l\'adresse e-mail du webmaster)') .'<span class="float-end ms-1" id="countcar_newtagreplyto"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" id="newtagdescription" type="text" name="newtag[description]" value="'. $tags['description'] .'" maxlength="200" />
            <label for="newtagdescription">'. __d('two_core', 'Description') .'</label>
            <span class="help-block">'. __d('two_core', '(Brève description des centres d\'intérêt du site. 200 caractères maxi.)') .'<span class="float-end ms-1" id="countcar_newtagdescription"></span></span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" id="newtagkeywords" type="text" name="newtag[keywords]" value="'. $tags['keywords'] .'" maxlength="1000" />
            <label for="newtagkeywords">'. __d('two_core', 'Mot(s) clé(s)') .'</label>
            <span class="help-block">'. __d('two_core', '(Définissez un ou plusieurs mot(s) clé(s). 1000 caractères maxi. Remarques : une lettre accentuée équivaut le plus souvent à 8 caractères. La majorité des moteurs de recherche font la distinction minuscule/majuscule. Séparez vos mots par une virgule)') .'<span class="float-end ms-1" id="countcar_newtagkeywords"></span></span>
        </div>
        <div class="form-floating mb-3">
            <select class="form-select" id="newtagrating" name="newtag[rating]">
                <option value="general"'. (!strcasecmp($tags['rating'], 'general') ? $sel : '') .'>'. __d('two_core', 'Tout public') .'</option>
                <option value="mature"'. (!strcasecmp($tags['rating'], 'mature') ? $sel : '') .'>'. __d('two_core', 'Adulte') .'</option>
                <option value="restricted"'. (!strcasecmp($tags['rating'], 'restricted') ? $sel : '') .'>'. __d('two_core', 'Accés restreint') .'</option>
                <option value="14 years"'. (!strcasecmp($tags['rating'], '14 years') ? $sel : '') .'>'. __d('two_core', '14 ans') .'</option>
            </select>
            <label for="newtagrating">'. __d('two_core', 'Audience') .'</label>
            <span class="help-block">'. __d('two_core', '(Définissez le public intéressé par votre site)') .'</span>
        </div>
        <div class="form-floating mb-3">
            <select class="form-select" id="newtagdistribution" name="newtag[distribution]">
                <option value="global"'. (!strcasecmp($tags['distribution'], 'global') ? $sel : '') .'>'. __d('two_core', 'Large') .'</option>
                <option value="local"'. (!strcasecmp($tags['distribution'], 'local') ? $sel : '') .'>'. __d('two_core', 'Restreinte') .'</option>
            </select>
            <label for="newtagdistribution">'. __d('two_core', 'Distribution') .'</label>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" id="newtagcopyright" type="text" name="newtag[copyright]" value="'. $tags['copyright'] .'" maxlength="100" />
            <label for="newtagcopyright">'. __d('two_core', 'Copyright') .'</label>
            <span class="help-block">'. __d('two_core', '(Informations légales)') .'<span class="float-end ms-1" id="countcar_newtagcopyright"></span></span>
        </div>
        <div class="form-floating mb-3">
            <select class="form-select" id="newtagrobots" name="newtag[robots]">
                <option value="all"'. (!strcasecmp($tags['robots'], 'all') ? $sel : '') .'>'. __d('two_core', 'Tout contenu (page/liens/etc)') .'</option>
                <option value="none"'. (!strcasecmp($tags['robots'], 'none') ? $sel : '') .'>'. __d('two_core', 'Aucune indexation') .'</option>
                <option value="index,nofollow"'. (!strcasecmp($tags['robots'], 'index,nofollow') ? $sel : '') .'>'. __d('two_core', 'Page courante sans liens locaux') .'</option>
                <option value="noindex,follow"'. (!strcasecmp($tags['robots'], 'noindex,follow') ? $sel : '') .'>'. __d('two_core', 'Liens locaux sauf page courante') .'</option>
                <option value="noarchive"'. (!strcasecmp($tags['robots'], 'noarchive') ? $sel : '') .'>'. __d('two_core', 'Pas d\'affichage du cache') .'</option>
                <option value="noodp,noydir"'. (!strcasecmp($tags['robots'], 'noodp,noydir') ? $sel : '') .'>'. __d('two_core', 'Pas d\'utilisation des descriptions ODP ou YDIR') .'</option>
            </select>
            <label for="newtagrobots">'. __d('two_core', 'Robots/Spiders') .'</label>
            <span class="help-block">'. __d('two_core', '(Définissez la méthode d\'analyse que doivent adopter les robots des moteurs de recherche)') .'</span>
        </div>
        <div class="form-floating mb-3">
            <input class="form-control" id="newtagrevisitafter" type="text" name="newtag[revisit-after]" value="'. $tags['revisit-after'] .'" maxlength="30" />
            <label for="newtagrevisitafter">'. __d('two_core', 'Fréquence de visite des Robots/Spiders') .'</label>
            <span class="help-block">'. __d('two_core', '(Ex. : 16 days. Remarque : ne définissez pas de fréquence inférieure à 14 jours !)') .'<span class="float-end ms-1" id="countcar_newtagrevisitafter"></span></span>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-floating mb-3">
                <select class="form-select" id="newtagdoctype" name="newtag[doctype]">
                    <option value="XHTML 1.0 Transitional"'. (!strcasecmp(doctype, 'XHTML 1.0 Transitional') ? $sel : '') .'>XHTML 1.0 '. __d('two_core', 'Transitional') .'</option>
                    <option value="XHTML 1.0 Strict"'. (!strcasecmp(doctype, 'XHTML 1.0 Strict') ? $sel : '') .'>XHTML 1.0 '. __d('two_core', 'Strict') .'</option>
                    <option value="HTML 5.1"'. (!strcasecmp(doctype, 'HTML 5.1') ? $sel : '') .'>HTML 5.1</option>
                </select>
                <label for="newtagdoctype">DOCTYPE</label>
                </div>
            </div>
        </div>
        <input type="hidden" name="op" value="MetaTagSave" />
        <button class="btn btn-primary my-3" type="submit">'. __d('two_core', 'Enregistrer') .'</button>
    </form>';

    $arg1 = '
    var formulid = ["metatagsadm"];
    inpandfieldlen("newtagauthor",100);
    inpandfieldlen("newtagowner",100);
    inpandfieldlen("newtagreplyto",100);
    inpandfieldlen("newtagdescription",200);
    inpandfieldlen("newtagkeywords",1000);
    inpandfieldlen("newtagcopyright",100);
    inpandfieldlen("newtagrevisitafter",30);';

    css::adminfoot('fv', '', $arg1, '');
}

/**
 * [GetMetaTags description]
 *
 * @param   string  $filename  [$filename description]
 *
 * @return  array
 */
function GetMetaTags(string $filename): array
{
    if (file_exists($filename)) {
        $temp = file($filename);

        foreach ($temp as $line) {
            $aline = trim(stripslashes($line));

            if (preg_match("#<meta (name|http-equiv|property)=\"([^\"]*)\" content=\"([^\"]*)\"#i", $aline, $regs)) {
                $regs[2] = strtolower($regs[2]);
                $tags[$regs[2]] = $regs[3];
            } elseif (preg_match("#<meta (charset)=\"([^\"]*)\"#i", $aline, $regs)) {
                $regs[1] = strtolower($regs[1]);
                $tags[$regs[1]] = $regs[2];
            } elseif (preg_match("#<meta (content-type)=\"([^\"]*)\" content=\"([^\"]*)\"#i", $aline, $regs)) {
                $regs[2] = strtolower($regs[2]);
                $tags[$regs[2]] = $regs[3];
            } elseif (preg_match("#<html (lang)=\"([^\"]*)\"#i", $aline, $regs)) {
                $regs[1] = strtolower($regs[1]);
                $tags[$regs[1]] = $regs[2];
            } elseif (preg_match("#<doctype (lang)=\"([^\"]*)\"#i", $aline, $regs)) {
                $regs[1] = strtolower($regs[1]);
                $tags[$regs[1]] = $regs[2];
            }
        }
    }

    return $tags;
}

/**
 * [MetaTagMakeSingleTag description]
 *
 * @param   string  $name     [$name description]
 * @param   string  $content  [$content description]
 * @param   string  $type     [$type description]
 * @param   name              [ description]
 *
 * @return  string
 */
function MetaTagMakeSingleTag(string $name, string $content, string $type = 'name'): string 
{
    if ($content != "humans.txt") {
        if ($content != "") {
            return "\$l_meta.=\"<meta $type=\\\"" . $name . "\\\" content=\\\"" . $content . "\\\" />\\n\";\n";
        } else {
            return "\$l_meta.=\"<meta $type=\\\"" . $name . "\\\" />\\n\";\n";
        }
    } else {
        return "\$l_meta.=\"<link type=\"text/plain\" rel=\"author\" href=\"http://humanstxt.org/humans.txt\" />\";\n";
    }
}

/**
 * [MetaTagSave description]
 *
 * @param   string  $filename  [$filename description]
 * @param   string  $tags      [$tags description]
 *
 * @return  bool
 */
function MetaTagSave(string $filename, string $tags): bool
{
    if (!is_array($tags)) {
        return false;
    }
    
    $nuke_url = Config::get('npds.nuke_url');

    $fh = fopen($filename, "w");
    if ($fh) {
        $content = "<?php\n/* Do not change anything in this file manually. Use the administration interface. */\n";
        $content .= "/* généré le : " . date("d-m-Y H:i:s") . " */\n";
        $content .= "\$meta_doctype = isset(\$meta_doctype) ? \$meta_doctype : '' ;\n";
        $content .= "\$nuke_url = isset(\$nuke_url) ? \$nuke_url : '' ;\n";
        $content .= "\$meta_doctype = isset(\$meta_doctype) ? \$meta_doctype : '' ;\n";
        $content .= "\$meta_op = isset(\$meta_op) ? \$meta_op : '' ;\n";
        $content .= "\$m_description = isset(\$m_description) ? \$m_description : '' ;\n";
        $content .= "\$m_keywords = isset(\$m_keywords) ? \$m_keywords : '' ;\n";
        $content .= "\$lang = language_iso(1, '', 0);\n";
        $content .= "if (\$meta_doctype==\"\")\n";

        if (!empty($tags['doctype'])) {
            if ($tags['doctype'] == "XHTML 1.0 Transitional") {
                $content .= "   \$l_meta=\"<!DOCTYPE html PUBLIC \\\"-//W3C//DTD XHTML 1.0 Transitional//EN\\\" \\\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\\\">\\n<html lang=\\\"\$lang\\\" xml:lang=\\\"\$lang\\\" xmlns=\\\"http://www.w3.org/1999/xhtml\\\">\\n<head>\\n\";\n";
            }
            
            if ($tags['doctype'] == "XHTML 1.0 Strict"){ 
                $content .= "   \$l_meta=\"<!DOCTYPE html PUBLIC \\\"-//W3C//DTD XHTML 1.0 Strict//EN\\\" \\\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\\\">\\n<html lang=\\\"\$lang\\\" xml:lang=\\\"\$lang\\\" xmlns=\\\"http://www.w3.org/1999/xhtml\\\">\\n<head>\\n\";\n";
            }
            
            if ($tags['doctype'] == "HTML 5.1") {
                $content .= "   \$l_meta=\"<!DOCTYPE html>\\n<html lang=\\\"\$lang\\\">\\n<head>\\n\";\n";
            }
        } else {
            $tags['doctype'] = "HTML 5.1";
            $content .= "   \$l_meta=\"<!DOCTYPE html>\\n<html lang=\\\"\$lang\\\">\\n<head>\\n\";\n";
        }

        $content .= "else\n";
        $content .= "   \$l_meta=\$meta_doctype.\"\\n<html lang=\\\"\$lang\\\">\\n<head>\\n\";\n";

        if (!empty($tags['content-type'])) {
            $tags['content-type'] = htmlspecialchars(stripslashes($tags['content-type']), ENT_COMPAT | ENT_HTML401, 'utf-8');
            
            $fp = fopen("config/doctype.php", "w");
            if ($fp) {
                fwrite($fp, "<?php\nif (!defined(\"doctype\"))\n   define ('doctype', \"" . $tags['doctype'] . "\");\n?>");
            }
            fclose($fp);
            $content .= MetaTagMakeSingleTag('content-type', $tags['content-type'], 'http-equiv');
        } else {
            
            $fp = fopen("config/doctype.php", "w");
            if ($fp) {
                fwrite($fp, "<?php\nif (!defined(\"doctype\"))\n   define ('doctype', \"" . $tags['doctype'] . "\");\n?>");
            }
            fclose($fp);

            if ($tags['doctype'] == "XHTML 1.0 Transitional" || $tags['doctype'] == "XHTML 1.0 Strict") {
                $content .= MetaTagMakeSingleTag('content-type', 'text/html; charset=utf-8', 'http-equiv');
            }
        }

        $content .= "\$l_meta.=\"<title>\$Titlesitename</title>\\n\";\n";
        $content .= MetaTagMakeSingleTag('viewport', 'width=device-width, initial-scale=1, shrink-to-fit=no');
        $content .= MetaTagMakeSingleTag('content-script-type', 'text/javascript', 'http-equiv');
        $content .= MetaTagMakeSingleTag('content-style-type', 'text/css', 'http-equiv');
        $content .= MetaTagMakeSingleTag('expires', '0', 'http-equiv');
        $content .= MetaTagMakeSingleTag('pragma', 'no-cache', 'http-equiv');
        $content .= MetaTagMakeSingleTag('cache-control', 'no-cache', 'http-equiv');
        $content .= MetaTagMakeSingleTag('identifier-url', $nuke_url, 'http-equiv');

        if (!empty($tags['author'])) {
            $tags['author'] = htmlspecialchars(stripslashes($tags['author']), ENT_COMPAT | ENT_HTML401, 'utf-8');
            $content .= MetaTagMakeSingleTag('author', $tags['author']);
        }

        if (!empty($tags['owner'])) {
            $tags['owner'] = htmlspecialchars(stripslashes($tags['owner']), ENT_COMPAT | ENT_HTML401, 'utf-8');
            $content .= MetaTagMakeSingleTag('owner', $tags['owner']);
        }

        if (!empty($tags['reply-to'])) {
            $tags['reply-to'] = htmlspecialchars(stripslashes($tags['reply-to']), ENT_COMPAT | ENT_HTML401, 'utf-8');
            $content .= MetaTagMakeSingleTag('reply-to', $tags['reply-to']);
        } else {
            $content .= MetaTagMakeSingleTag('reply-to', Config::get('npds.adminmail'));
        }

        if (!empty($tags['description'])) {
            $tags['description'] = htmlspecialchars(stripslashes($tags['description']), ENT_COMPAT | ENT_HTML401, 'utf-8');
            $content .= "if (\$m_description!=\"\")\n";
            $content .= "   \$l_meta.=\"<meta name=\\\"description\\\" content=\\\"\$m_description\\\" />\\n\";\n";
            $content .= "else\n";
            $content .= "   " . MetaTagMakeSingleTag('description', $tags['description']);
        }

        if (!empty($tags['keywords'])) {
            $tags['keywords'] = htmlspecialchars(stripslashes($tags['keywords']), ENT_COMPAT | ENT_HTML401, 'utf-8');
            $content .= "if (\$m_keywords!=\"\")\n";
            $content .= "   \$l_meta.=\"<meta name=\\\"keywords\\\" content=\\\"\$m_keywords\\\" />\\n\";\n";
            $content .= "else\n";
            $content .= "   " . MetaTagMakeSingleTag('keywords', $tags['keywords']);
        }

        if (!empty($tags['rating'])) {
            $tags['rating'] = htmlspecialchars(stripslashes($tags['rating']), ENT_COMPAT | ENT_HTML401, 'utf-8');
            $content .= MetaTagMakeSingleTag('rating', $tags['rating']);
        }

        if (!empty($tags['distribution'])) {
            $tags['distribution'] = htmlspecialchars(stripslashes($tags['distribution']), ENT_COMPAT | ENT_HTML401, 'utf-8');
            $content .= MetaTagMakeSingleTag('distribution', $tags['distribution']);
        }

        if (!empty($tags['copyright'])) {
            $tags['copyright'] = htmlspecialchars(stripslashes($tags['copyright']), ENT_COMPAT | ENT_HTML401, 'utf-8');
            $content .= MetaTagMakeSingleTag('copyright', $tags['copyright']);
        }

        if (!empty($tags['revisit-after'])) {
            $tags['revisit-after'] = htmlspecialchars(stripslashes($tags['revisit-after']), ENT_COMPAT | ENT_HTML401, 'utf-8');
            $content .= MetaTagMakeSingleTag('revisit-after', $tags['revisit-after']);
        } else {
            $content .= MetaTagMakeSingleTag('revisit-after', "14 days");
        }

        $content .= MetaTagMakeSingleTag('resource-type', "document");
        $content .= MetaTagMakeSingleTag('robots', $tags['robots']);
        $content .= MetaTagMakeSingleTag('generator', Config::get('versioning.Version_ID') . Config::get('versioning.Version_Num') . Config::get('versioning.Version_Sub') );

        //==> OpenGraph Meta Tags
        $content .= MetaTagMakeSingleTag('og:type', 'website', 'property');
        $content .= MetaTagMakeSingleTag('og:url', $nuke_url, 'property');
        $content .= MetaTagMakeSingleTag('og:title', '$Titlesitename', 'property');
        $content .= MetaTagMakeSingleTag('og:description', $tags['description'], 'property');
        $content .= MetaTagMakeSingleTag('og:image', $nuke_url.'/images/ogimg.jpg', 'property');
        $content .= MetaTagMakeSingleTag('twitter:card', 'summary', 'property');

        //<== OpenGraph Meta Tags
        $content .= "if (\$meta_op==\"\") echo \$l_meta; else \$l_meta=str_replace(\"\\n\",\"\",str_replace(\"\\\"\",\"'\",\$l_meta));\n?>";
        fwrite($fh, $content);
        fclose($fh);

        global $aid;
        logs::Ecr_Log('security', "MetaTagsave() by AID : $aid", '');

        return true;
    }
    
    return false;
}

if (!stristr($_SERVER['PHP_SELF'], 'admin.php')) {
    Header('Location: '. site_url('die.php?op=admin'));
}

include("admin/settings_save.php");

settype($meta_saved, 'bool');

switch ($op) {
    case 'MetaTagSave':
        $meta_saved = MetaTagSave("storage/meta/meta.php", $newtag);
        header('location: '. site_url('admin.php?op=MetaTagAdmin&meta_saved='. $meta_saved));
        break;

    case 'MetaTagAdmin':
        MetaTagAdmin($meta_saved);
        break;
}
