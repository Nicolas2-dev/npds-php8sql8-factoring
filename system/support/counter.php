<?php

declare(strict_types=1);

namespace npds\system\support;

class counter
{

    /**
     * [counterUpadate description]
     *
     * @return  void    [return description]
     */
    public static function counterUpadate(): void
    {
        global $admin, $not_admin_count, $NPDS_Prefix;;

        if ((!$admin) or ($not_admin_count != 1)) {
            $user_agent = getenv("HTTP_USER_AGENT");

            if ((stristr($user_agent, "Nav")) 
            || (stristr($user_agent, "Gold")) 
            || (stristr($user_agent, "X11")) 
            || (stristr($user_agent, "Mozilla")) 
            || (stristr($user_agent, "Netscape")) 
            and (!stristr($user_agent, "MSIE")) 
            and (!stristr($user_agent, "SAFARI")) 
            and (!stristr($user_agent, "IPHONE")) 
            and (!stristr($user_agent, "IPOD")) 
            and (!stristr($user_agent, "IPAD")) 
            and (!stristr($user_agent, "ANDROID"))) 
                $browser = "Netscape";
            elseif (stristr($user_agent, "MSIE")) 
                $browser = "MSIE";
            elseif (stristr($user_agent, "Trident")) 
                $browser = "MSIE";
            elseif (stristr($user_agent, "Lynx")) 
                $browser = "Lynx";
            elseif (stristr($user_agent, "Opera")) 
                $browser = "Opera";
            elseif (stristr($user_agent, "WebTV")) 
                $browser = "WebTV";
            elseif (stristr($user_agent, "Konqueror")) 
                $browser = "Konqueror";
            elseif (stristr($user_agent, "Chrome")) 
                $browser = "Chrome";
            elseif (stristr($user_agent, "Safari")) 
                $browser = "Safari";
            elseif (preg_match('#([bB]ot|[sS]pider|[yY]ahoo)#', $user_agent)) 
                $browser = "Bot";
            else 
                $browser = "Other";

            if (stristr($user_agent, "Win")) 
                $os = "Windows";
            elseif ((stristr($user_agent, "Mac")) 
            || (stristr($user_agent, "PPC"))) 
                $os = "Mac";
            elseif (stristr($user_agent, "Linux")) 
                $os = "Linux";
            elseif (stristr($user_agent, "FreeBSD")) 
                $os = "FreeBSD";
            elseif (stristr($user_agent, "SunOS")) 
                $os = "SunOS";
            elseif (stristr($user_agent, "IRIX")) 
                $os = "IRIX";
            elseif (stristr($user_agent, "BeOS")) 
                $os = "BeOS";
            elseif (stristr($user_agent, "OS/2")) 
                $os = "OS/2";
            elseif (stristr($user_agent, "AIX")) 
                $os = "AIX";
            else 
                $os = "Other";

            sql_query("UPDATE " . $NPDS_Prefix . "counter SET count=count+1 WHERE (type='total' AND var='hits') OR (var='$browser' AND type='browser') OR (var='$os' AND type='os')");
        }
    }
}
