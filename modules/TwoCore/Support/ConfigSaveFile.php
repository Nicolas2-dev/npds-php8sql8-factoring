<?php

declare(strict_types=1);

namespace Modules\TwoCore\Support;


class ConfigSaveFile
{

    /**
     * [save_setting_npds description]
     *
     * @param   int     $xparse                  [$xparse description]
     * @param   string  $xsitename               [$xsitename description]
     * @param   string  $xnuke_url               [$xnuke_url description]
     * @param   string  $xsite_logo              [$xsite_logo description]
     * @param   string  $xslogan                 [$xslogan description]
     * @param   string  $xstartdate              [$xstartdate description]
     * @param   string  $xadminmail              [$xadminmail description]
     * @param   int     $xtop                    [$xtop description]
     * @param   int     $xstoryhome              [$xstoryhome description]
     * @param   int     $xoldnum                 [$xoldnum description]
     * @param   int     $xultramode              [$xultramode description]
     * @param   int     $xanonpost               [$xanonpost description]
     * @param   string  $xDefault_Theme          [$xDefault_Theme description]
     * @param   int     $xbanners                [$xbanners description]
     * @param   string  $xmyIP                   [$xmyIP description]
     * @param   string  $xfoot1                  [$xfoot1 description]
     * @param   string  $xfoot2                  [$xfoot2 description]
     * @param   string  $xfoot3                  [$xfoot3 description]
     * @param   string  $xfoot4                  [$xfoot4 description]
     * @param   string  $xbackend_title          [$xbackend_title description]
     * @param   string  $xbackend_language       [$xbackend_language description]
     * @param   string  $xbackend_image          [$xbackend_image description]
     * @param   int     $xbackend_width          [$xbackend_width description]
     * @param   int     $xbackend_height         [$xbackend_height description]
     * @param   string  $xlanguage               [$xlanguage description]
     * @param   string  $xlocale                 [$xlocale description]
     * @param   int     $xperpage                [$xperpage description]
     * @param   int     $xpopular                [$xpopular description]
     * @param   int     $xnewlinks               [$xnewlinks description]
     * @param   int     $xtoplinks               [$xtoplinks description]
     * @param   int     $xlinksresults           [$xlinksresults description]
     * @param   int     $xlinks_anonaddlinklock  [$xlinks_anonaddlinklock description]
     * @param   int     $xnotify                 [$xnotify description]
     * @param   string  $xnotify_email           [$xnotify_email description]
     * @param   string  $xnotify_subject         [$xnotify_subject description]
     * @param   string  $xnotify_message         [$xnotify_message description]
     * @param   string  $xnotify_from            [$xnotify_from description]
     * @param   int     $xmoderate               [$xmoderate description]
     * @param   string  $xanonymous              [$xanonymous description]
     * @param   int     $xmaxOptions             [$xmaxOptions description]
     * @param   int     $xsetCookies             [$xsetCookies description]
     * @param   string  $xtipath                 [$xtipath description]
     * @param   string  $xuserimg                [$xuserimg description]
     * @param   string  $xadminimg               [$xadminimg description]
     * @param   int     $xadmingraphic           [$xadmingraphic description]
     * @param   int     $xadmart                 [$xadmart description]
     * @param   int     $xminpass                [$xminpass description]
     * @param   int     $xhttpref                [$xhttpref description]
     * @param   int     $xhttprefmax             [$xhttprefmax description]
     * @param   int     $xpollcomm               [$xpollcomm description]
     * @param   int     $xlinkmainlogo           [$xlinkmainlogo description]
     * @param   string  $xstart_page             [$xstart_page description]
     * @param   int     $xsmilies                [$xsmilies description]
     * @param   int     $xOnCatNewLink           [$xOnCatNewLink description]
     * @param   int     $xshort_user             [$xshort_user description]
     * @param   int     $xgzhandler              [$xgzhandler description]
     * @param   bool    $xrss_host_verif         [$xrss_host_verif description]
     * @param   bool    $xcache_verif            [$xcache_verif description]
     * @param   int     $xmember_list            [$xmember_list description]
     * @param   string  $xdownload_cat           [$xdownload_cat description]
     * @param   int     $xmod_admin_news         [$xmod_admin_news description]
     * @param   int     $xgmt                    [$xgmt description]
     * @param   int     $xAutoRegUser            [$xAutoRegUser description]
     * @param   string  $xTitlesitename          [$xTitlesitename description]
     * @param   int     $xshort_review           [$xshort_review description]
     * @param   int     $xnot_admin_count        [$xnot_admin_count description]
     * @param   int     $xadmin_cook_duration    [$xadmin_cook_duration description]
     * @param   int     $xuser_cook_duration     [$xuser_cook_duration description]
     * @param   int     $xtroll_limit            [$xtroll_limit description]
     * @param   int     $xsubscribe              [$xsubscribe description]
     * @param   int     $xCloseRegUser           [$xCloseRegUser description]
     * @param   int     $xshort_menu_admin       [$xshort_menu_admin description]
     * @param   int     $xmail_fonction          [$xmail_fonction description]
     * @param   int     $xmemberpass             [$xmemberpass description]
     * @param   int     $xshow_user              [$xshow_user description]
     * @param   bool    $xdns_verif              [$xdns_verif description]
     * @param   int     $xmember_invisible       [$xmember_invisible description]
     * @param   string  $xavatar_size            [$xavatar_size description]
     * @param   string  $xlever                  [$xlever description]
     * @param   string  $xcoucher                [$xcoucher description]
     * @param   bool    $xmulti_langue           [$xmulti_langue description]
     * @param   string  $xadmf_ext               [$xadmf_ext description]
     * @param   int     $xsavemysql_size         [$xsavemysql_size description]
     * @param   int     $xsavemysql_mode         [$xsavemysql_mode description]
     * @param   bool    $xtiny_mce               [$xtiny_mce description]
     * @param   int     $xnpds_twi               [$xnpds_twi description]
     * @param   int     $xnpds_fcb               [$xnpds_fcb description]
     * @param   string  $xDefault_Skin           [$xDefault_Skin description]
     *
     * @return  void                             [return description]
     */
    public static function save_setting_npds(int $xparse, string $xsitename, string $xnuke_url, string $xsite_logo, string $xslogan, string $xstartdate, string $xadminmail, int $xtop, int $xstoryhome, int $xoldnum, int $xultramode, int $xanonpost, string $xDefault_Theme, int $xbanners, string $xmyIP, string $xfoot1, string $xfoot2, string $xfoot3, string $xfoot4, string $xbackend_title, string $xbackend_language, string $xbackend_image, int $xbackend_width, int $xbackend_height, string $xlanguage, string $xlocale, int $xperpage, int $xpopular, int $xnewlinks, int $xtoplinks, int $xlinksresults, int $xlinks_anonaddlinklock, int $xnotify, string $xnotify_email, string $xnotify_subject, string $xnotify_message, string $xnotify_from, int $xmoderate, string $xanonymous, int $xmaxOptions, int $xsetCookies, string $xtipath, string $xuserimg, string $xadminimg, int $xadmingraphic, int $xadmart, int $xminpass, int $xhttpref, int $xhttprefmax, int $xpollcomm, int $xlinkmainlogo, string $xstart_page, int $xsmilies, int $xOnCatNewLink, int $xshort_user, int $xgzhandler, bool $xrss_host_verif, bool $xcache_verif, int $xmember_list, string $xdownload_cat, int $xmod_admin_news, int $xgmt, int $xAutoRegUser, string $xTitlesitename, int $xshort_review, int $xnot_admin_count, int $xadmin_cook_duration, int $xuser_cook_duration, int $xtroll_limit, int $xsubscribe, int $xCloseRegUser, int $xshort_menu_admin, int $xmail_fonction, int $xmemberpass, int $xshow_user, bool $xdns_verif, int $xmember_invisible, string $xavatar_size, string $xlever, string $xcoucher, bool $xmulti_langue, string $xadmf_ext, int $xsavemysql_size, int $xsavemysql_mode, bool $xtiny_mce, int $xnpds_twi, int $xnpds_fcb, string $xDefault_Skin): void
    {

        $lineblock = static::linecomment('General Site Configuration');

        $lineblock .= static::lineBlockWhite('Select the parse function you want to use for preference', 'parse', $xparse);
        $lineblock .= static::lineBlockWhite('PHP > 5.x : default 0 / PHP < 5.x sending compressed html with zlib : 1 - be careful', 'gzhandler', $xgzhandler);
        $lineblock .= static::lineBlockWhite('Duration in hour for Admin cookie (default 24)', 'admin_cook_duration', $xadmin_cook_duration);
        $lineblock .= static::lineBlockWhite('Duration in hour for Admin cookie (default 24)', 'user_cook_duration', $xuser_cook_duration);
        $lineblock .= static::lineBlockWhite('Your Site Name', 'sitename', $xsitename);
        $lineblock .= static::lineBlockWhite('Your Site Phrase for the Title (html Title Tag) off the HTML Page', 'Titlesitename', $xTitlesitename);
        $lineblock .= static::lineBlockWhite('Complete URL for your site (Do not put / at end)', 'nuke_url', $xnuke_url);
        $lineblock .= static::lineBlockWhite('Logo for Printer Friendly Page (It\'s good to have a Black/White graphic)', 'site_logo', $xsite_logo);
        $lineblock .= static::lineBlockWhite('Your site\'s slogan', 'slogan', $xslogan);
        $lineblock .= static::lineBlockWhite('Start Date to display in Statistic Page', 'startdate', $xstartdate);
        $lineblock .= static::lineBlockWhite('Allow Anonymous to Post Comments? (1=Yes 0=No)', 'anonpost', $xanonpost);
        $lineblock .= static::lineBlockWhite('Maximum Number off Comments per user (24H)', 'troll_limit', ((!$xtroll_limit) ? 6 : $xtroll_limit));
        $lineblock .= static::lineBlockWhite('Moderation of comments', 'moderate', $xmoderate);
        $lineblock .= static::lineBlockWhite('Allow only Moderator and Admin to Post News? (1=Yes 0=No)', 'mod_admin_news', $xmod_admin_news);
        $lineblock .= static::lineBlockWhite('Don\'t record Admin\'s Hits in stats (1=Yes=>don\'t rec 0=No=>rec)', 'not_admin_count', $xnot_admin_count);
        $lineblock .= static::lineBlockWhite('Default Theme for your site (See /themes directory for the complete list, case sensitive!)', 'Default_Theme', $xDefault_Theme);
        $lineblock .= static::lineBlockWhite('Default Skin for Theme ... with skins (See /themes/_skins directory for the complete list, case sensitive!)', 'Default_Skin', ((substr($xDefault_Theme, -3) != "_sk") ? '' : $xDefault_Skin ));
        $lineblock .= static::lineBlockWhite('Default Page for your site (default : index.php but you can use : topics.php, links.php ...)', 'Start_Page', $xstart_page);
        $lineblock .= static::lineBlockWhite('Messages for all footer pages (Can include HTML code)', 'foot1', $xfoot1);
        $lineblock .= static::lineBlockWhite('Messages for all footer pages (Can include HTML code)', 'foot2', $xfoot2);
        $lineblock .= static::lineBlockWhite('Messages for all footer pages (Can include HTML code)', 'foot3', $xfoot3);
        $lineblock .= static::lineBlockWhite('Messages for all footer pages (Can include HTML code)', 'foot4', $xfoot4);
        $lineblock .= static::lineBlockWhite('Anonymous users Default Name', 'anonymous', $xanonymous);
        $lineblock .= static::lineBlockWhite('Minimum character for users passwords', 'minpass', $xminpass);
        $lineblock .= static::lineBlockWhite('Number off user showed in memberslist page', 'show_user', $xshow_user);

        $lineblock .= static::linecomment('General Stories Options');

        $lineblock .= static::lineBlockWhite('How many items in Top Page?', 'top', ((!$xtop) ? 10 : $xtop));
        $lineblock .= static::lineBlockWhite('How many stories to display in Home Page?', 'storyhome', ((!$xstoryhome) ? 10 : $xstoryhome));
        $lineblock .= static::lineBlockWhite('How many stories in Old Articles Box?', 'oldnum', ((!$xoldnum) ? 10 : $xoldnum));

        $lineblock .= static::linecomment('Banners/Advertising Configuration');

        $lineblock .= static::lineBlockWhite('Activate Banners Ads for your site? (1=Yes 0=No)', 'banners', $xbanners);
        $lineblock .= static::lineBlockWhite('Write your IP number to not count impressions, be fair about this!', 'myIP', $xmyIP);

        $lineblock .= static::linecomment('XML/RDF Backend Configuration & Social Networks');

        $lineblock .= static::lineBlockWhite('Backend title, can be your site\'s name and slogan', 'backend_title', $xbackend_title);
        $lineblock .= static::lineBlockWhite('Language format of your site', 'backend_language', $xbackend_language);
        $lineblock .= static::lineBlockWhite('Image logo for your site', 'backend_image', $xbackend_image);
        $lineblock .= static::lineBlockWhite('Image logo width', 'backend_width', $xbackend_width);
        $lineblock .= static::lineBlockWhite('Image logo height', 'backend_height', $xbackend_height);
        $lineblock .= static::lineBlockWhite('Activate ultramode plain text and XML files backend syndication? (1=Yes 0=No). locate in /cache directory', 'ultramode', $xultramode);
        $lineblock .= static::lineBlockWhite('Activate the Twitter syndication? (1=Yes 0=No).', 'npds_twi', ((!$xnpds_twi) ? 0 : $xnpds_twi));
        $lineblock .= static::lineBlockWhite('Activate the Facebook syndication? (1=Yes 0=No).', 'npds_fcb', ((!$xnpds_fcb) ? 0 : $xnpds_fcb));

        $lineblock .= static::linecomment('Site Language Preferences');

        $lineblock .= static::lineBlockWhite('Language of your site (You need to have lang-xxxxxx.php file for your selected language in the /language directory of your site)', 'language', $xlanguage);
        $lineblock .= static::lineBlockWhite('Locale configuration to correctly display date with your country format. (See /usr/share/locale)', 'multi_langue', ($xmulti_langue == 0 ? 'false' : 'true'));
        $lineblock .= static::lineBlockWhite('Locale configuration to correctly display date with your GMT offset.', 'locale', $xlocale);
        $lineblock .= static::lineBlockWhite('HH:MM where Day become.', 'gmt', $xgmt);
        $lineblock .= static::lineBlockWhite('HH:MM where Night become.', 'lever', $xlever);
        $lineblock .= static::lineBlockWhite('Activate Multi-langue NPDS\'capability.', 'coucher', $xcoucher);

        $lineblock .= static::linecomment('Web Links Preferences');

        $lineblock .= static::lineBlockWhite('How many links to show on each page?', 'perpage', $xperpage);
        $lineblock .= static::lineBlockWhite('How many hits need a link to be listed as popular?', 'popular', $xpopular);
        $lineblock .= static::lineBlockWhite('How many links to display in the New Links Page?', 'newlinks', $xnewlinks);
        $lineblock .= static::lineBlockWhite('How many links to display in The Best Links Page? (Most Popular)', 'toplinks', $xtoplinks);
        $lineblock .= static::lineBlockWhite('How many links to display on each search result page?', 'linksresults', $xlinksresults);
        $lineblock .= static::lineBlockWhite('Is Anonymous autorise to post new links? (0=Yes 1=No)', 'links_anonaddlinklock', $xlinks_anonaddlinklock);
        $lineblock .= static::lineBlockWhite('Activate Logo on Main web Links Page (1=Yes 0=No)', 'linkmainlogo', $xlinkmainlogo);
        $lineblock .= static::lineBlockWhite('Activate Icon for New Categorie on Main web Links Page (1=Yes 0=No)', 'OnCatNewLink', $xOnCatNewLink);

        $lineblock .= static::linecomment('Function Mail and Notification of News Submissions');

        $lineblock .= static::lineBlockWhite('Site Administrator\'s Email', 'adminmail', $xadminmail);
        $lineblock .= static::lineBlockWhite('What Mail function to be used (1=mail, 2=email)', 'mail_fonction', $xmail_fonction);
        $lineblock .= static::lineBlockWhite('Notify you each time your site receives a news submission? (1=Yes 0=No)', 'notify', $xnotify);
        $lineblock .= static::lineBlockWhite('Email, address to send the notification', 'notify_email', $xnotify_email);
        $lineblock .= static::lineBlockWhite('Email subject', 'notify_subject', $xnotify_subject);
        $lineblock .= static::lineBlockWhite('Email body, message', 'notify_message', $xnotify_message);
        $lineblock .= static::lineBlockWhite('account name to appear in From field of the Email', 'notify_from', $xnotify_from);

        $lineblock .= static::linecomment('Survey/Polls Config');

        $lineblock .= static::lineBlockWhite('Number of maximum options for each poll', 'maxOptions', $xmaxOptions);
        $lineblock .= static::lineBlockWhite('Set cookies to prevent visitors vote twice in a period of 24 hours? (0=Yes 1=No)', 'setCookies', $xsetCookies);
        $lineblock .= static::lineBlockWhite('Activate comments in Polls? (1=Yes 0=No)', 'pollcomm', $xpollcomm);

        $lineblock .= static::linecomment('Some Graphics Options');

        $lineblock .= static::lineBlockWhite('Topics images path (put / only at the end, not at the begining)', 'tipath', $xtipath);
        $lineblock .= static::lineBlockWhite('User images path (put / only at the end, not at the begining)', 'userimg', $xuserimg);
        $lineblock .= static::lineBlockWhite('Administration system images path (put / only at the end, not at the begining)', 'adminimg', $xadminimg);
        $lineblock .= static::lineBlockWhite('Activate short Administration Menu? (1=Yes 0=No)', 'short_menu_admin', $xshort_menu_admin);
        $lineblock .= static::lineBlockWhite('Activate graphic menu for Administration Menu? (1=Yes 0=No)', 'admingraphic', $xadmingraphic);
        $lineblock .= static::lineBlockWhite('Image Files\'extesion for admin menu (default: gif)', 'admf_ext', ((!$xadmf_ext) ? "gif" : $xadmf_ext));
        $lineblock .= static::lineBlockWhite('How many articles to show in the admin section?', 'admart', $xadmart);

        $lineblock .= static::linecomment('HTTP Referers Options');

        $lineblock .= static::lineBlockWhite('Activate HTTP referer logs to know who is linking to our site? (1=Yes 0=No)', 'httpref', $xhttprefmax);
        $lineblock .= static::lineBlockWhite('Maximum number of HTTP referers to store in the Database (Try to not set this to a high number, 500 ~ 1000 is Ok)', 'httprefmax', $xhttpref);

        $lineblock .= static::linecomment('Miscelaneous Options');

        $lineblock .= static::lineBlockWhite('Activate Avatar? (1=Yes 0=No)', 'smilies', $xsmilies);
        $lineblock .= static::lineBlockWhite('Maximum size for uploaded avatars in pixel (width*height)', 'avatar_size', $xavatar_size);
        $lineblock .= static::lineBlockWhite('Activate Short User registration (without ICQ, MSN, ...)? (1=Yes 0=No)', 'short_user', $xshort_user);
        $lineblock .= static::lineBlockWhite('Make the members List Private (only for members) or Public (Private=Yes Public=No)', 'member_list', $xmember_list);
        $lineblock .= static::lineBlockWhite('Witch category do you want to show first in download section?', 'download_cat', $xdownload_cat);
        $lineblock .= static::lineBlockWhite('Allow automated new-user creation (sending email and allowed connection)', 'AutoRegUser', $xAutoRegUser);
        $lineblock .= static::lineBlockWhite('For transform reviews like \"gold book\" (1=Yes, 0=no)', 'short_review', $xshort_review);
        $lineblock .= static::lineBlockWhite('Allow your members to subscribe to topics, ... (1=Yes, 0=no)', 'subscribe', $xsubscribe);
        $lineblock .= static::lineBlockWhite('Allow members to hide from other members, ... (1=Yes, 0=no)', 'member_invisible', $xmember_invisible);
        $lineblock .= static::lineBlockWhite('Allow you to close New Member Registration (from Gawax Idea), ... (1=Yes, 0=no)', 'CloseRegUser', $xCloseRegUser);
        $lineblock .= static::lineBlockWhite('Allow user to choose alone the password (1=Yes, 0=no)', 'memberpass', $xmemberpass);

        $lineblock .= static::linecomment('HTTP Miscelaneous Options');

        $lineblock .= static::lineBlockWhite('Activate the validation of the existance of a web on Port 80 for Headlines (true=Yes false=No)', 'rss_host_verif', ($xrss_host_verif == 0 ? 'false' : 'true'));
        $lineblock .= static::lineBlockWhite('Activate the Advance Caching Meta Tag (pragma ...) (true=Yes false=No)', 'cache_verif', ($xcache_verif == 0 ? 'false' : 'true'));
        $lineblock .= static::lineBlockWhite('Activate the DNS resolution for posts (forum ...), IP-Ban, ... (true=Yes false=No)', 'dns_verif', ($xdns_verif == 0 ? 'false' : 'true'));

        $lineblock .= static::linecomment('SYSTEM Miscelaneous Options');

        $lineblock .= static::lineBlockWhite('Determine the maximum size for one file in the SaveMysql process', 'savemysql_size', $xsavemysql_size);
        $lineblock .= static::lineBlockWhite('Type of Myql process (1, 2 or 3)', 'savemysql_mode', $xsavemysql_mode);
        $lineblock .= static::lineBlockWhite('true=Yes or false=No to use tiny_mce Editor or NO Editor', 'tiny_mce', ($xtiny_mce == 0 ? 'false' : 'true'));

        static::lineEndBlockWhite('npds.php', $lineblock);
    }

