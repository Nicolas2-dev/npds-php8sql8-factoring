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

        $title = $block_title == '' ? __d('two_newsleter', 'La lettre') : $block_title;

        $arg1 = '
        var formulid = ["lnlblock"]';

        $boxstuff = '
          <form id="lnlblock" action="' . site_url('lnl.php') . '" method="get">
             <div class="mb-3">
                <select name="op" class=" form-select">
                   <option value="subscribe">' . __d('two_newsleter', 'Abonnement') . '</option>
                   <option value="unsubscribe">' . __d('two_newsleter', 'Désabonnement') . '</option>
                </select>
             </div>
             <div class="form-floating mb-3">
                <input type="email" id="email_block" name="email" maxlength="254" class="form-control" required="required"/>
                <label for="email_block">' . __d('two_newsleter', 'Votre adresse Email') . '</label>
                <span class="help-block">' . __d('two_newsleter', 'Recevez par mail les nouveautés du site.') . '</span>
             </div>
             <button type="submit" class="btn btn-outline-primary btn-block btn-sm"><i class ="fa fa-check fa-lg me-2"></i>' . __d('two_newsleter', 'Valider') . '</button>
          </form>'
            . adminfoot('fv', '', $arg1, '0');

        Theme::themesidebox($title, $boxstuff);
    }
}
