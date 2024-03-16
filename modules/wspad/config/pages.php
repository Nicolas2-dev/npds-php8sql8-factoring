<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Collab WS-Pad 1.5 by Developpeur and Jpb                             */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
global $nuke_url;
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad*']['title'] = "[fr]WS-Pad[/fr][en]WS-PAd[/en][es]WS-Pad[/es][de]WS-Pad[/de][zh]WS-Pad[/zh]+|$title+";
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad*']['run'] = "yes";
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad*']['blocs'] = "0";
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad*']['TinyMce'] = 1;
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad*']['TinyMce-theme'] = "full+setup";
$PAGES['modules.php?ModPath=' . $ModPath . '&ModStart=wspad']['css'] = [$nuke_url . "/assets/shared/bootstrap/dist/css/bootstrap-icons.css+"];