    /**
     * [save_setting_app description]
     *
     * @return  void    [return description]
     */
    public function save_setting_app(): void 
    {
        //$lineblock = static::linecomment('');

        //$lineblock .= static::lineBlockWhite('', '', );

        //static::lineEndBlockWhite('app.php', $lineblock);
    }

    /**
     * [save_setting_versioning description]
     *
     * @param   string  $Version_Num  [$Version_Num description]
     * @param   string  $Version_Id   [$Version_Id description]
     * @param   string  $Version_Sub  [$Version_Sub description]
     *
     * @return  void
     */
    //public static function save_setting_versioning(string $Version_Num, string $Version_Id, string $Version_Sub): void
    public static function save_setting_versioning(): void
    {
        $lineblock = static::lineBlockWhite('Version Num', 'Version_Num', 'v.16.8');
        $lineblock .= static::lineBlockWhite('Version Id', 'Version_Id', 'NPDS');
        $lineblock .= static::lineBlockWhite('Version sub', 'Version_sub', 'REvolution');

        static::lineEndBlockWhite('versioning.php', $lineblock);
    }

    /**
     * [save_setting_filemanager description]
     *
     * @param   bool   $xfilemanager  [$xfilemanager description]
     *
     * @return  void
     */
    public static function save_setting_filemanager(bool $xfilemanager): void
    {
        $lineblock = static::lineBlockWhite('FileManager', 'manager', ($xfilemanager == 0 ? 'false' : 'true'));

        static::lineEndBlockWhite('filemanager.php', $lineblock);

    }

