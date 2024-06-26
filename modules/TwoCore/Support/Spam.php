<?php

declare(strict_types=1);

namespace Modules\TwoCore\Support;

use Two\Support\Facades\Crypt;


class Spam
{

    /**
     * Permet l'utilisation de la fonction anti_spam via preg_replace
     *
     * @param   string  $ibid  [$ibid description]
     *
     * @return  string
     */
    public static function preg_anti_spam(string $ibid): string
    {
        // Adaptation - David MARTINET alias Boris (2011)
        return ("<a href=\"mailto:" . static::anti_spam($ibid, 1) . "\" target=\"_blank\">" . static::anti_spam($ibid, 0) . "</a>");
    }
  
    /**
     * Encode une chaine en mélangeant caractères normaux, codes décimaux et hexa. Si $highcode == 1,
     * utilise également le codage ASCII (compatible uniquement avec des mailto et des URL, pas pour affichage)
     *
     * @param   string  $str       [$str description]
     * @param   int     $highcode  [$highcode description]
     *
     * @return  string
     */
    public static function anti_spam(string $str, int $highcode = 0): string
    {
        // Idée originale : Pomme (2004). Nouvelle version : David MARTINET alias Boris (2011)
        $str_encoded = "";

        mt_srand( (int) microtime() * 1000000);

        for ($i = 0; $i < strlen($str); $i++) {
            
            if ($highcode == 1) {
                $alea = mt_rand(1, 400);
                $modulo = 4;
            } else {
                $alea = mt_rand(1, 300);
                $modulo = 3;
            }

            switch (($alea % $modulo)) {
                case 0:
                    $str_encoded .= $str[$i];
                    break;

                case 1:
                    $str_encoded .= "&#" . ord($str[$i]) . ";";
                    break;

                case 2:
                    $str_encoded .= "&#x" . bin2hex($str[$i]) . ";";
                    break;

                case 3:
                    $str_encoded .= "%" . bin2hex($str[$i]) . "";
                    break;

                default:
                    $str_encoded = "Error";
                    break;
            }
        }

        return $str_encoded;
    }
 
