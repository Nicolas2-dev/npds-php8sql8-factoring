<?php

declare(strict_types=1);

use Two\Support\Facades\DB;
use Two\Support\Facades\Cache;
use Two\Support\Facades\Crypt;
use Two\Support\Facades\Config;
use Two\Support\Facades\Request;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Language;
use Modules\TwoAuthors\Support\Facades\Author;

// request

if (! function_exists('getIp'))
{
    /**
     * [getIp description]
     *
     * @return  [type]  [return description]
     */
    function getIp()
    {
        // proxy
        if (Request::server('HTTP_X_FORWARDED_FOR') && Request::server('HTTP_X_FORWARD')) {
            $ip = Request::getClientIp();
        // no proxy
        } else {
            $ip = Request::ip();
        }  
        
        return $ip;
    }
}

// cache : Note a revoir car suppression de supercache

if (! function_exists('SC_infos'))
{
    /**
     * [SC_infos description]
     *
     * @return  [type]  [return description]
     */
    function SC_infos()
    {
        return 'super Cache Aftif'; //return Cache::SC_infos();
    }
}

// url 

if (! function_exists('redirect_url'))
{
    /**
     * Permet une redirection javascript en lieu et place de header("location: ...");
     *
     * @param   string  $urlx  [$urlx description]
     *
     * @return  void
     */
    function redirect_url(string $urlx): void
    {
        echo "<script type=\"text/javascript\">\n";
        echo "//<![CDATA[\n";
        echo "document.location.href='". site_url($urlx) ."';\n";
        echo "//]]>\n";
        echo "</script>";
    }
}

// log

if (! function_exists('Ecr_Log'))
{
    /**
     * Pour écrire dans un log (security.log par exemple)
     *
     * @param   string  $fic_log  [$fic_log description]
     * @param   string  $req_log  [$req_log description]
     * @param   string  $mot_log  [$mot_log description]
     *
     * @return  void              [return description]
     */
    function Ecr_Log(string $fic_log, string $req_log, string $mot_log): void
    {
        // $Fic_log= the file name :
        //  => "security" for security maters
        //  => ""
        // $req_log= a phrase describe the infos
        //
        // $mot_log= if "" the Ip is recorded, else extend status infos
        $logfile = "storage/logs/$fic_log.log";
        
        $fp = fopen($logfile, 'a');
        flock($fp, 2);
        fseek($fp, filesize($logfile));
        
        if ($mot_log == "") {
            $mot_log = "IP=>" . getip();
        }

        $ibid = sprintf("%-10s %-60s %-10s\r\n", date("m/d/Y H:i:s", time()), basename($_SERVER['PHP_SELF']) ."=>". strip_tags(urldecode($req_log)), strip_tags(urldecode($mot_log)));
        
        fwrite($fp, $ibid);
        flock($fp, 3);
        fclose($fp);
    }
}

// manuel admin

if (! function_exists('manuel'))
{
    /**
     * [manuel description]
     *
     * @param   [type]  $manuel  [$manuel description]
     *
     * @return  [type]
     */
    function manuel($manuel)  
    {
        return 'modules/manuels/view/'. Config::get('two_core::config.language') .'/'. $manuel .'.html';
    }
}

// acces refuser admin

if (! function_exists('access_denied'))
{
    /**
     * [access_denied description]
     *
     * @return  [type]  [return description]
     */
    function access_denied()
    {
        include("admin/die.php");
    }
}

// admin alert

if (! function_exists('Admin_alert'))
{
    function Admin_alert($motif)
    {
        $admin = Author::getAdmin();

        setcookie('admin', '', 0);
        unset($admin);

        Ecr_Log('security', 'auth.inc.php/Admin_alert : ' . $motif, '');
        
        if (file_exists("storage/meta/meta.php")) {
            $Titlesitename = 'NPDS';
            include("storage/meta/meta.php");
        }

        echo '
            </head>
            <body>
                <br /><br /><br />
                <p style="font-size: 24px; font-family: Tahoma, Arial; color: red; text-align:center;"><strong>.: ' . __d('two_core', 'Votre adresse Ip est enregistrée') . ' :.</strong></p>
            </body>
        </html>';
        die();
    }
}

// get os

