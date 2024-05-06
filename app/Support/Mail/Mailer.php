<?php

declare(strict_types=1);

namespace App\Support\Mail;

use App\Support\Logs\Logs;
use App\Support\Facades\Theme;
use App\Support\Facades\User;
use App\Support\Utility\Spam;

use Npds\Support\Facades\DB;
use Npds\Support\Facades\Config;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MaillerExecption;


class mailer
{
 
    /**
     * Pour envoyer un mail en texte ou html avec ou sans pieces jointes
     * $mime = 'text' 'html' 'html-nobr'-(sans application de nl2br) ou 'mixed'-(avec piece(s) jointe(s) : génération ou non d'un DKIM suivant option choisie)
     *
     * @param   string  $email     [$email description]
     * @param   string  $subject   [$subject description]
     * @param   string  $message   [$message description]
     * @param   string  $from      [$from description]
     * @param   bool    $priority  [$priority description]
     * @param   false              [ description]
     * @param   string  $mime      [$mime description]
     * @param   text               [ description]
     * @param   string  $file      [$file description]
     *
     * @return  bool               [return description]
     */
    public static function send_email(string $email, string $subject, string $message, string $from = "", bool $priority = false, string $mime = "text", string $file = null): bool
    {
        $From_email = $from != '' ? $from : Config::get('npds.adminmail');

        if (preg_match('#^[_\.0-9a-z-]+@[0-9a-z-\.]+\.+[a-z]{2,4}$#i', $From_email)) {
            
            $config = Config::get('mailer');
            
            if ($config['dkim_auto'] == 2) {
                //Private key filename for this selector 
                $privatekeyfile = 'storage/mailer/key/'. Config::get('app.NPDS_Key') .'_dkim_private.pem';

                //Public key filename for this selector 
                $publickeyfile = 'storage/mailer/key/'. Config::get('app.NPDS_Key') .'_dkim_public.pem';
                
                if (!file_exists($privatekeyfile)) {

                    //Create a 2048-bit RSA key with an SHA256 digest 
                    $pk = openssl_pkey_new(
                        [
                            'digest_alg' => 'sha256',
                            'private_key_bits' => 2048,
                            'private_key_type' => OPENSSL_KEYTYPE_RSA,
                        ]
                    );

                    //Save private key 
                    openssl_pkey_export_to_file($pk, $privatekeyfile);
                    
                    //Save public key 
                    $pubKey = openssl_pkey_get_details($pk);
                    $publickey = $pubKey['key'];
                    file_put_contents($publickeyfile, $publickey);
                }
            }

            $mail = new PHPMailer($config['debug']);

            try {
                //Server settings config smtp 
                if (Config::get('npds.mail_fonction') == 2) {
                    $mail->isSMTP();
                    $mail->Host       = $config['smtp_host'];
                    $mail->SMTPAuth   = $config['smtp_auth'];
                    $mail->Username   = $config['smtp_username'];
                    $mail->Password   = $config['smtp_password'];
                    
                    if ($config['smtp_secure']) {
                        if ($config['smtp_crypt'] === 'tls') {
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        } elseif ($config['smtp_crypt'] === 'ssl') {
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        }
                    }

                    $mail->Port = $config['smtp_port'];
                }

                $mail->CharSet = 'utf-8';
                $mail->Encoding = 'base64';
                
                if ($priority) {
                    $mail->Priority = 2;
                }
                
                //Recipients 
                $mail->setFrom(Config::get('npds.adminmail'), Config::get('npds.sitename'));
                $mail->addAddress($email, $email);

                //Content 
                if ($mime == 'mixed') {
                    $mail->isHTML(true);
                    
                    // pièce(s) jointe(s)) 
                    if (!is_null($file)) {
                        if (is_array($file)) {
                            $mail->addAttachment($file['file'], $file['name']);
                        } else {
                            $mail->addAttachment($file);
                        }
                    }
                }

                if (($mime == 'html') or ($mime == 'html-nobr')) {
                    $mail->isHTML(true);
                    
                    if ($mime != 'html-nobr') {
                        $message = nl2br($message);
                    }
                }

                $mail->Subject = $subject;
                $stub_mail = "<html>\n<head>\n<style type='text/css'>\nbody {\nbackground: #FFFFFF;\nfont-family: Tahoma, Calibri, Arial;\nfont-size: 1 rem;\ncolor: #000000;\n}\na, a:visited, a:link, a:hover {\ntext-decoration: underline;\n}\n</style>\n</head>\n<body>\n %s \n</body>\n</html>";
                
                if ($mime == 'text') {
                    $mail->isHTML(false);
                    $mail->Body = $message;
                } else {
                    $mail->Body = sprintf($stub_mail, $message);
                }
                
                if ($config['dkim_auto'] == 2) {
                    $mail->DKIM_domain = str_replace(['http://', 'https://'], ['', ''], Config::get('npds.nuke_url'));
                    $mail->DKIM_private = $privatekeyfile;;
                    $mail->DKIM_selector = Config::get('app.NPDS_Key');
                    $mail->DKIM_identity = $mail->From;
                }

                if (Config::get('npds.mail_fonction') == 2) {
                    if ($config['debug']) {
                        // on génère un journal détaillé après l'envoi du mail 
                        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    }
                }

                $mail->send();

                if ($config['debug']) {
                    // stop l'exécution du script pour affichage du journal sur la page 
                    die();
                }
                
                $result = true;
            } catch (MaillerExecption $e) {
                Logs::Ecr_Log('smtpmail', "send Smtp mail by $email", "Message could not be sent. Mailer Error: $mail->ErrorInfo");
                $result = false;
            }
        }

        return $result ? true : false;
    }