    /**
     * [save_setting_signature description]
     *
     * @param   string  $xEmailFooter  [$xEmailFooter description]
     *
     * @return  void
     */
    public static function save_setting_signature(string $xEmailFooter): void
    {
        $xEmailFooter = str_replace(chr(13) . chr(10), "\n", $xEmailFooter);

        $lineblock = static::lineBlockWhite('Signature', 'message', $xEmailFooter);

        static::lineEndBlockWhite('signature.php', $lineblock);
    }

    /**
     * [save_setting_mailler description]
     *
     * @param   bool    $xmail_debug     [$xmail_debug description]
     * @param   string  $xsmtp_host      [$xsmtp_host description]
     * @param   int     $xsmtp_port      [$xsmtp_port description]
     * @param   int     $xsmtp_auth      [$xsmtp_auth description]
     * @param   string  $xsmtp_username  [$xsmtp_username description]
     * @param   string  $xsmtp_password  [$xsmtp_password description]
     * @param   int     $xsmtp_secure    [$xsmtp_secure description]
     * @param   string  $xsmtp_crypt     [$xsmtp_crypt description]
     * @param   int     $xdkim_auto      [$xdkim_auto description]
     *
     * @return  void
     */
    public static function save_setting_mailler(bool $xmail_debug, string $xsmtp_host, string $xsmtp_port, int $xsmtp_auth, string $xsmtp_username, string $xsmtp_password, int $xsmtp_secure, string $xsmtp_crypt, int $xdkim_auto): void
    {
        $lineblock = static::lineBlockWhite('Debug', 'debug', ($xmail_debug == 0 ? 'false' : 'true'));
        $lineblock .= static::lineBlockWhite('Configurer le serveur SMTP', 'smtp_host', $xsmtp_host);
        $lineblock .= static::lineBlockWhite('Port TCP, utilisez 587 si vous avez activé le chiffrement TLS', 'smtp_port', $xsmtp_port);
        $lineblock .= static::lineBlockWhite('Activer l\'authentification SMTP', 'smtp_auth', $xsmtp_auth);
        $lineblock .= static::lineBlockWhite('Nom d\'utilisateur SMTP', 'smtp_username', $xsmtp_username);
        $lineblock .= static::lineBlockWhite('Mot de passe SMTP', 'smtp_password', $xsmtp_password);
        $lineblock .= static::lineBlockWhite('Activer le chiffrement TLS', 'smtp_secure', $xsmtp_secure);
        $lineblock .= static::lineBlockWhite('Type du chiffrement TLS', 'smtp_crypt', $xsmtp_crypt);
        $lineblock .= static::lineBlockWhite('DKIM 1 pour celui du dns 2 pour une génération automatique', 'dkim_auto', $xdkim_auto);

        static::lineEndBlockWhite('mailer.php', $lineblock);
    }

