<?php

if (! function_exists('getIp'))
{
    /**
     * [author_error_handler description]
     *
     * @param   string  $ibid  [$ibid description]
     *
     * @return  void           [return description]
     */
    function author_error_handler(string $ibid): void
    {
        echo '
        <div class="alert alert-danger mb-3">
            ' . __d('two_authors', 'Merci d\'entrer l\'information en fonction des spécifications') .'<br />'. $ibid .'
        </div>
        <a class="btn btn-outline-secondary" href="'. site_url('admin.php?op=mod_authors') .'" >' . __d('two_authors', 'Retour en arrière') .'</a>';
    }
}
