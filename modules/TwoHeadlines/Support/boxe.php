<?php

use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Modules\TwoThemes\Support\Facades\Theme;


if (! function_exists('headlines'))
{
    /**
     * Bloc HeadLines
     * syntaxe : function#headlines
     * params#ID_du_canal
     *
     * @param   string  $hid    [$hid description]
     * @param   bool    $block  [$block description]
     * @param   string|true            [ description]
     *
     * @return  string          [return description]
     */
    function headlines(?string $hid = '', string|bool $block = true)
    {
        if (file_exists("config/proxy.php")) {
            include("config/proxy.php");
        }

        if ($hid == '') {
            $result = DB::table('headlines')
                ->select('sitename', 'url', 'headlinesurl', 'hid')
                ->where('status', 1)
                ->get();
        } else {
            $result = DB::table('headlines')
                ->select('sitename', 'url', 'headlinesurl', 'hid')
                ->where('hid', $hid)
                ->where('status', 1)
                ->get();
        }

        foreach ($result as $headlines) { 
            $boxtitle = $headlines['sitename'];

            $cache_file = 'storage/cache/' . preg_replace('[^a-z0-9]', '', strtolower($headlines['sitename'])) .'_'. $headlines['hid'] .'.cache';
            $cache_time = 1200; //3600 origine

            $max_items = 6;
            $rss_timeout = 15;
            $rss_font = '<span class="small">';

            if ((!(file_exists($cache_file))) or (filemtime($cache_file) < (time() - $cache_time)) or (!(filesize($cache_file)))) {
                $rss = parse_url($headlines['url']);

                if (Config::get('two_core::config.rss_host_verif') == true) {
                    $verif = fsockopen($rss['host'], 80, $errno, $errstr, $rss_timeout);

                    if ($verif) {
                        fclose($verif);
                        $verif = true;
                    }
                } else {
                    $verif = true;
                }

                if (!$verif) {
                    $cache_file_sec = $cache_file . ".security";

                    if (file_exists($cache_file)) {
                        rename($cache_file, $cache_file_sec);
                    }

                    Theme::themesidebox($boxtitle, "Security Error");
                    return true;
                } else {
                    $long_chain = Theme::getConfig('config.long_chain');

                    if (!$long_chain) {
                        $long_chain = 15;
                    }

                    $fpwrite = fopen($cache_file, 'w');

                    if ($fpwrite) {
                        fputs($fpwrite, "<ul>\n");
                        $flux = simplexml_load_file($headlines['headlinesurl'], 'SimpleXMLElement', LIBXML_NOCDATA);

                        //ATOM//
                        if ($flux->entry) {
                            $j = 0;
                            $cont = '';

                            foreach ($flux->entry as $entry) {
                                if ($entry->content) {
                                    $cont = (string) $entry->content;
                                }

                                fputs($fpwrite, '<li><a href="'. (string) $entry->link['href'] .'" target="_blank" >'. (string) $entry->title .'</a><br />'. $cont .'</li>');

                                if ($j == $max_items) {
                                    break;
                                }
                                $j++;
                            }
                        }

                        if ($flux->{'item'}) {
                            $j = 0;
                            $cont = '';
                            foreach ($flux->item as $item) {
                                if ($item->description) {
                                    $cont = (string) $item->description;
                                }

                                fputs($fpwrite, '<li><a href="'. (string) $item->link['href'] .'"  target="_blank" >'. (string) $item->title .'</a><br /></li>');

                                if ($j == $max_items) {
                                    break;
                                }
                                $j++;
                            }
                        }

                        //RSS
                        if ($flux->{'channel'}) {
                            $j = 0;
                            $cont = '';
                            foreach ($flux->channel->item as $item) {
                                if ($item->description) {
                                    $cont = (string) $item->description;
                                }

                                fputs($fpwrite, '<li><a href="'. (string) $item->link .'"  target="_blank" >'. (string) $item->title .'</a><br />'. $cont .'</li>');

                                if ($j == $max_items) {
                                    break;
                                }
                                $j++;
                            }
                        }

                        $j = 0;
                        if ($flux->image) {
                            $ico = '<img class="img-fluid" src="'. $flux->image->url .'" />&nbsp;';
                        }

                        foreach ($flux->item as $item) {
                            fputs($fpwrite, '<li>'. $ico .'<a href="'. (string) $item->link .'" target="_blank" >'. (string) $item->title .'</a></li>');

                            if ($j == $max_items) {
                                break;
                            }
                            $j++;
                        }

                        fputs($fpwrite, "\n" .'</ul>');
                        fclose($fpwrite);
                    }
                }
            }
 
            if (file_exists($cache_file)) {
                ob_start();
                    readfile($cache_file);
                    $boxstuff = $rss_font . ob_get_contents() .'</span>';
                ob_end_clean();
            }

            $boxstuff .= '<div class="text-end"><a href="'. $headlines['url'] .'" target="_blank">'. __d('two_headlines', '("Lire la suite..."') .'</a></div>';

            if ($block) {
                Theme::themesidebox($boxtitle, $boxstuff);
                $boxstuff = '';
                return true;
            } else {
                return $boxstuff;
            }
        }
    }
}
