<?php
/************************************************************************/
/* NPDS DUNE : Net Portal Dynamic System .                              */
/* ===========================                                          */
/*                                                                      */
/* This version name NPDS Copyright (c) 2001-2023 by Philippe Brunier   */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
use npds\support\language\language;

//==> core required dont remove
echo '
      <script type="text/javascript" src="assets/shared/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
      <script type="text/javascript" src="assets/shared/bootstrap-table/dist/bootstrap-table.min.js"></script>
      <script type="text/javascript" src="assets/shared/bootstrap-table/dist/locale/bootstrap-table-'.language::language_iso(1,"-",1).'.min.js" async="async"></script>
      <script type="text/javascript" src="assets/shared/bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile.min.js" async="async"></script>
      <script type="text/javascript" src="assets/shared/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js" async="async"></script>
      <script type="text/javascript" src="assets/shared/tableExport.jquery.plugin-master/tableExport.js" async="async"></script>
      <script type="text/javascript" src="assets/js/js.cookie.js" async="async"></script>
      <script type="text/javascript" src="assets/shared/jquery-ui/jquery-ui.min.js" ></script>
      <script type="text/javascript" src="assets/js/bootbox.min.js" async="async"></script>
      <script type="text/javascript" src="assets/js/prism.js"></script>
      <script type="text/javascript">
         //<![CDATA[
            (tarteaucitron.job = tarteaucitron.job || []).push("vimeo");
            (tarteaucitron.job = tarteaucitron.job || []).push("youtube");
            (tarteaucitron.job = tarteaucitron.job || []).push("dailymotion");
            //tarteaucitron.user.gtagUa = ""; /*uncomment the line and add your gtag*/
            //tarteaucitron.user.gtagMore = function () { /* uncomment the line add here your optionnal gtag() */ };
            //(tarteaucitron.job = tarteaucitron.job || []).push("gtag");
         //]]
      </script>';
// <== core

// ==> page-time pseudo-module : If you want to show the time used to generate each page uncomment those lines
   /*
   $time = round(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],5); // php => 5.4 
   echo '
   <span class="small" id="build_time"><i class="bi bi-clock-history me-1"></i>'.language::aff_langue('[fr]Temps :[/fr][en]Time:[/en][de]Zeit in Sekunden[/de][es]Tiempo en segundos :[/es][zh]&#x5728;&#x51E0;&#x79D2;&#x949F;&#x7684;&#x65F6;&#x95F4; :[/zh] '.$time.' [fr]seconde(s)[/fr][en]second(s)[/en]').'</span>';
   */
// <== page-time pseudo-module

echo '
   </footer>
</div>
<script type="text/javascript" src="assets/js/npds_adapt.js"></script>';
?>