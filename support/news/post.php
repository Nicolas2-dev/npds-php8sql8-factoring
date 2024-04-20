<?php

declare(strict_types=1);

namespace npds\support\news;

use npds\support\theme\theme;
use npds\support\utility\code;
use npds\system\config\Config;
use npds\support\language\language;
use npds\system\support\facades\Request;


class post
{

    /**
     * [code_aff description]
     *
     * @param   string  $subject   [$subject description]
     * @param   string  $story     [$story description]
     * @param   string  $bodytext  [$bodytext description]
     * @param   string  $notes     [$notes description]
     *
     * @return  void
     */
    public static function code_aff(string $subject, string $story, string $bodytext, string $notes): void
    {
        $local_user_language = Request::input('local_user_language');
    
        $subjectX = code::aff_code(language::preview_local_langue($local_user_language, $subject));
        $storyX = code::aff_code(language::preview_local_langue($local_user_language, $story));
        $bodytextX = code::aff_code(language::preview_local_langue($local_user_language, $bodytext));
        $notesX = code::aff_code(language::preview_local_langue($local_user_language, $notes));
    
        theme::themepreview($subjectX, $storyX, $bodytextX, $notesX);
    }
    
    /**
     * [publication description]
     *
     * @param   string|int   $dd_pub  [$dd_pub description]
     * @param   string|int   $fd_pub  [$fd_pub description]
     * @param   string|int   $dh_pub  [$dh_pub description]
     * @param   string|int   $fh_pub  [$fh_pub description]
     * @param   int   $epur    [$epur description]
     *
     * @return  void
     */
    public static function publication(string|int $dd_pub, string|int $fd_pub, string|int $dh_pub, string|int $fh_pub, int $epur): void
    {
        $today = getdate(time() + ((int) Config::get('npds.gmt') * 3600));

        if (!$dd_pub) {
            $dd_pub .= $today['year'] . '-';
            if ($today['mon'] < 10) {
                $dd_pub .= '0' . $today['mon'] . '-';
            } else {
                $dd_pub .= $today['mon'] . '-';
            }
            
            if ($today['mday'] < 10) {
                $dd_pub .= '0' . $today['mday'];
            } else {
                $dd_pub .= $today['mday'];
            }
        }
    
        if (!$fd_pub) {
            $fd_pub .= ($today['year'] + 99) . '-';
            if ($today['mon'] < 10) {
                $fd_pub .= '0' . $today['mon'] . '-';
            } else {
                $fd_pub .= $today['mon'] . '-';
            }
            
            if ($today['mday'] < 10){ 
                $fd_pub .= '0' . $today['mday'];
            } else {
                $fd_pub .= $today['mday'];
            }
        }
    
        if (!$dh_pub) {
            if ($today['hours'] < 10) {
                $dh_pub .= '0' . $today['hours'] . ':';
            } else {
                $dh_pub .= $today['hours'] . ':';
            }
            
            if ($today['minutes'] < 10) {
                $dh_pub .= '0' . $today['minutes'];
            } else {
                $dh_pub .= $today['minutes'];
            }
        }
    
        if (!$fh_pub) {
            if ($today['hours'] < 10) {
                $fh_pub .= '0' . $today['hours'] . ':';
            } else {
                $fh_pub .= $today['hours'] . ':';
            }
            
            if ($today['minutes'] < 10) {
                $fh_pub .= '0' . $today['minutes'];
            } else {
                $fh_pub .= $today['minutes'];
            }
        }
    
        echo '
        <hr />
        <p class="small text-end">
        ' . translate(date("l")) . date(" " . translate("dateinternal"), time() + ((int) Config::get('npds.gmt') * 3600)) . '
        </p>';
    
        if ($dd_pub != -1 and $dh_pub != -1) {
            echo '
            <div class="row mb-3">
                <div class="col-sm-5 mb-2">
                    <label class="form-label" for="dd_pub">' . translate("Date de publication") . '</label>
                    <input type="text" class="form-control flatpi" id="dd_pub" name="dd_pub" value="' . $dd_pub . '" />
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="form-label" for="dh_pub">' . translate("Heure") . '</label>
                    <div class="input-group clockpicker">
                        <span class="input-group-text"><i class="far fa-clock fa-lg"></i></span>
                        <input type="text" class="form-control" placeholder="Heure" id="dh_pub" name="dh_pub" value="' . $dh_pub . '" />
                    </div>
                </div>
            </div>'; 
        }
    
        echo '
        <div class="row mb-3">
            <div class="col-sm-5 mb-2">
                <label class="form-label" for="fd_pub">' . translate("Date de fin de publication") . '</label>
                <input type="text" class="form-control flatpi" id="fd_pub" name="fd_pub" value="' . $fd_pub . '" />
            </div>
            <div class="col-sm-3 mb-2">
                <label class="form-label" for="fh_pub">' . translate("Heure") . '</label>
                <div class="input-group clockpicker">
                    <span class="input-group-text"><i class="far fa-clock fa-lg"></i></span>
                    <input type="text" class="form-control" placeholder="Heure" id="fh_pub" name="fh_pub" value="' . $fh_pub . '" />
                </div>
            </div>
        </div>
        <script type="text/javascript" src="assets/shared/flatpickr/dist/flatpickr.min.js"></script>
        <script type="text/javascript" src="assets/shared/flatpickr/dist/l10n/' . language::language_iso(1, '', '') . '.js"></script>
        <script type="text/javascript" src="assets/js/bootstrap-clockpicker.min.js"></script>
        <script type="text/javascript">
        //<![CDATA[
            $(document).ready(function() {
                $("<link>").appendTo("head").attr({type: "text/css", rel: "stylesheet",href: "assets/shared/flatpickr/dist/themes/npds.css"});
                $("<link>").appendTo("head").attr({type: "text/css", rel: "stylesheet",href: "assets/css/bootstrap-clockpicker.min.css"});
                $(".clockpicker").clockpicker({
                    placement: "bottom",
                    align: "top",
                    autoclose: "true"
                });
    
            })
            const fp = flatpickr(".flatpi", {
                altInput: true,
                altFormat: "l j F Y",
                dateFormat:"Y-m-d",
                "locale": "' . language::language_iso(1, '', '') . '",
            });
        //]]>
        </script>
    
        <div class="mb-3 row">
            <label class="col-form-label">' . translate("Epuration de la new à la fin de sa date de validité") . '</label>';
    
        $sel1 = '';
        $sel2 = '';
    
        if (!$epur) {
            $sel2 = 'checked="checked"';
        } else {
            $sel1 = 'checked="checked"';
        }
        
        echo '
            <div class="col-sm-8 my-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="epur_y" name="epur" value="1" ' . $sel1 . ' />
                    <label class="form-check-label" for="epur_y">' . translate("Oui") . '</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="epur_n" name="epur" value="0" ' . $sel2 . ' />
                    <label class="form-check-label" for="epur_n">' . translate("Non") . '</label>
                </div>
            </div>
        </div>
        <hr />';
    }

}