    /**
     * [lineBlockwhite description]
     *
     * @param   string  $description  [$description description]
     * @param   string  $key          [$key description]
     * @param   int|bool|string  $value        [$value description]
     *
     * @return  string
     */
    private static function lineBlockWhite(string $description, string $key, int|bool|string $value): string 
    {
        $line = "    /**\n";
        $line .= "    * " . $description ."\n";
        $line .= "    *\n";
        $line .= "    */\n";

        if ($value === 'false') {
            $line .= "    '" . $key . "'  => false,\n";
        } elseif ($value === 'true') {
            $line .= "    '" . $key . "'  => true,\n";
        } elseif (is_int($value)) {
            $line .= "    '" . $key . "'  => " . $value . ",\n";     
        } elseif (is_string($value)) {
            $line .= "    '" . $key . "'  => '" . $value . "',\n";  
        }

        $line .= "\n";

        return $line;
    }

    /**
     * [lineStartBlockWhite description]
     *
     * @param   string  $filename  [$filename description]
     *
     * @return  string
     */
    private static function lineStartBlockWhite(string $filename): string
    {
        global $file;

        $file = fopen("config/". $filename, "w");

        $line = "<?php\n";
        $line .= "\n";
        $line .= "return array(\n";
        $line .= "\n";

        return $line;
    } 

    /**
     * [lineEndBlockWhite description]
     *
     * @param   string  $filename   [$filename description]
     * @param   string  $lineblock  [$lineblock description]
     *
     * @return  void
     */
    private static function lineEndBlockWhite(string $filename, string $lineblock): void
    {
        global $file;

        $line = static::lineStartBlockWhite($filename);
        $line .= $lineblock;
        $line .= ");";
        $line .= "\n";

        fwrite($file, $line);
        fclose($file);
    } 

    /**
     * [linecomment description]
     *
     * @param   string  $comment  [$comment description]
     *
     * @return  string
     */
    private static function linecomment(string $comment): string
    {
        $line = "\n";
        $line .= "    ## ". $comment ."\n";
        $line .= "\n";

        return $line;
    } 

}