if (! function_exists('get_os'))
{
    /**
     * retourne true si l'OS de la station cliente est Windows sinon false
     *
     * @return  [type]  [return description]
     */
    function get_os()
    {
        $client = getenv("HTTP_USER_AGENT");
        
        if (preg_match('#(\(|; )(Win)#', $client, $regs)) {
            if ($regs[2] == "Win") {
                $MSos = true;
            } else {
                $MSos = false;
            }
        } else {
            $MSos = false;
        }

        return $MSos;
    }
}

// java popup

if (! function_exists('JavaPopUp'))
{
    /**
     * 
     *
     * @param   string  $F  [$F description]
     * @param   string  $T  [$T description]
     * @param   int     $W  [$W description]
     * @param   int     $H  [$H description]
     *
     * @return  string      [return description]
     */
    function JavaPopUp(string $F, string $T, int $W, int $H): string 
    {
        // 01.feb.2002 by GaWax
        if ($T == "") {
            $T = "@ ".time()." ";
        }

        $PopUp = "'$F', '$T', 'menubar=no,location=no,directories=no,status=no,copyhistory=no,height=$H,width=$W,toolbar=no,scrollbars=yes,resizable=yes'";

        return $PopUp;
    }

}

// css

// Deprecated function
// if (! function_exists('import_css_javascript'))
// {
//     /**
//      * recherche et affiche la CSS (site, langue courante ou par défaut)
//      * Charge la CSS complémentaire
//      * le HTML ne contient que de simple quote pour être compatible avec javascript
//      *
//      * @param   string  $tmp_theme      [$tmp_theme description]
//      * @param   string  $language       [$language description]
//      * @param   string  $fw_css         [$fw_css description]
//      * @param   string  $css_pages_ref  [$css_pages_ref description]
//      * @param   string|array  $css            [$css description]
//      *
//      * @return  string
//      */
//     function import_css_javascript(string $tmp_theme, string $language, string $fw_css, string $css_pages_ref = '', string|array $css = ''): string
//     {
//         $tmp = '';

//         // CSS framework
//         if (file_exists("themes/_skins/$fw_css/bootstrap.min.css")) {
//             $tmp .= "<link href='themes/_skins/$fw_css/bootstrap.min.css' rel='stylesheet' type='text/css' media='all' />\n";
//         }
        
//         // CSS standard 
//         if (file_exists("themes/$tmp_theme/assets/css/$language-style.css")) {
//             $tmp .= "<link href='themes/$tmp_theme/assets/css/$language-style.css' title='default' rel='stylesheet' type='text/css' media='all' />\n";
            
//             if (file_exists("themes/$tmp_theme/assets/css/$language-style-AA.css")) {
//                 $tmp .= "<link href='themes/$tmp_theme/assets/css/$language-style-AA.css' title='alternate stylesheet' rel='alternate stylesheet' type='text/css' media='all' />\n";
//             }
            
//             if (file_exists("themes/$tmp_theme/assets/css/$language-print.css")) {
//                 $tmp .= "<link href='themes/$tmp_theme/assets/css/$language-print.css' rel='stylesheet' type='text/css' media='print' />\n";
//             }

//         } else if (file_exists("themes/$tmp_theme/assets/css/style.css")) {
//             $tmp .= "<link href='themes/$tmp_theme/assets/css/style.css' title='default' rel='stylesheet' type='text/css' media='all' />\n";
            
//             if (file_exists("themes/$tmp_theme/assets/css/style-AA.css")) {
//                 $tmp .= "<link href='themes/$tmp_theme/assets/css/style-AA.css' title='alternate stylesheet' rel='alternate stylesheet' type='text/css' media='all' />\n";
//             }
            
//             if (file_exists("themes/$tmp_theme/assets/css/print.css")) {
//                 $tmp .= "<link href='themes/$tmp_theme/assets/css/print.css' rel='stylesheet' type='text/css' media='print' />\n";
//             }
//         } else {
//             $tmp .= "<link href='themes/default/assets/css/style.css' title='default' rel='stylesheet' type='text/css' media='all' />\n";
//         }

//         // Chargeur CSS spécifique
//         if ($css_pages_ref) {

//             include("routes/pages.php");

//             if (is_array($PAGES[$css_pages_ref]['css'])) {
//                 foreach ($PAGES[$css_pages_ref]['css'] as $tab_css) {
//                     $admtmp = '';
//                     $op = substr($tab_css, -1);

