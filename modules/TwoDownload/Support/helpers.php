<?php

use Modules\TwoDownload\Library\Download;


if (! function_exists('topdownload_data'))
{
    /**
     * [topdownload_data description]
     *
     * @param   [type]  $form   [$form description]
     * @param   [type]  $ordre  [$ordre description]
     *
     * @return  [type]          [return description]
     */
    function topdownload_data($form, $ordre)
    {
        return Download::topdownload_data($form, $ordre);
    }
}
