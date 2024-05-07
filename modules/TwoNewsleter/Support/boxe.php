<?php

use Modules\TwoThemes\Support\Facades\Theme;


if (!function_exists('lnlbox')) {
    /**
     * Bloc Little News-Letter
     * syntaxe : function#lnlbox
     *
     * @return  void    [return description]
     */
    function lnlbox(): void
    {
        global $block_title;

        $title = $block_title == '' ? translate("La lettre") : $block_title;

        $arg1 = '
        var formulid = ["lnlblock"]';

        $boxstuff = '
          <form id="lnlblock" action="' . site_url('lnl.php') . '" method="get">
             <div class="mb-3">
                <select name="op" class=" form-select">
                   <option value="subscribe">' . translate("Abonnement") . '</option>
                   <option value="unsubscribe">' . translate("Désabonnement") . '</option>
                </select>
             </div>
             <div class="form-floating mb-3">
                <input type="email" id="email_block" name="email" maxlength="254" class="form-control" required="required"/>
                <label for="email_block">' . translate("Votre adresse Email") . '</label>
                <span class="help-block">' . translate("Recevez par mail les nouveautés du site.") . '</span>
             </div>
             <button type="submit" class="btn btn-outline-primary btn-block btn-sm"><i class ="fa fa-check fa-lg me-2"></i>' . translate("Valider") . '</button>
          </form>'
            . adminfoot('fv', '', $arg1, '0');

        Theme::themesidebox($title, $boxstuff);
    }
}
