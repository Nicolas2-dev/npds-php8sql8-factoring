<?php

use Two\Support\Facades\DB;
use Two\Support\Facades\Cache;
use Two\Support\Facades\Config;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoThemes\Support\Facades\Theme;


if (! function_exists('online'))
{
    /**
     * Bloc Online (Who_Online)
     * syntaxe : function#online
     *
     * @return  void    [return description]
     */
    function online(): void
    {
        $ip = getip();

        $cookie = User::cookieUser(1);
        $username = isset($cookie) ? $cookie : '';

        if ($username == '') {
            $username = $ip;
            $guest = 1;
        } else {
            $guest = 0;
        }

        DB::table('session')->where('time', '<', (time() - 300))->delete();

        $result = DB::table('session')
                    ->select('time')
                    ->where('username', $username)
                    ->first();

        if ($result) {
            DB::table('session')->where('username', $username)->update(array(
                'username'      => $username,
                'time'          => time(),
                'host_addr'     => $ip,
                'guest'         => $guest,
            ));
        } else {
            DB::table('session')->insert(array(
                'username'      => $username,
                'time'          => time(),
                'host_addr'     => $ip,
                'guest'         => $guest,
            ));
        }

        $guest_online_num = DB::table('session')->select('username')->where('guest', 1)->count();

        $member_online_num = DB::table('session')->select('username')->where('guest', 0)->count();

        $who_online = '<p class="text-center">'. __d('two_users', 'Il y a actuellement') .' <span class="badge bg-secondary">'. $guest_online_num .'</span> '. __d('two_users', 'visiteur(s) et') .' <span class="badge bg-secondary"> '. $member_online_num .' </span> '. __d('two_users', 'membre(s) en ligne.') .'<br />';
        $content = $who_online;

        $user = User::getUser();

        if ($user) {
            $content .= '<br />'. __d('two_users', 'Vous êtes connecté en tant que') .' <strong>'. $username .'</strong>.<br />';
            
            $result = Cache::remember('onlineblock', Config::get('two_users::config.cache.onlineblock'), function () use ($username) {
                    return DB::table('users')
                                ->select('uid')
                                ->where('uname', $username)
                                ->first();
            });

            $numrow = DB::table('priv_msgs')
                        ->select('to_userid')
                        ->where('to_userid', $result->uid)
                        ->where('type_msg', 0)->count();

            $content .= __d('two_users', 'Vous avez') .' <a href="'. site_url('viewpmsg.php') .'"><span class="badge bg-primary">'. $numrow .'</span></a> '. __d('two_users', 'message(s) personnel(s).') .'</p>';
        } else {
            $content .= '<br />'. __d('two_users', 'Devenez membre privilégié en cliquant') .' <a href="'. site_url('user.php?op=only_newuser') .'">'. __d('two_users', 'ici') .'</a></p>';
        }

        global $block_title;
        $title = $block_title == '' ? __d('two_users', 'Qui est en ligne ?') : $block_title;

        Theme::themesidebox($title, $content);
    }
}

if (! function_exists('loginbox'))
{
    /**
     * Bloc Login
     * syntaxe : function#loginbox
     *
     * @return  void    [return description]
     */
    function loginbox(): void
    {
        $boxstuff = '';

        if (!User::getUser()) {
            $boxstuff = '
            <form action="'. site_url('user.php') .'" method="post">
                <div class="mb-3">
                    <label for="uname">'. __d('two_users', 'Identifiant') .'</label>
                    <input class="form-control" type="text" name="uname" maxlength="25" />
                </div>
                <div class="mb-3">
                    <label for="pass">'. __d('two_users', 'Mot de passe') .'</label>
                    <input class="form-control" type="password" name="pass" maxlength="20" />
                </div>
                <div class="mb-3">
                    <input type="hidden" name="op" value="login" />
                    <button class="btn btn-primary" type="submit">'. __d('two_users', 'Valider') .'</button>
                </div>
                <div class="help-block">
                '. __d('two_users', 'Vous n\'avez pas encore de compte personnel ? Vous devriez') .' <a href="'. site_url('user.php') .'">'. __d('two_users', 'en créer un') .'</a>. '. __d('two_users', 'Une fois enregistré') .' '. __d('two_users', 'vous aurez certains avantages, comme pouvoir modifier l\'aspect du site,') .' '. __d('two_users', 'ou poster des commentaires signés...') .'
                </div>
            </form>';

            global $block_title;
            $title = $block_title == '' ? __d('two_users', 'Se connecter') : $block_title;

            Theme::themesidebox($title, $boxstuff);
        }
    }
}

if (! function_exists('userblock'))
{
    /**
     * Bloc membre
     * syntaxe : function#userblock
     *
     * @return  void    [return description]
     */
    function userblock(): void
    {
        if ((user::getUser()) and (user::cookieUser(8))) {

            $getblock = Cache::remember('userblock', Config::get('two_users::config.cache.userblock'), function () {
                return DB::table('users')
                            ->select('ublock')
                            ->where('uid', User::cookieUser(0))
                            ->first();
            });

            $ublock = $getblock->ublock;

            global $block_title;
            $title = $block_title == '' ? __d('two_users', 'Menu de') . ' ' . User::cookieUser(1) : $block_title;

            Theme::themesidebox($title, $ublock);
        }
    }
}