//                     if ($op == '+' or $op == '-') {
//                         $tab_css = substr($tab_css, 0, -1);
//                     }

//                     if (stristr($tab_css, 'http://') || stristr($tab_css, 'https://')) {
//                         $admtmp = "<link href='$tab_css' rel='stylesheet' type='text/css' media='all' />\n";
//                     } else {
//                         if (file_exists("themes/$tmp_theme/assets/css/$tab_css") and ($tab_css != '')) {
//                             $admtmp = "<link href='themes/$tmp_theme/assets/css/$tab_css' rel='stylesheet' type='text/css' media='all' />\n";
//                         } elseif (file_exists("$tab_css") and ($tab_css != '')) {
//                             $admtmp = "<link href='$tab_css' rel='stylesheet' type='text/css' media='all' />\n";
//                         }
//                     }

//                     if ($op == '-') {
//                         $tmp = $admtmp;
//                     } else {
//                         $tmp .= $admtmp;
//                     }
//                 }
//             } else {
//                 $oups = $PAGES[$css_pages_ref]['css'];

//                 settype($oups, 'string');

//                 $op = substr($oups, -1);
//                 $css = substr($oups, 0, -1);

//                 if (($css != '') and (file_exists("themes/$tmp_theme/assets/css/$css"))) {
//                     if ($op == '-') {
//                         $tmp = "<link href='themes/$tmp_theme/assets/css/$css' rel='stylesheet' type='text/css' media='all' />\n";
//                     } else {
//                         $tmp .= "<link href='themes/$tmp_theme/assets/css/$css' rel='stylesheet' type='text/css' media='all' />\n";
//                     }
//                 }
//             }
//         }

//         return $tmp;
//     }
// }

// Deprecated function
// if (! function_exists('import_css'))
// {
//     /**
//      * Fonctionnement identique à import_css_javascript sauf que le code HTML en retour ne contient que de double quote
//      *
//      * @param   string  $tmp_theme      [$tmp_theme description]
//      * @param   string  $language       [$language description]
//      * @param   string  $fw_css         [$fw_css description]
//      * @param   string  $css_pages_ref  [$css_pages_ref description]
//      * @param   string|array  $css            [$css description]
//      *
//      * @return  string
//      */
//     function import_css(string $tmp_theme, string $language, string $fw_css, string $css_pages_ref, string|array $css): string
//     {
//         return (str_replace("'", "\"", static::import_css_javascript($tmp_theme, $language, $fw_css, $css_pages_ref, $css)));
//     }
// }

