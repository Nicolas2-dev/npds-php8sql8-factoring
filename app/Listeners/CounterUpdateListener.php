<?php

namespace App\Listeners;

use Npds\Support\Facades\DB;
use Npds\Database\Query\Builder;
use Npds\Support\Facades\Config;
use App\Events\CounterUpdateEvent;


class CounterUpdateListener
{
    
    /**
     * 
     */
    private $config;


    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = Config::get('npds');
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CounterUpdateEvent  $event
     * @return void
     */
    public function handle(CounterUpdateEvent $event)
    {
        //$admin = Auth::guard('admin')->user();

        //if ((!$admin) or ($this->config['not_admin_count'])) {
        if ($this->config['not_admin_count']) {

            $user_agent = $event->request->server('HTTP_USER_AGENT');
        
            $browser = $this->get_browser($user_agent);
            $os = $this->get_os($user_agent);
        
            DB::table('counter')
                ->where(function (Builder $query) use ($browser, $os) {
                    return $query->where('type', '=', 'total')
                          ->where('var', '=', 'hits')
                          ->orWhere('var', '=', $browser)
                          ->orWhere('type', '=', $browser)
                          ->orWhere('var', '=', $os)
                          ->orWhere('type', '=', $os);
                })->update(['count' => DB::raw('count+1')]);
        }     
    }

    /** 
     * 
     */
    private function get_browser($user_agent)
    {
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
            and (!stristr($user_agent, "ANDROID"))
        )
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

        return $browser;
    }

    /** 
     * 
     */
    private function get_os($user_agent)
    {
        if (stristr($user_agent, "Win"))
            $os = "Windows";
        elseif ((stristr($user_agent, "Mac")) || (stristr($user_agent, "PPC")))
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

        return $os;
    }

}
