<?php

use Two\Support\Facades\Config;
use Modules\TwoUsers\Support\Facades\User;
use Modules\TwoThemes\Support\Facades\Theme;

if (!function_exists('blockSkin')) {
    /**
     * [blockSkin description]
     *
     * @return  void
     */
    function blockSkin(): void
    {
        $skinOn = '';

        if (User::getUser()) {
            $ibix = explode('+', urldecode(User::cookieUser(9)));
            $skinOn = substr($ibix[0], -3) != '_sk' ? '' : $ibix[1];
        } else {
            $skinOn = substr(Config::get('two_core::config.Default_Theme'), -3) != '_sk' ? '' : Config::get('two_core::config.Default_Skin');
        }

        $content = '';

        if ($skinOn != '') {
            $content .= '
            <div class="form-floating">
                <select class="form-select" id="blocskinchoice"><option>' . $skinOn . '</option></select>
                <label for="blocskinchoice">Choisir un skin</label>
            </div>
            <script type="text/javascript">
                //<![CDATA[
                fetch("themes/_skins/api/skins.json")
                    .then(response => response.json())
                    .then(data => load(data));
                function load(data) {
                    const skins = data.skins;
                    const select = document.querySelector("#blocskinchoice");
                    skins.forEach((value, index) => {
                        const option = document.createElement("option");
                        option.value = index;
                        option.textContent = value.name;
                        select.append(option);
                    });
                    select.addEventListener("change", (e) => {
                        const skin = skins[e.target.value];
                        if (skin) {
                        document.querySelector("#bsth").setAttribute("href", skin.css);
                        document.querySelector("#bsthxtra").setAttribute("href", skin.cssxtra);
                        }
                    });
                    const changeEvent = new Event("change");
                    select.dispatchEvent(changeEvent);
                }
                //]]>
            </script>';
        } else {
            $content .= '<div class="alert alert-danger">Thème non skinable</div>';
        }

        Theme::themesidebox('Theme Skin', $content);
    }
}
