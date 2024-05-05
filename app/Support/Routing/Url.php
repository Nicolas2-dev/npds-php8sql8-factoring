<?php

declare(strict_types=1);

namespace App\Support\Routing;


class Url
{
 
    /**
     * Permet une redirection javascript en lieu et place de header("location: ...");
     *
     * @param   string  $urlx  [$urlx description]
     *
     * @return  void
     */
    public static function redirect_url(string $urlx): void
    {
        echo "<script type=\"text/javascript\">\n";
        echo "//<![CDATA[\n";
        echo "document.location.href='". site_url($urlx) ."';\n";
        echo "//]]>\n";
        echo "</script>";
    }
}
