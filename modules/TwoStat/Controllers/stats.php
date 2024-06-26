<?php

/************************************************************************/
/* DUNE by NPDS                                                         */
/* ===========================                                          */
/*                                                                      */
/* Based on PhpNuke 4.x source code                                     */
/*                                                                      */
/* NPDS Copyright (c) 2002-2022 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\str;
use npds\support\theme\theme;
use npds\system\config\Config;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

include("themes/default/header.php");

function generatePourcentageAndTotal($count, $total)
{
    $tab[] = str::wrh($count);
    $tab[] = substr(sprintf('%f', 100 * $count / $total), 0, 5);

    return $tab;
}

// = DB::table('')->select()->where('', )->orderBy('')->get();

$dkn = sql_query("SELECT type, var, count FROM " . $NPDS_Prefix . "counter ORDER BY type DESC");
while (list($type, $var, $count) = sql_fetch_row($dkn)) {
    
    if (($type == "total") && ($var == "hits"))
        $total = $count;
    elseif ($type == "browser") {
        if ($var == "Netscape")
            $netscape = generatePourcentageAndTotal($count, $total);
        elseif ($var == "MSIE")
            $msie = generatePourcentageAndTotal($count, $total);
        elseif ($var == "Konqueror")
            $konqueror = generatePourcentageAndTotal($count, $total);
        elseif ($var == "Opera")
            $opera = generatePourcentageAndTotal($count, $total);
        elseif ($var == "Lynx")
            $lynx = generatePourcentageAndTotal($count, $total);
        elseif ($var == "WebTV")
            $webtv = generatePourcentageAndTotal($count, $total);
        elseif ($var == "Chrome")
            $chrome = generatePourcentageAndTotal($count, $total);
        elseif ($var == "Safari")
            $safari = generatePourcentageAndTotal($count, $total);
        elseif ($var == "Bot")
            $bot = generatePourcentageAndTotal($count, $total);
        elseif (($type == "browser") && ($var == "Other"))
            $b_other = generatePourcentageAndTotal($count, $total);
    
    } elseif ($type == "os") {
        if ($var == "Windows")
            $windows = generatePourcentageAndTotal($count, $total);
        elseif ($var == "Mac")
            $mac = generatePourcentageAndTotal($count, $total);
        elseif ($var == "Linux")
            $linux = generatePourcentageAndTotal($count, $total);
        elseif ($var == "FreeBSD")
            $freebsd = generatePourcentageAndTotal($count, $total);
        elseif ($var == "SunOS")
            $sunos = generatePourcentageAndTotal($count, $total);
        elseif ($var == "IRIX")
            $irix = generatePourcentageAndTotal($count, $total);
        elseif ($var == "BeOS")
            $beos = generatePourcentageAndTotal($count, $total);
        elseif ($var == "OS/2")
            $os2 = generatePourcentageAndTotal($count, $total);
        elseif ($var == "AIX")
            $aix = generatePourcentageAndTotal($count, $total);
        elseif ($var == "Android")
            $andro = generatePourcentageAndTotal($count, $total);
        elseif ($var == "iOS")
            $ios = generatePourcentageAndTotal($count, $total);
        elseif (($type == "os") && ($var == "Other"))
            $os_other = generatePourcentageAndTotal($count, $total);
    }
}

echo '
    <h2>' . __d('two_stat', 'Statistiques') . '</h2>
    <div class="card card-body lead">
        <div>
        ' . __d('two_stat', 'Nos visiteurs ont visualisé') . ' <span class="badge bg-secondary">' . str::wrh($total) . '</span> ' . __d('two_stat', 'pages depuis le') . ' ' . Config::get('npds.startdate') . '
        </div>
    </div>
    <h3 class="my-4">' . __d('two_stat', 'Navigateurs web') . '</h3>
    <table data-toggle="table" data-mobile-responsive="true">
        <thead>
            <tr>
                <th data-sortable="true" >' . __d('two_stat', 'Navigateurs web') . '</th>
                <th data-sortable="true" data-halign="center" data-align="right" >%</th>
                <th data-align="right" ></th>
            </tr>
        </thead>
        <tbody>';

$imgtmp = $ibid = theme::theme_image('stats/explorer.gif') ? $ibid : 'assets/images/stats/explorer.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="MSIE_ico" loading="lazy"/> MSIE </td>
                <td>
                <div class="text-center small">' . $msie[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $msie[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $msie[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $msie[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/firefox.gif') ? $ibid : 'assets/images/stats/firefox.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="Mozilla_ico" loading="lazy"/> Mozilla </td>
                <td>
                <div class="text-center small">' . $netscape[1] . ' %</div>
                    <div class="progress bg-light">
                        <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $netscape[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $netscape[1] . '%; height:1rem;"></div>
                    </div>
                </td>
                <td> ' . $netscape[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/opera.gif') ? $ibid : 'assets/images/stats/opera.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="Opera_ico" loading="lazy"/> Opera </td>
                <td>
                <div class="text-center small">' . $opera[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $opera[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $opera[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $opera[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/chrome.gif') ? $ibid : 'assets/images/stats/chrome.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="Chrome_ico" loading="lazy"/> Chrome </td>
                <td>
                <div class="text-center small">' . $chrome[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $chrome[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $chrome[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $chrome[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/safari.gif') ? $ibid : 'assets/images/stats/safari.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="Safari_ico" loading="lazy"/> Safari </td>
                <td>
                <div class="text-center small">' . $safari[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $safari[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $safari[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $safari[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/webtv.gif') ? $ibid : 'assets/images/stats/webtv.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '"  alt="WebTV_ico" loading="lazy"/> WebTV </td>
                <td>
                <div class="text-center small">' . $webtv[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $webtv[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $webtv[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $webtv[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/konqueror.gif') ? $ibid : 'assets/images/stats/konqueror.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="Konqueror_ico" loading="lazy"/> Konqueror </td>
                <td>
                <div class="text-center small">' . $konqueror[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $konqueror[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $konqueror[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $konqueror[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/lynx.gif') ? $ibid : 'assets/images/stats/lynx.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="Lynx_ico" loading="lazy"/> Lynx </td>
                <td>
                <div class="text-center small">' . $lynx[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $lynx[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $lynx[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $lynx[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/altavista.gif') ? $ibid : 'assets/images/stats/altavista.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="' . __d('two_stat', 'Moteurs de recherche') . '_ico" /> ' . __d('two_stat', 'Moteurs de recherche') . ' </td>
                <td>
                <div class="text-center small">' . $bot[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $bot[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $bot[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $bot[0] . '</td>
            </tr>
            <tr>
                <td><i class="fa fa-question fa-3x align-middle"></i> ' . __d('two_stat', 'Inconnu') . ' </td>
                <td>
                <div class="text-center small">' . $b_other[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $b_other[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $b_other[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $b_other[0] . '</td>
            </tr>
        </tbody>
    </table>
    <br />
    <h3 class="my-4">' . __d('two_stat', 'Systèmes d\'exploitation') . '</h3>
    <table data-toggle="table" data-mobile-responsive="true" >
        <thead>
            <tr>
                <th data-sortable="true" >' . __d('two_stat', 'Systèmes d\'exploitation') . '</th>
                <th data-sortable="true" data-halign="center" data-align="right">%</th>
                <th data-align="right"></th>
            </tr>
        </thead>
        <tbody>';

$imgtmp = $ibid = theme::theme_image('stats/windows.gif') ? $ibid : 'assets/images/stats/windows.gif';

echo '
            <tr>
                <td ><img src="' . $imgtmp . '"  alt="Windows" loading="lazy"/>&nbsp;Windows</td>
                <td>
                <div class="text-center small">' . $windows[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $windows[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $windows[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $windows[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/linux.gif') ? $ibid : 'assets/images/stats/linux.gif';

echo '
            <tr>
                <td ><img src="' . $imgtmp . '"  alt="Linux" loading="lazy"/>&nbsp;Linux</td>
                <td>
                <div class="text-center small">' . $linux[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $linux[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $linux[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $linux[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/mac.gif') ? $ibid : 'assets/images/stats/mac.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '"  alt="Mac/PPC" loading="lazy"/>&nbsp;Mac/PPC</td>
                <td>
                <div class="text-center small">' . $mac[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $mac[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $mac[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $mac[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/bsd.gif') ? $ibid : 'assets/images/stats/bsd.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '"  alt="FreeBSD" loading="lazy"/>&nbsp;FreeBSD</td>
                <td>
                <div class="text-center small">' . $freebsd[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $freebsd[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $freebsd[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $freebsd[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/sun.gif') ? $ibid : 'assets/images/stats/sun.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '"  alt="SunOS" loading="lazy"/>&nbsp;SunOS</td>
                <td>
                <div class="text-center small">' . $sunos[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $sunos[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $sunos[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $sunos[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/irix.gif') ? $ibid : 'assets/images/stats/irix.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '"  alt="IRIX" loading="lazy"/>&nbsp;IRIX</td>
                <td>
                <div class="text-center small">' . $irix[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $irix[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $irix[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $irix[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/be.gif') ? $ibid : 'assets/images/stats/be.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="BeOS" loading="lazy"/>&nbsp;BeOS</td>
                <td>
                <div class="text-center small">' . $beos[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $beos[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $beos[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $beos[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/os2.gif') ? $ibid : 'assets/images/stats/os2.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="OS/2" loading="lazy"/>&nbsp;OS/2</td>
                <td>
                <div class="text-center small">' . $os2[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $os2[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $os2[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $os2[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/aix.gif') ? $ibid : 'assets/images/stats/aix.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="AIX" loading="lazy"/>&nbsp;AIX</td>
                <td>
                <div class="text-center small">' . $aix[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $aix[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $aix[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $aix[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/android.gif') ? $ibid : 'assets/images/stats/android.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="Android" loading="lazy"/>&nbsp;Android</td>
                <td>
                <div class="text-center small">' . $andro[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $andro[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $andro[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $andro[0] . '</td>
            </tr>';

$imgtmp = $ibid = theme::theme_image('stats/ios.gif') ? $ibid : 'assets/images/stats/ios.gif';

echo '
            <tr>
                <td><img src="' . $imgtmp . '" alt="Ios" loading="lazy"/> Ios</td>
                <td>
                <div class="text-center small">' . $ios[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $ios[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $ios[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $ios[0] . '</td>
            </tr>
            <tr>
                <td><i class="fa fa-question fa-3x align-middle"></i>&nbsp;' . __d('two_stat', 'Inconnu') . '</td>
                <td>
                <div class="text-center small">' . $os_other[1] . ' %</div>
                <div class="progress bg-light">
                    <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="' . $os_other[1] . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $os_other[1] . '%; height:1rem;"></div>
                </div>
                </td>
                <td>' . $os_other[0] . '</td>
            </tr>
        </tbody>
    </table>
    <h3 class="my-4">' . __d('two_stat', 'Thème(s)') . '</h3>
    <table data-toggle="table" data-striped="true">
        <thead>
            <tr>
                <th data-sortable="true" data-halign="center">' . __d('two_stat', 'Thème(s)') . '</th>
                <th data-halign="center" data-align="right">' . __d('two_stat', 'Nombre d\'utilisateurs par thème') . '</th>
                <th data-halign="center">' . __d('two_stat', 'Status') . '</th>
            </tr>
        </thead>
        <tbody>';

// = DB::table('')->select()->where('', )->orderBy('')->get();

$resultX = sql_query("SELECT DISTINCT(theme) FROM " . $NPDS_Prefix . "users");


while (list($themelist) = sql_fetch_row($resultX)) {
    if ($themelist != '') {
        $ibix = explode('+', $themelist);
        $T_exist = is_dir("themes/$ibix[0]") ? '' : '<span class="text-danger">' . __d('two_stat', 'Ce fichier n\'existe pas ...') . '</span>';

        if ($themelist == Config::get('npds.Default_Theme')) {

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT uid FROM " . $NPDS_Prefix . "users WHERE theme='$themelist'");
            $themeD1 = $result ? sql_num_rows($result) : 0;


            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT uid FROM " . $NPDS_Prefix . "users WHERE theme=''");
            $themeD2 = $result ? sql_num_rows($result) : 0;

            echo '
                <tr>
                <td>' . $themelist . ' <b>(' . __d('two_stat', 'par défaut') . ')</b></td>
                <td><b>' . str::wrh(($themeD1 + $themeD2)) . '</b></td>
                <td>' . $T_exist . '</td>
                </tr>';
        } else {

            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result = sql_query("SELECT uid FROM " . $NPDS_Prefix . "users WHERE theme='$themelist'");
            $themeU = $result ? sql_num_rows($result) : 0;

            echo '
                <tr>';
            echo substr($ibix[0], -3) == "_sk" ? '
                <td>' . $themelist . '</td>' : '
                <td>' . $ibix[0] . '</td>';
            echo '
                <td><b>' . str::wrh($themeU) . '</b></td>
                <td>' . $T_exist . '</td>
                </tr>';
        }
    }
}
echo '
        </tbody>
    </table>';

$result = sql_query("SELECT uid FROM " . $NPDS_Prefix . "users");
$unum = $result ? sql_num_rows($result) - 1 : 0;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$result = sql_query("SELECT groupe_id FROM " . $NPDS_Prefix . "groupes");
$gnum = $result ? sql_num_rows($result) : 0;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$result = sql_query("SELECT sid FROM " . $NPDS_Prefix . "stories");
$snum = $result ? sql_num_rows($result) : 0;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$result = sql_query("SELECT aid FROM " . $NPDS_Prefix . "authors");
$anum = $result ? sql_num_rows($result) : 0;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$result = sql_query("SELECT post_id FROM " . $NPDS_Prefix . "posts WHERE forum_id<0");
$cnum = $result ? sql_num_rows($result) : 0;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$result = sql_query("SELECT secid FROM " . $NPDS_Prefix . "sections");
$secnum = $result ? sql_num_rows($result) : 0;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$result = sql_query("SELECT artid FROM " . $NPDS_Prefix . "seccont");
$secanum = $result ? sql_num_rows($result) : 0;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$result = sql_query("SELECT qid FROM " . $NPDS_Prefix . "queue");
$subnum = $result ? sql_num_rows($result) : 0;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$result = sql_query("SELECT topicid FROM " . $NPDS_Prefix . "topics");
$tnum = $result ? sql_num_rows($result) : 0;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$result = sql_query("SELECT lid FROM " . $NPDS_Prefix . "links_links");
$links = $result ? sql_num_rows($result) : 0;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$result = sql_query("SELECT cid FROM " . $NPDS_Prefix . "links_categories");
$cat1 = $result ? sql_num_rows($result) : 0;

// = DB::table('')->select()->where('', )->orderBy('')->get();

$result = sql_query("SELECT sid FROM " . $NPDS_Prefix . "links_subcategories");
$cat2 = $result ? sql_num_rows($result) : 0;
$cat = $cat1 + $cat2;

echo '
    <h3 class="my-4">' . __d('two_stat', 'Statistiques diverses') . '</h3>
    <ul class="list-group">
        <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-user fa-2x text-muted me-1"></i>' . __d('two_stat', 'Utilisateurs enregistrés') . ' <span class="badge bg-secondary ms-auto">' . str::wrh($unum) . ' </span></li>
        <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-users fa-2x text-muted me-1"></i>' . __d('two_stat', 'Groupe') . ' <span class="badge bg-secondary ms-auto">' . str::wrh($gnum) . ' </span></li>
        <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-user-edit fa-2x text-muted me-1"></i>' . __d('two_stat', 'Auteurs actifs') . ' <span class="badge bg-secondary ms-auto">' . str::wrh($anum) . ' </span></li>';

$imgtmp = $ibid = theme::theme_image('stats/postnew.png') ? $ibid : 'assets/images/admin/postnew.png';

echo '
        <li class="list-group-item d-flex justify-content-start align-items-center"><img class="me-1" src="' . $imgtmp . '" alt="" loading="lazy"/>' . __d('two_stat', 'Articles publiés') . ' <span class="badge bg-secondary ms-auto">' . str::wrh($snum) . ' </span></li>';

$imgtmp = $ibid = theme::theme_image('stats/topicsman.png') ? $ibid : 'assets/images/admin/topicsman.png';

echo '
        <li class="list-group-item d-flex justify-content-start align-items-center"><img class="me-1" src="' . $imgtmp . '" alt="" loading="lazy"/>' . __d('two_stat', 'Sujets actifs') . ' <span class="badge bg-secondary ms-auto">' . str::wrh($tnum) . ' </span></li>
        <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-comments fa-2x text-muted me-1"></i>' . __d('two_stat', 'Commentaires') . ' <span class="badge bg-secondary ms-auto">' . str::wrh($cnum) . ' </span></li>';

$imgtmp = $ibid = theme::theme_image('stats/sections.png') ? $ibid : 'assets/images/admin/sections.png';

echo '
        <li class="list-group-item d-flex justify-content-start align-items-center"><img class="me-1" src="' . $imgtmp . '" alt="" loading="lazy"/>' . __d('two_stat', 'Rubriques spéciales') . ' <span class="badge bg-secondary ms-auto">' . str::wrh($secnum) . ' </span></li>';

$imgtmp = $ibid = theme::theme_image('stats/sections.png') ? $ibid : 'assets/images/admin/sections.png';

echo '
        <li class="list-group-item d-flex justify-content-start align-items-center"><img class="me-1" src="' . $imgtmp . '" alt="" loading="lazy"/>' . __d('two_stat', 'Articles présents dans les rubriques') . ' <span class="badge bg-secondary ms-auto">' . str::wrh($secanum) . ' </span></li>';
echo '
        <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-link fa-2x text-muted me-1"></i>' . __d('two_stat', 'Liens présents dans la rubrique des liens web') . ' <span class="badge bg-secondary ms-auto">' . str::wrh($links) . ' </span></li>
        <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-link fa-2x text-muted me-1"></i>' . __d('two_stat', 'Catégories dans la rubrique des liens web') . ' <span class="badge bg-secondary ms-auto">' . str::wrh($cat) . ' </span></li>';

$imgtmp = $ibid = theme::theme_image('stats/submissions.png') ? $ibid : 'assets/images/admin/submissions.png';

echo '
        <li class="list-group-item d-flex justify-content-start align-items-center"><img class="me-1" src="' . $imgtmp . '"  alt="" />' . __d('two_stat', 'Article en attente d\'édition') . ' <span class="badge bg-secondary ms-auto">' . str::wrh($subnum) . ' </span></li>
        <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-cogs fa-2x text-muted me-1"></i>Version Num <span class="badge bg-danger ms-auto">' . Config::get('versioning.Version_Num') . '</span></li>
        <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-cogs fa-2x text-muted me-1"></i>Version Id <span class="badge bg-danger ms-auto">' . Config::get('versioning.Version_ID') . '</span></li>
        <li class="list-group-item d-flex justify-content-start align-items-center"><i class="fa fa-cogs fa-2x text-muted me-1"></i>Version Sub <span class="badge bg-danger ms-auto">' . Config::get('versioning.Version_Sub') . '</span></li>
    </ul>
    <br />
    <p class="text-center"><a href="http://www.npds.org" >http://www.npds.org</a> - French Portal Generator Gnu/Gpl Licence</p><br />';

include("themes/default/footer.php");
