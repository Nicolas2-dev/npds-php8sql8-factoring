<?php

declare(strict_types=1);

namespace npds\system\routing;

class url
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
        echo "document.location.href='" . $urlx . "';\n";
        echo "//]]>\n";
        echo "</script>";
    }
}