if (! function_exists('adminfoot'))
{
    /**
     * fin d'affichage avec form validateur ou pas, ses parametres (js), fermeture div admin et inclusion footer.php  
     * $fv => fv : inclusion du validateur de form, 
     * $fv_parametres => éléments de l'objet fields differents input (objet js ex :   xxx: {},...) si !###! est trouvé 
     * dans la variable la partie du code suivant sera inclu à la fin de la fonction d'initialisation, 
     * $arg1 => js pur au début du script js,
     * $foo =='' ==> </div> et inclusion footer.php $foo =='foo' ==> inclusion footer.php
     *
     * @param   string  $fv             [$fv description]
     * @param   string  $fv_parametres  [$fv_parametres description]
     * @param   string  $arg1           [$arg1 description]
     * @param   string  $foo            [$foo description]
     *
     * @return  void
     */
    function adminfoot(string $fv, string $fv_parametres, string $arg1, string $foo): string
    {
        if ($fv == 'fv') {
            if ($fv_parametres != '') {
                $fv_parametres = explode('!###!', $fv_parametres);
            }

            Theme::SetFormValidation_Js(true);

            $foot ='
    <script type="text/javascript">
    //<![CDATA[
    '. $arg1 .'
    var diff;
    document.addEventListener("DOMContentLoaded", function(e) {
       // validateur pour mots de passe
       const strongPassword = function() {
         return {
             validate: function(input) {
                let score=0;
                const value = input.value;
                if (value === "") {
                   return {
                      valid: true,
                      meta:{score:null},
                   };
                }
                if (value === value.toLowerCase()) {
                   return {
                      valid: false,
                      message: "'. __d('two_core', 'Le mot de passe doit contenir au moins un caractère en majuscule.') .'",
                      meta:{score: score-1},
                    };
                }
                if (value === value.toUpperCase()) {
                   return {
                      valid: false,
                      message: "'. __d('two_core', 'Le mot de passe doit contenir au moins un caractère en minuscule.') .'",
                      meta:{score: score-2},
                   };
                }
                if (value.search(/[0-9]/) < 0) {
                   return {
                      valid: false,
                      message: "'. __d('two_core', 'Le mot de passe doit contenir au moins un chiffre.') .'",
                      meta:{score: score-3},
                   };
                }
                if (value.search(/[@\+\-!#$%&^~*_]/) < 0) {
                   return {
                      valid: false,
                      message: "'. __d('two_core', 'Le mot de passe doit contenir au moins un caractère non alphanumérique.') .'",
                      meta:{score: score-4},
                   };
                }
                if (value.length < 8) {
                   return {
                      valid: false,
                      message: "'. __d('two_core', 'Le mot de passe doit contenir') .' '. Config::get('two_core::config.minpass') .' '. __d('two_core', 'caractères au minimum') .'",
                      meta:{score: score-5},
                   };
                }
 
                score += ((value.length >= '. Config::get('two_core::config.minpass') .') ? 1 : -1);
                if (/[A-Z]/.test(value)) score += 1;
                if (/[a-z]/.test(value)) score += 1; 
                if (/[0-9]/.test(value)) score += 1;
                if (/[@\+\-!#$%&^~*_]/.test(value)) score += 1; 
                return {
                   valid: true,
                   meta:{score: score},
                };
             },
          };
       };
       FormValidation.validators.checkPassword = strongPassword;
       formulid.forEach(function(item, index, array) {
          const fvitem = FormValidation.formValidation(
             document.getElementById(item),{
                locale: "'. Language::language_iso(1, "_", 1) .'",
                localization: FormValidation.locales.'. Language::language_iso(1, "_", 1) .',
                fields: {';
            
            if ($fv_parametres != '') {
                $foot .= $fv_parametres[0];
            }
            
            $foot .= '
                },
                plugins: {
                   declarative: new FormValidation.plugins.Declarative({
                      html5Input: true,
                   }),
                   trigger: new FormValidation.plugins.Trigger(),
                   submitButton: new FormValidation.plugins.SubmitButton(),
                   defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                   bootstrap5: new FormValidation.plugins.Bootstrap5({rowSelector: ".mb-3"}),
                   icon: new FormValidation.plugins.Icon({
                      valid: "fa fa-check",
                      invalid: "fa fa-times",
                      validating: "fa fa-sync",
                      onPlaced: function(e) {
                         e.iconElement.addEventListener("click", function() {
                            fvitem.resetField(e.field);
                         });
                      },
                   }),
                },
             })
             .on("core.validator.validated", function(e) {
                if ((e.field === "add_pwd" || e.field === "chng_pwd" || e.field === "pass" || e.field === "add_pass" || e.field === "code" || e.field === "passwd") && e.validator === "checkPassword") {
                   var score = e.result.meta.score;
                   const barre = document.querySelector("#passwordMeter_cont");
                   const width = (score < 0) ? score * -18 + "%" : "100%";
                   barre.style.width = width;
                   barre.classList.add("progress-bar","progress-bar-striped","progress-bar-animated","bg-success");
                   barre.setAttribute("aria-valuenow", width);
                   if (score === null) {
                      barre.style.width = "100%";
                      barre.setAttribute("aria-valuenow", "100%");
                      barre.classList.replace("bg-success","bg-danger");
                   } else 
                      barre.classList.replace("bg-danger","bg-success");
                }
                if (e.field === "B1" && e.validator === "promise") {
                   if (e.result.valid && e.result.meta && e.result.meta.source) {
                       $("#ava_perso").removeClass("border-danger").addClass("border-success")
                   } else if (!e.result.valid) {
                      $("#ava_perso").addClass("border-danger")
                   }
                }
             });';

            if ($fv_parametres != '')
                if (array_key_exists(1, $fv_parametres)) {
                    $foot .= '
                '. $fv_parametres[1];
            }
            
            $foot .= '
          })
       });
    //]]>
    </script>';
        }

        switch ($foo) {
            case '':
                $foot .= '</div>';
                break;
        }

        return $foot;
    }
}

if (! function_exists('auto_complete'))
{
    /**
     * fabrique un array js à partir de la requete sql et implente un auto complete pour l'input
     * (dependence : jquery.min.js ,jquery-ui.js) $nom_array_js=> nom du tableau javascript; 
     * $nom_champ=>nom de champ bd; $nom_tabl=>nom de table bd,$id_inpu=> id de l'input,
     * $temps_cache=>temps de cache de la requête. Si $id_inpu n'est pas défini retourne un array js.
     *
     * @param   string  $nom_array_js  [$nom_array_js description]
     * @param   string  $nom_champ     [$nom_champ description]
     * @param   string  $nom_tabl      [$nom_tabl description]
     * @param   string  $id_inpu       [$id_inpu description]
     * @param   int  $temps_cache   [$temps_cache description]
     *
     * @return  string                 [return description]
     */
    function auto_complete(string $nom_array_js, string $nom_champ, string $nom_tabl, string $id_inpu, int $temps_cache): string 
    {
        $list_json = '';
        $list_json .= 'var ' . $nom_array_js . ' = [';

        $res_data = Cache::remember($nom_tabl.$nom_champ, $temps_cache, function () use ($nom_tabl, $nom_champ) {
            return DB::table($nom_tabl)->select($nom_champ)->get();
        });

        foreach ($res_data as $ar_data) 
        {
            if ($id_inpu == '') {
                $list_json .= '"' . base64_encode($ar_data[$nom_champ]) . '",';
            } else {
                $list_json .= '"'. $ar_data[$nom_champ] . '",';
            }
        }

        $list_json = rtrim($list_json, ',');
        $list_json .= '];';
        $scri_js = '';
        
        if ($id_inpu == '') {
            $scri_js .= $list_json;
        } else {
            $scri_js .= '
        <script type="text/javascript">
        //<![CDATA[
        $(function() {
        ' . $list_json;
            if ($id_inpu != '')
                $scri_js .= '
        $( "#' . $id_inpu . '" ).autocomplete({
            source: ' . $nom_array_js . '
            });';
            $scri_js .= '
        });
        //]]>
        </script>';
        }

        return $scri_js;
    }
}

if (! function_exists('auto_complete_multi'))
{
    /**
     * fabrique un pseudo array json à partir de la requete sql et implente un auto complete pour le champ input
     * (dependence : jquery-2.1.3.min.js ,jquery-ui.js)
     *
     * @param   string  $nom_array_js  [$nom_array_js description]
     * @param   string  $nom_champ     [$nom_champ description]
     * @param   string  $nom_tabl      [$nom_tabl description]
     * @param   string  $id_inpu       [$id_inpu description]
     * @param   string  $req           [$req description]
     *
     * @return  string                 [return description]
     */
    function auto_complete_multi(string $nom_array_js, string $nom_champ, string $nom_tabl, string $id_inpu, string $req): string
    {
        $list_json = '';
        $list_json .= $nom_array_js . ' = [';

        foreach (DB::table($nom_tabl)->select($nom_champ)->get() as $champ) 
        {
            $list_json .= '\'' . $champ[$nom_champ] . '\',';
        }

        $list_json = rtrim($list_json, ',');
        $list_json .= '];';
        $scri_js = '';
        $scri_js .= '
        <script type="text/javascript">
        //<![CDATA[
        var ' . $nom_array_js . ';
        $(function() {
        ' . $list_json . '
        function split( val ) {
        return val.split( /,\s*/ );
        }
        function extractLast( term ) {
        return split( term ).pop();
        }
        $( "#' . $id_inpu . '" )
        // dont navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).autocomplete( "instance" ).menu.active ) {
            event.preventDefault();
            }
        })
        .autocomplete({
            minLength: 0,
            source: function( request, response ) {
            response( $.ui.autocomplete.filter(
                ' . $nom_array_js . ', extractLast( request.term ) ) );
            },
            focus: function() {
            return false;
            },
            select: function( event, ui ) {
            var terms = split( this.value );
            terms.pop();
            terms.push( ui.item.value );
            terms.push( "" );
            this.value = terms.join( ", " );
            return false;
            }
        });
        });
        //]]>
        </script>' . "\n";

        return $scri_js;
    }
}

if (! function_exists('auto_complete_multi_query'))
{
    /**
     * [auto_complete_multièdb description]
     *
     * @param   string  $nom_array_js  [$nom_array_js description]
     * @param   string  $nom_champ     [$nom_champ description]
     * @param   string  $id_inpu       [$id_inpu description]
     * @param   array   $query           [$req description]
     *
     * @return  string
     */
    function auto_complete_multi_query(string $nom_array_js, string $nom_champ, string $id_inpu, array $query): string
    {
        $list_json = '';
        $list_json .= $nom_array_js . ' = [';
        
        foreach($query as $result) {
            $list_json .= '\'' . $result[$nom_champ] . '\',';
        }

        $list_json = rtrim($list_json, ',');
        $list_json .= '];';
        $scri_js = '';
        $scri_js .= '
        <script type="text/javascript">
        //<![CDATA[
        var '. $nom_array_js .';
        $(function() {
        '. $list_json .'
        function split( val ) {
        return val.split( /,\s*/ );
        }
        function extractLast( term ) {
        return split( term ).pop();
        }
        $( "#'. $id_inpu .'" )
        // dont navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).autocomplete( "instance" ).menu.active ) {
            event.preventDefault();
            }
        })
        .autocomplete({
            minLength: 0,
            source: function( request, response ) {
            response( $.ui.autocomplete.filter(
                '. $nom_array_js .', extractLast( request.term ) ) );
            },
            focus: function() {
            return false;
            },
            select: function( event, ui ) {
            var terms = split( this.value );
            terms.pop();
            terms.push( ui.item.value );
            terms.push( "" );
            this.value = terms.join( ", " );
            return false;
            }
        });
        });
        //]]>
        </script>'. "\n";

        return $scri_js;
    }
}