    /**
     * forge un champ de formulaire (champ de saisie : $asb_reponse / champ hidden : asb_question) permettant de déployer une fonction anti-spambot
     *
     * @return  string
     */
    public static function Q_spambot(): string
    {
        $user = users::getUser();

        // Idée originale, développement et intégration - Gérald MARINO alias neo-machine
        // Rajout brouillage anti_spam() : David MARTINET, alias Boris (2011)
        // Other stuff : Dev 2012        
        $asb_question = array(
            '4 - (3 / 1)'       => 1,
            '7 - 5 - 0'         => 2,
            '2 + (1 / 1)'       => 3,
            '2 + (1 + 1)'       => 4,
            '3 + (0) + 2'       => 5,
            '3 + (9 / 3)'       => 6,
            '4 + 3 - 0'         => 7,
            '6 + (0) + 2'       => 8,
            '8 + (5 - 4)'       => 9,
            '0 + (6 + 4)'       => 10,
            '(5 * 2) + 1'       => 11,
            '6 + (3 + 3)'       => 12,
            '1 + (6 * 2)'       => 13,
            '(8 / 1) + 6 '      => 14,
            '6 + (5 + 4)'       => 15,
            '8 + (4 * 2)'       => 16,
            '1 + (8 * 2)'       => 17,
            '9 + (3 + 6)'       => 18,
            '(7 * 2) + 5'       => 19,
            '(8 * 3) - 4'       => 20,
            '7 + (2 * 7)'       => 21,
            '9 + 5 + 8'         => 22,
            '(5 * 4) + 3'       => 23,
            '0 + (8 * 3)'       => 24,
            '1 + (4 * 6)'       => 25,
            '(6 * 5) - 4'       => 26,
            '3 * (9 + 0)'       => 27,
            '4 + (3 * 8)'       => 28,
            '(6 * 4) + 5'       => 29,
            '0 + (6 * 5)'       => 30
        );

        // START ALEA
        mt_srand( (int) microtime() * 1000000);

        // choix de la question
        $asb_index = mt_rand(0, count($asb_question) - 1);
        $ibid = array_keys($asb_question);
        $aff = $ibid[$asb_index];

        // translate
        $tab = explode(' ', str_replace(')', '', str_replace('(', '', $aff)));
        $al1 = mt_rand(0, count($tab) - 1);

        if (function_exists("imagepng")) {
            $aff = str_replace($tab[$al1], html_entity_decode($tab[$al1], ENT_QUOTES | ENT_HTML401, 'UTF-8'), $aff);
        } else {
            $aff = str_replace($tab[$al1], $tab[$al1], $aff);
        }

        // mis en majuscule
        if ($asb_index % 2) {
            $aff = ucfirst($aff);
        }
        // END ALEA

        //Captcha - si GD
        if (function_exists("imagepng")) {
            $aff = "<img src=\"getfile.php?att_id=" . rawurlencode(Crypt::encrypt($aff . " = ")) . "&amp;apli=captcha\" style=\"vertical-align: middle;\" />";
        } else {
            $aff = "" . static::anti_spam($aff . " = ", 0) . "";
        }

        $tmp = '';

        if ($user == '') {
            $tmp = '
            <div class="mb-3 row">
                <div class="col-sm-9 text-end">
                    <label class="form-label text-danger" for="asb_reponse">' . __d('two_core', 'Anti-Spam / Merci de répondre à la question suivante : ') . '&nbsp;' . $aff . '</label>
                </div>
                <div class="col-sm-3 text-end">
                    <input class="form-control" type="text" id="asb_reponse" name="asb_reponse" maxlength="2" onclick="this.value" />
                    <input type="hidden" name="asb_question" value="' . Crypt::encrypt($ibid[$asb_index] . ',' . time()) . '" />
                </div>
            </div>';
        } else {
            $tmp = '
            <input type="hidden" name="asb_question" value="" />
            <input type="hidden" name="asb_reponse" value="" />';
        }

        return $tmp;
    }
 
    /**
     * Log spambot activity : $ip="" => getip of the current user OR $ip="x.x.x.x"
     * $status = Op to do : true => not log or suppress log - false => log+1 - ban => Ban an IP 
     *
     * @param   string  $ip      [$ip description]
     * @param   string  $status  [$status description]
     *
     * @return  void
     */
    public static function L_spambot(string $ip, string $status): void
    {
        $cpt_sup = 0;
        $maj_fic = false;

        if ($ip == '') {
            $ip = getip();
        }

        $ip = urldecode($ip);

        if (file_exists("storage/logs/spam.log")) {
            $tab_spam = str_replace("\r\n", '', file("storage/logs/spam.log"));

            if (in_array($ip . '|1', $tab_spam)) {
                $cpt_sup = 2;
            }
            
            if (in_array($ip . '|2', $tab_spam)) {
                $cpt_sup = 3;
            }

            if (in_array($ip . '|3', $tab_spam)) {
                $cpt_sup = 4;
            }

            if (in_array($ip . '|4', $tab_spam)) {
                $cpt_sup = 5;
            }
        }

        if ($cpt_sup) {
            if ($status == "false") {
                $tab_spam[array_search($ip . '|' . ($cpt_sup - 1), $tab_spam)] = $ip . '|' . $cpt_sup;
            } else if ($status == "ban") {
                $tab_spam[array_search($ip . '|' . ($cpt_sup - 1), $tab_spam)] = $ip . '|5';
            } else {
                $tab_spam[array_search($ip . '|' . ($cpt_sup - 1), $tab_spam)] = '';
            }

            $maj_fic = true;
        } else {
            if ($status == "false") {
                $tab_spam[] = $ip . '|1';
                $maj_fic = true;
            } else if ($status == 'ban') {
                
                if (!in_array($ip . '|5', $tab_spam)) {
                    $tab_spam[] = $ip . '|5';
                    $maj_fic = true;
                }
            }
        }

        if ($maj_fic) {
            $file = fopen("storage/logs/spam.log", "w");
            
            foreach ($tab_spam as $key => $val) {
                if ($val) {
                    fwrite($file, $val . "\r\n");
                }
            }

            fclose($file);
        }
    }
  