    /**
     * Pour copier un subject+message dans un email ($to_userid)
     *
     * @param   string|int  $to_userid  [$to_userid description]
     * @param   string  $sujet      [$sujet description]
     * @param   string  $message    [$message description]
     *
     * @return  void
     */
    public static function copy_to_email(string|int $to_userid, string $sujet, string $message): void
    {
        $user = DB::table('users')
                    ->select('email', 'send_email')
                    ->where('uid', $to_userid)
                    ->first();

        if (($user['email']) and ($user['send_email'] == 1)) {
            static::send_email($user['email'], $sujet, $message, '', true, 'html', '');
        }
    }
 
    /**
     * Appel la fonction d'affichage du groupe check_mail (theme principal de NPDS) sans class
     *
     * @param   string  $username  [$username description]
     *
     * @return  void
     */    
    public static function Mess_Check_Mail(string $username): void
    {
        static::Mess_Check_Mail_interface($username, '');
    }

    /**
     * Affiche le groupe check_mail (theme principal de NPDS)
     *
     * @param   string  $username  [$username description]
     * @param   string  $class     [$class description]
     *
     * @return  void
     */
    public static function Mess_Check_Mail_interface(string $username, string $class): void
    {
        if ($ibid = Theme::theme_image("fle_b.gif")) {
            $imgtmp = $ibid;
        } else {
            $imgtmp = false;
        }

        if ($class != "") {
            $class = "class=\"$class\"";
        }
        
        if ($username == Config::get('npds.anonymous')) {
            if ($imgtmp) {
                echo '<img alt="" src="'. $imgtmp .'" align="center" />'. $username .' - <a href="'. site_url('user.php') .'" '. $class .'>' . translate("Votre compte") . '</a>';
            } else {
                echo '['. $username .' - <a href="'. site_url('user.php') .'" '. $class .'>' . translate("Votre compte") . '</a>]';
            }
        } else {
            if ($imgtmp) {
                echo '<a href="'. site_url('user.php') .'" '. $class .'><img alt="" src="'. $imgtmp .'" align="center" />'. translate("Votre compte") .'</a>&nbsp;'. static::Mess_Check_Mail_Sub($username, $class);
            } else {
                echo '[<a href="'. site_url('user.php') .'" $class>'. translate("Votre compte") .'</a>&nbsp;&middot;&nbsp;'. static::Mess_Check_Mail_Sub($username, $class) .']';
            }
        }
    }

    /**
     * Affiche le groupe check_mail (theme principal de NPDS)
     *
     * @param   string  $username  [$username description]
     * @param   string  $class     [$class description]
     *
     * @return  string
     */
    public static function Mess_Check_Mail_Sub(string $username, string $class): string
    {
        if ($username) {
            $user = User::getUser();

            $userdata = explode(':', base64_decode($user));

            $total_messages = DB::table('priv_msgs')
                                ->select('msg_id')
                                ->where('to_userid', $userdata[0])
                                ->where('type_msg', 0)
                                ->count();
            
            $new_messages = DB::table('priv_msgs')
                                ->select('msg_id')
                                ->where('to_userid', $userdata[0])
                                ->where('read_msg', 0)
                                ->where('type_msg', 0)
                                ->count();

            if ($total_messages > 0) {
                if ($new_messages > 0) {
                    $Xcheck_Nmail = $new_messages;
                } else {
                    $Xcheck_Nmail = '0';
                }

                $Xcheck_mail = $total_messages;
            } else {
                $Xcheck_Nmail = '0';
                $Xcheck_mail = '0';
            }
        }

        $YNmail = "$Xcheck_Nmail";
        $Ymail = "$Xcheck_mail";
        $Mel = '<a href="'. site_url('viewpmsg.php') .'" '. $class .'>Mel</a>';
        
        if ($Xcheck_Nmail > 0) {
            $YNmail = '<a href="'. site_url('viewpmsg.php') .'" '. $class .'>'. $Xcheck_Nmail .'</a>';
            $Mel = 'Mel';
        }

        if ($Xcheck_mail > 0) {
            $Ymail = '<a href="'. site_url('viewpmsg.php') .'" '. $class .'>'. $Xcheck_mail .'</a>';
            $Mel = 'Mel';
        }

        return ("$Mel : $YNmail / $Ymail");
    }

    /**
     * Contrôle si le domaine existe et si il dispose d'un serveur de mail
     *
     * @param   string  $email  [$email description]
     *
     * @return  bool
     */
    public static function checkdnsmail(string $email): bool 
    {
        $ibid = explode('@', $email);

        if (!checkdnsrr($ibid[1], 'MX')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * utilisateur dans le fichier des mails incorrect true or false 
     *
     * @param   string|int  $utilisateur  [$utilisateur description]
     *
     * @return  bool
     */
    public static function isbadmailuser(string|int $utilisateur): bool
    {
        $contents = '';
        $filename = "storage/users_private/usersbadmail.txt";
        $handle = fopen($filename, "r");

        if (filesize($filename) > 0) {
            $contents = fread($handle, filesize($filename));
        }

        fclose($handle);

        if (strstr($contents, '#' . $utilisateur .'|')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * [fakedmail description]
     *
     * @param   array   $r  [$r description]
     *
     * @return  string
     */
    public static function fakedmail(array $r): string
    {
        return Spam::preg_anti_spam($r[1]);
    }

}