// date 

if (! function_exists('NightDay'))
{
    /**
     * Pour obtenir Nuit ou Jour ... Un grand Merci à P.PECHARD pour cette fonction
     *
     * @return  string
     */
    function NightDay(): string 
    {
        $Maintenant = strtotime("now");
        
        $Jour = strtotime(Config::get('two_core::config.lever'));
        $Nuit = strtotime(Config::get('two_core::config.coucher'));

        if ($Maintenant - $Jour < 0 xor $Maintenant - $Nuit > 0) {
            return "Nuit";
        } else {
            return "Jour";
        }
    }
}

if (! function_exists('formatTimestamp'))
{
    /**
     * Formate un timestamp en fonction de la valeur de $locale (config.php)
     * si "nogmt" est concaténé devant la valeur de $time, le décalage gmt n'est pas appliqué
     *
     * @param   string     $time  [$time description]
     *
     * @return  string
     */
    function formatTimestamp(string $time): string
    {
        global $datetime;

        $locale = config::get('two_core::config.locale');

        return $datetime = ucfirst(htmlentities(\PHP81_BC\strftime(__d('two_core', 'datestring'), $time, $locale), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'utf-8'));
    }
}

if (! function_exists('convertdateTOtimestamp'))
{
    /**
     * [convertdateTOtimestamp description]
     *
     * @param   string  $myrow  [$myrow description]
     *
     * @return  int
     */
    function convertdateTOtimestamp(string $myrow): int
    {
        if (substr($myrow, 2, 1) == "-") {
            $day = substr($myrow, 0, 2);
            $month = substr($myrow, 3, 2);
            $year = substr($myrow, 6, 4);
        } else {
            $day = substr($myrow, 8, 2);
            $month = substr($myrow, 5, 2);
            $year = substr($myrow, 0, 4);
        }

        $hour = substr($myrow, 11, 2);
        $mns = substr($myrow, 14, 2);
        $sec = substr($myrow, 17, 2);

        return mktime((int) $hour, (int) $mns, (int) $sec, (int) $month, (int) $day, (int) $year);
    }
}

if (! function_exists('post_convertdate'))
{
    /**
     * [post_convertdate description]
     *
     * @param   int     $tmst  [$tmst description]
     *
     * @return  string
     */
    function post_convertdate(int $tmst): string 
    {
        $val = $tmst > 0 ? date(__d('two_core', 'dateinternal'), $tmst) : '';
    
        return $val;
    }
}

if (! function_exists('convertdate'))
{
    /**
     * [convertdate description]
     *
     * @param   string  $myrow  [$myrow description]
     *
     * @return  string
     */
    function convertdate(string $myrow): string
    {
        $tmst = convertdateTOtimestamp($myrow);
        $val = post_convertdate($tmst);
    
        return $val;
    }
}