    /**
     * valide le champ $asb_question avec la valeur de $asb_reponse (anti-spambot)
     * et filtre le contenu de $message si nécessaire
     *
     * @param   string  $asb_question  [$asb_question description]
     * @param   string  $asb_reponse   [$asb_reponse description]
     * @param   string  $message       [$message description]
     *
     * @return  bool                   [return description]
     */
    public static function R_spambot(string $asb_question, string $asb_reponse, string $message = ''): bool
    {
        global $user, $REQUEST_METHOD;

        // idée originale, développement et intégration - Gérald MARINO alias neo-machine
        if ($REQUEST_METHOD == "POST") {
            if ($user == '') {
                
                if (($asb_reponse != '') and (is_numeric($asb_reponse)) and (strlen($asb_reponse) <= 2)) {
                    $ibid = Crypt::decrypt($asb_question);
                    $ibid = explode(',', $ibid);
                    $result = "\$arg=($ibid[0]);";

                    // submit intervient en moins de 5 secondes (trop vite) ou plus de 30 minutes (trop long)
                    $temp = time() - $ibid[1];
                    
                    if (($temp < 1800) and ($temp > 5)) {
                        eval($result);
                    } else {
                        $arg = uniqid( (string) mt_rand());
                    }
                } else {
                    $arg = uniqid( (string) mt_rand());
                }

                if ($arg == $asb_reponse) {
                    // plus de 2 http:// dans le texte
                    preg_match_all('#http://#', $message, $regs);
                    
                    if (count($regs[0]) > 2) {
                        static::L_spambot('', "false");
                        return false;
                    } else {
                        static::L_spambot('', "true");
                        return true;
                    }
                } else {
                    static::L_spambot('', "false");
                    return false;
                }
            } else {
                static::L_spambot('', "true");
                return true;
            }
        } else {
            static::L_spambot('', "false");
            return false;
        }
    }

    /**
     * [spam_logs description]
     *
     * @return  void
     */
    public static function spam_logs(): void
    {
        // First of all : Spam from IP / |5 indicate that the same IP has passed 6 times with status KO in the anti_spambot function
        if (file_exists("storage/logs/spam.log")) {
            $tab_spam = str_replace("\r\n", "", file("storage/logs/spam.log"));
        }

        $ip = getIp();

        if (is_array($tab_spam)) {
            $ipadr = urldecode($ip);
            $ipv = strstr($ipadr, ':') ? '6' : '4';
            
            if (in_array($ipadr . "|5", $tab_spam)) {
                access_denied();
            }
            
                //=> nous pouvons bannir une plage d'adresse ip en V4 (dans l'admin IPban sous forme x.x.%|5 ou x.x.x.%|5)
            if ($ipv == '4') {
                $ip4detail = explode('.', $ipadr);
                if (in_array($ip4detail[0] . '.' . $ip4detail[1] . '.%|5', $tab_spam)) {
                    access_denied();
                }

                if (in_array($ip4detail[0] . '.' . $ip4detail[1] . '.' . $ip4detail[2] . '.%|5', $tab_spam)) {
                    access_denied();
                }
            }

            //=> nous pouvons bannir une plage d'adresse ip en V6 (dans l'admin IPban sous forme x:x:%|5 ou x:x:x:%|5)
            if ($ipv == '6') {
                $ip6detail = explode(':', $ipadr);
                if (in_array($ip6detail[0] . ':' . $ip6detail[1] . ':%|5', $tab_spam)) {
                    access_denied();
                }

                if (in_array($ip6detail[0] . ':' . $ip6detail[1] . ':' . $ip6detail[2] . ':%|5', $tab_spam)) {
                    access_denied();
                }
            }
        }
    }

}
