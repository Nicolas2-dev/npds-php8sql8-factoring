<?php

/************************************************************************/
/* Theme for NPDS / Net Portal Dynamic System                           */
/*======================================================================*/
/* This theme use the NPDS theme-dynamic engine (Meta-Lang)             */
/*                                                                      */
/* Theme : npds-boost_sk 2015 by jpb                                    */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\system\config\Config;

$theme = 'npds-boost_sk';
$long_chain = '34'; // Nombre de caractères affichés avant troncature pour certains blocs

Config::set('npds.theme.long_chaine', 34);


// ne pas supprimer cette ligne / Don't remove this line
require_once('themes/themes-dynamic/theme.php');
// ne pas supprimer cette ligne / Don't remove this line
