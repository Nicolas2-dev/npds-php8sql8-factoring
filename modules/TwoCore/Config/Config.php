<?php

/*
|--------------------------------------------------------------------------
| Module Configuration
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Configuration for the Module.
*/

return array(


    ## General Site Configuration

    /**
    * Select the parse function you want to use for preference
    *
    */
    'parse'  => 0,

    /**
    * PHP > 5.x : default 0 / PHP < 5.x sending compressed html with zlib : 1 - be careful
    *
    */
    'gzhandler'  => 0,

    /**
    * Duration in hour for Admin cookie (default 24)
    *
    */
    'admin_cook_duration'  => 240,

    /**
    * Duration in hour for Admin cookie (default 24)
    *
    */
    'user_cook_duration'  => 8000,

    /**
    * Your Site Name
    *
    */
    'sitename'  => 'npds',

    /**
    * Your Site Phrase for the Title (html Title Tag) off the HTML Page
    *
    */
    'Titlesitename'  => 'test',

    /**
    * Complete URL for your site (Do not put / at end)
    *
    */
    'nuke_url'  => 'http://www.two.local',

    /**
    * Logo for Printer Friendly Page (It's good to have a Black/White graphic)
    *
    */
    'site_logo'  => 'assets/images/npds_p.gif',

    /**
    * Your site's slogan
    *
    */
    'slogan'  => 'v 8.2',

    /**
    * Start Date to display in Statistic Page
    *
    */
    'startdate'  => '02/03/2024',

    /**
    * Allow Anonymous to Post Comments? (1=Yes 0=No)
    *
    */
    'anonpost'  => 1,

    /**
    * Maximum Number off Comments per user (24H)
    *
    */
    'troll_limit'  => 5,

    /**
    * Moderation of comments
    *
    */
    'moderate'  => 1,

    /**
    * Allow only Moderator and Admin to Post News? (1=Yes 0=No)
    *
    */
    'mod_admin_news'  => 0,

    /**
    * Don't record Admin's Hits in stats (1=Yes=>don't rec 0=No=>rec)
    *
    */
    'not_admin_count'  => 1,

    /**
    * Default Theme for your site (See /themes directory for the complete list, case sensitive!)
    *
    */
    'Default_Theme'  => 'TwoFrontend',

    /**
    * Default Skin for Theme ... with skins (See /themes/_skins directory for the complete list, case sensitive!)
    *
    */
    'Default_Skin'  => 'default',

    /**
    * Default Page for your site (default : index.php but you can use : topics.php, links.php ...)
    *
    */
    'Start_Page'  => 'index.php?op=edito',

    /**
    * Messages for all footer pages (Can include HTML code)
    *
    */
    'foot1'  => '<a href="admin.php" ><i class="fa fa-cogs fa-2x me-3 align-middle" title="Admin" data-bs-toggle="tooltip"></i></a>
    <a href="https://www.mozilla.org/fr/" target="_blank"><i class="fab fa-firefox fa-2x  me-1 align-middle"  title="get Firefox" data-bs-toggle="tooltip"></i></a>
    <a href="static.php?op=charte.html&amp;npds=0&amp;metalang=1">Charte</a> 
    - <a href="modules.php?ModPath=contact&amp;ModStart=contact" class="me-3">Contact</a> 
    <a href="backend.php" target="_blank" ><i class="fa fa-rss fa-2x  me-3 align-middle" title="RSS 1.0" data-bs-toggle="tooltip"></i></a>&nbsp;<a href="https://github.com/npds/npds_dune" target="_blank"><i class="fab fa-github fa-2x  me-3 align-middle"  title="NPDS Dune on Github ..." data-bs-toggle="tooltip"></i></a>',

    /**
    * Messages for all footer pages (Can include HTML code)
    *
    */
    'foot2'  => 'Tous les Logos et Marques sont d&eacute;pos&eacute;s, les commentaires sont sous la responsabilit&eacute; de ceux qui les ont publi&eacute;s, le reste &copy; <a href=\"http://www.npds.org\" target=\"_blank\" >NPDS</a>',

    /**
    * Messages for all footer pages (Can include HTML code)
    *
    */
    'foot3'  => '',

    /**
    * Messages for all footer pages (Can include HTML code)
    *
    */
    'foot4'  => '',

    /**
    * Anonymous users Default Name
    *
    */
    'anonymous'  => 'Visiteur',

    /**
    * Minimum character for users passwords
    *
    */
    'minpass'  => 8,

    /**
    * Number off user showed in memberslist page
    *
    */
    'show_user'  => 20,


    ## General Stories Options

    /**
    * How many items in Top Page?
    *
    */
    'top'  => 10,

    /**
    * How many stories to display in Home Page?
    *
    */
    'storyhome'  => 5,

    /**
    * How many stories in Old Articles Box?
    *
    */
    'oldnum'  => 10,


    ## Banners/Advertising Configuration

    /**
    * Activate Banners Ads for your site? (1=Yes 0=No)
    *
    */
    'banners'  => 1,

    /**
    * Write your IP number to not count impressions, be fair about this!
    *
    */
    'myIP'  => '1.1.1.100',


    ## XML/RDF Backend Configuration & Social Networks

    /**
    * Backend title, can be your site's name and slogan
    *
    */
    'backend_title'  => 'NPDS',

    /**
    * Language format of your site
    *
    */
    'backend_language'  => 'fr-FR',

    /**
    * Image logo for your site
    *
    */
    'backend_image'  => '',

    /**
    * Image logo width
    *
    */
    'backend_width'  => 90,

    /**
    * Image logo height
    *
    */
    'backend_height'  => 30,

    /**
    * Activate ultramode plain text and XML files backend syndication? (1=Yes 0=No). locate in /cache directory
    *
    */
    'ultramode'  => 1,

    /**
    * Activate the Twitter syndication? (1=Yes 0=No).
    *
    */
    'npds_twi'  => 0,

    /**
    * Activate the Facebook syndication? (1=Yes 0=No).
    *
    */
    'npds_fcb'  => 0,


    ## Site Language Preferences

    /**
    * Language of your site (You need to have lang-xxxxxx.php file for your selected language in the /language directory of your site)
    *
    */
    'language'  => 'fr',

    /**
    * Locale configuration to correctly display date with your country format. (See /usr/share/locale)
    *
    */
    'multi_langue'  => false,

    /**
    * Locale configuration to correctly display date with your GMT offset.
    *
    */
    'locale'  => 'fr_FR.UTF8',

    /**
    * HH:MM where Day become.
    *
    */
    'gmt'  => 0,

    /**
    * HH:MM where Night become.
    *
    */
    'lever'  => '08:00',

    /**
    * Activate Multi-langue NPDS'capability.
    *
    */
    'coucher'  => '20:00',


    ## Web Links Preferences

    /**
    * How many links to show on each page?
    *
    */
    'perpage'  => 10,

    /**
    * How many hits need a link to be listed as popular?
    *
    */
    'popular'  => 10,

    /**
    * How many links to display in the New Links Page?
    *
    */
    'newlinks'  => 10,

    /**
    * How many links to display in The Best Links Page? (Most Popular)
    *
    */
    'toplinks'  => 10,

    /**
    * How many links to display on each search result page?
    *
    */
    'linksresults'  => 10,

    /**
    * Is Anonymous autorise to post new links? (0=Yes 1=No)
    *
    */
    'links_anonaddlinklock'  => 0,

    /**
    * Activate Logo on Main web Links Page (1=Yes 0=No)
    *
    */
    'linkmainlogo'  => 0,

    /**
    * Activate Icon for New Categorie on Main web Links Page (1=Yes 0=No)
    *
    */
    'OnCatNewLink'  => 1,


    ## Function Mail and Notification of News Submissions

    /**
    * Site Administrator's Email
    *
    */
    'adminmail'  => 'nicolas.l.devoy@gmail.com',

    /**
    * What Mail function to be used (1=mail, 2=email)
    *
    */
    'mail_fonction'  => 2,

    /**
    * Notify you each time your site receives a news submission? (1=Yes 0=No)
    *
    */
    'notify'  => 1,

    /**
    * Email, address to send the notification
    *
    */
    'notify_email'  => 'nicolas.l.devoy@gmail.com',

    /**
    * Email subject
    *
    */
    'notify_subject'  => 'Nouvelle soumission',

    /**
    * Email body, message
    *
    */
    'notify_message'  => 'Le site a recu une nouvelle soumission !',

    /**
    * account name to appear in From field of the Email
    *
    */
    'notify_from'  => 'nicolas.l.devoy@gmail.com',


    ## Survey/Polls Config

    /**
    * Number of maximum options for each poll
    *
    */
    'maxOptions'  => 12,

    /**
    * Set cookies to prevent visitors vote twice in a period of 24 hours? (0=Yes 1=No)
    *
    */
    'setCookies'  => 1,

    /**
    * Activate comments in Polls? (1=Yes 0=No)
    *
    */
    'pollcomm'  => 0,


    ## Some Graphics Options

    /**
    * Topics images path (put / only at the end, not at the begining)
    *
    */
    'tipath'  => 'images/topics/',

    /**
    * User images path (put / only at the end, not at the begining)
    *
    */
    'userimg'  => 'images/menu/',

    /**
    * Administration system images path (put / only at the end, not at the begining)
    *
    */
    'adminimg'  => 'images/admin/',

    /**
    * Activate short Administration Menu? (1=Yes 0=No)
    *
    */
    'short_menu_admin'  => 1,

    /**
    * Activate graphic menu for Administration Menu? (1=Yes 0=No)
    *
    */
    'admingraphic'  => 1,

    /**
    * Image Files'extesion for admin menu (default: gif)
    *
    */
    'admf_ext'  => 'png',

    /**
    * How many articles to show in the admin section?
    *
    */
    'admart'  => 10,


    ## HTTP Referers Options

    /**
    * Activate HTTP referer logs to know who is linking to our site? (1=Yes 0=No)
    *
    */
    'httpref'  => 0,

    /**
    * Maximum number of HTTP referers to store in the Database (Try to not set this to a high number, 500 ~ 1000 is Ok)
    *
    */
    'httprefmax'  => 1,


    ## Miscelaneous Options

    /**
    * Activate Avatar? (1=Yes 0=No)
    *
    */
    'smilies'  => 1,

    /**
    * Maximum size for uploaded avatars in pixel (width*height)
    *
    */
    'avatar_size'  => '80*100',

    /**
    * Activate Short User registration (without ICQ, MSN, ...)? (1=Yes 0=No)
    *
    */
    'short_user'  => 1,

    /**
    * Make the members List Private (only for members) or Public (Private=Yes Public=No)
    *
    */
    'member_list'  => 1,

    /**
    * Witch category do you want to show first in download section?
    *
    */
    'download_cat'  => 'Tous',

    /**
    * Allow automated new-user creation (sending email and allowed connection)
    *
    */
    'AutoRegUser'  => 1,

    /**
    * For transform reviews like \"gold book\" (1=Yes, 0=no)
    *
    */
    'short_review'  => 0,

    /**
    * Allow your members to subscribe to topics, ... (1=Yes, 0=no)
    *
    */
    'subscribe'  => 1,

    /**
    * Allow members to hide from other members, ... (1=Yes, 0=no)
    *
    */
    'member_invisible'  => 0,

    /**
    * Allow you to close New Member Registration (from Gawax Idea), ... (1=Yes, 0=no)
    *
    */
    'CloseRegUser'  => 0,

    /**
    * Allow user to choose alone the password (1=Yes, 0=no)
    *
    */
    'memberpass'  => 1,


    ## HTTP Miscelaneous Options

    /**
    * Activate the validation of the existance of a web on Port 80 for Headlines (true=Yes false=No)
    *
    */
    'rss_host_verif'  => false,

    /**
    * Activate the Advance Caching Meta Tag (pragma ...) (true=Yes false=No)
    *
    */
    'cache_verif'  => true,

    /**
    * Activate the DNS resolution for posts (forum ...), IP-Ban, ... (true=Yes false=No)
    *
    */
    'dns_verif'  => false,


    ## SYSTEM Miscelaneous Options

    /**
    * Determine the maximum size for one file in the SaveMysql process
    *
    */
    'savemysql_size'  => 256,

    /**
    * Type of Myql process (1, 2 or 3)
    *
    */
    'savemysql_mode'  => 1,

    /**
    * true=Yes or false=No to use tiny_mce Editor or NO Editor
    *
    */
    'tiny_mce'  => true,

    /**
     * [description]
     */
    'packageTwoThemes' => 'TwoThemes',
);

