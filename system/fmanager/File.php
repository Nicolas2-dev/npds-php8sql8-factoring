<?php
/************************************************************************/
/* NPDS V : Net Portal Dynamic System .                                 */
/* ===========================                                          */
/*                                                                      */
/* File Class Manipulation                                              */
/* NPDS Copyright (c) 2002-2019 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

declare(strict_types=1);

namespace npds\system\fmanager;

use npds\system\theme\theme;

class File
{
    /**
     * [$Url description]
     *
     * @var [type]
     */
    private string $Url = '';

    /**
     * [$Extention description]
     *
     * @var [type]
     */
    private string $Extention = '';

    /**
     * [$Size description]
     *
     * @var [type]
     */
    private int $Size = 0;


    /**
     * [__construct description]
     *
     * @param   string  $Url  [$Url description]
     *
     */
    public function __construct(string $Url)
    {
        $this->Url = $Url;
    }

    /**
     * [Size description]
     *
     * @return  void
     */
    function Size(): void
    {
        $this->Size = @filesize($this->Url);
    }

    /**
     * [Extention description]
     *
     * @return  void
     */
    function Extention(): void
    {
        $extension = strtolower(substr(strrchr($this->Url, '.'), 1));

        $this->Extention = $extension;
    }

    /**
     * [Affiche_Size description]
     *
     * @param   string    $Format  [$Format description]
     *
     * @return  string|int
     */
    function Affiche_Size(string $Format = "CONVERTI"): string|int
    {
        $this->Size();

        if (!$this->Size) {
            return '<span class="text-danger"><strong>?</strong></span>';
        }

        switch ($Format) {
                // en kilo/mega ou giga
            case "CONVERTI":
                // return ($this->pretty_Size($this->Size));
                return ('!!bug!!');
                break;

                // en octet
            case "NORMAL":
                return $this->Size;
                break;
        }
    }

    /**
     * [Affiche_Extention description]
     *
     * @param   string  $Format  [$Format description]
     *
     * @return  string
     */
    function Affiche_Extention(string $Format): string
    {
        $this->Extention();

        switch ($Format) {
            case "IMG":
                if ($ibid = theme::theme_image("upload/file_types/" . $this->Extention . ".gif")) {
                    $imgtmp = $ibid;
                } else {
                    $imgtmp = "assets/images/upload/file_types/" . $this->Extention . ".gif";
                }

                if (@file_exists($imgtmp)) {
                    return '<img src="' . $imgtmp . '" />';
                } else {
                    return '<img src="assets/images/upload/file_types/unknown.gif" />';
                }
                break;

            case "webfont":
                return '
                    <span class="fa-stack">
                    <i class="fa fa-file fa-stack-2x"></i>
                    <span class="fa-stack-1x filetype-text">' . $this->Extention . '</span>
                    </span>';
                break;
        }
    }
}
