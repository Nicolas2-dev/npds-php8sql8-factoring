<?php

use Two\Support\Facades\DB;
use Two\Support\Facades\Config;
use Modules\TwoThemes\Support\Facades\Theme;
use Modules\TwoCore\Support\Facades\Language;


if (! function_exists('pollMain'))
{
    /**
     * Construit le bloc sondage
     *
     * @param   int  $pollID     [$pollID description]
     * @param   string              [ description]
     * @param   int     $pollClose  [$pollClose description]
     *
     * @return  void
     */
    function pollMain(int $pollID, string|int $pollClose): void
    {
        global $boxTitle, $boxContent;

        if (!isset($pollID)) {
            $pollID = 1;
        }

        if (!isset($url)) {
            $url = sprintf(site_url('pollBooth.php?op=results&amp;pollID=%d'), $pollID);
        }

        $boxContent = '
        <form action="'. site_url('pollBooth.php') .'" method="post">
        <input type="hidden" name="pollID" value="'. $pollID .'" />
        <input type="hidden" name="forwarder" value="'. $url .'" />';

        $poll_desc = DB::table('poll_desc')
                        ->select('pollTitle', 'voters')
                        ->where('pollID', $pollID)
                        ->first();
        
        global $block_title;
        $boxTitle = $block_title == '' ? translate("Sondage") :  $block_title;

        $boxContent .= '<legend>'. Language::aff_langue($poll_desc->pollTitle) .'</legend>';

        $result = DB::table('poll_data')
                ->select('pollID', 'optionText', 'optionCount', 'voteID')
                ->where('pollID', $pollID)
                ->where('optionText', '<>', '')
                ->orderBy('voteID')
                ->get();
                
        $sum = 0;
        $j = 0;

        if (!$pollClose) {
            $boxContent .= '<div class="mb-3">';

            foreach ($result as $poll_data) {
                $boxContent .= '
                <div class="form-check">
                    <input class="form-check-input" type="radio" id="voteID'. $j .'" name="voteID" value="'. $poll_data->voteID .'" />
                    <label class="form-check-label d-block" for="voteID'. $j .'" >'. Language::aff_langue($poll_data->optionText) .'</label>
                </div>';
                $sum = $sum + $poll_data->optionCount;
                $j++;
            }

            $boxContent .= '</div>';
        } else {
            foreach ($result as $poll_data) {
                $boxContent .= '&nbsp;'. Language::aff_langue($poll_data->optionText) .'<br />';
                $sum = $sum + $poll_data->optionCount;
            }
        }

        settype($inputvote, 'string');

        if (!$pollClose) {
            $inputvote = '<button class="btn btn-outline-primary btn-sm btn-block" type="submit" value="'. translate("Voter") .'" title="'. translate("Voter") .'" ><i class="fa fa-check fa-lg"></i> '. translate("Voter") .'</button>';
        }

        $boxContent .= '
        <div class="mb-3">'. $inputvote .'</div>
        </form>
        <a href="'. site_url('pollBooth.php?op=results&amp;pollID='. $pollID) .'" title="'. translate("Résultats") .'">'. translate("Résultats") .'</a>&nbsp;&nbsp;<a href="'. site_url('pollBooth.php') .'">'. translate("Anciens sondages") .'</a>
        <ul class="list-group mt-3">
        <li class="list-group-item">'. translate("Votes : ") .' <span class="badge rounded-pill bg-secondary float-end">'. $sum .'</span></li>';

        if (Config::get('two_core::config.pollcomm')) {
            if (file_exists("modules/comments/config/pollBoth.conf.php")) {
                include("modules/comments/config/pollBoth.conf.php");
            }

            $numcom = DB::table('posts')
                        ->select('*')
                        ->where('forum_id', $forum)
                        ->where('topic_id', $pollID)
                        ->where('post_aff', 1)
                        ->count();

            $boxContent .= '<li class="list-group-item">'. translate("Commentaire(s) : ") .' <span class="badge rounded-pill bg-secondary float-end">'. $numcom .'</span></li>';
        }

        $boxContent .= '</ul>';

        Theme::themesidebox($boxTitle, $boxContent);
    }
}
