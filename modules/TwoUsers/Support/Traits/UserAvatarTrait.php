<?php

namespace Modules\TwoUsers\Support\Traits;

use Modules\TwoThemes\Support\Facades\Theme;


trait UserAvatarTrait 
{

    public function avatar($user)
    {
        if (!$user->user_avatar) {
            $avatar = asset_url('images/avatar/blank.gif', 'modules/'. $this->app['two-users']->getHint());

        } elseif (stristr($user->user_avatar, "users_private")) {
            $avatar = $user->picture();
            
        } else {
            if ($theme_avatar = Theme::image('avatar/'. $user->user_avatar)) {
                $avatar = $theme_avatar;

            } elseif ($this->app->files->exists($this->app['two-users']->getPath() .DS. 'Assets' .DS. 'images' .DS. 'avatar' .DS. $user->user_avatar)) {
                $avatar = asset_url('images/avatar/'. $user->user_avatar, 'modules/'. $this->app['two-users']->getHint());

            } else {
                $avatar = asset_url('images/forum/avatar/blank.gif', 'modules/'. $this->app['two-users']->getHint());
            }
        }

        return $avatar;
    }

}