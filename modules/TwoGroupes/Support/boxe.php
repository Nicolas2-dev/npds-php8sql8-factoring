<?php

if (! function_exists('bloc_espace_groupe'))
{
    /**
     * Bloc du WorkSpace
     * syntaxe : function#bloc_espace_groupe
     * params#ID_du_groupe, Aff_img_groupe(0 ou 1) 
     * Si le bloc n'a pas de titre, Le nom du groupe sera utilisé
     *
     * @param   string   $gr    [$gr description]
     * @param   string   $i_gr  [$i_gr description]
     *
     * @return  void
     */
    function bloc_espace_groupe(string $gr, string $i_gr): void
    {
        global $block_title;

        if ($block_title == '') {
            
            $rsql = DB::table('groupes')->select('groupe_name')->where('groupe_id', $gr)->first();
            
            $title = $rsql['groupe_name'];
        } else {
            $title = $block_title;
        }

        themesidebox($title, Groupe::fab_espace_groupe($gr, "0", $i_gr));
    }
}

if (! function_exists('bloc_groupes'))
{
    /**
     * Bloc des groupes
     * syntaxe : function#bloc_groupes
     * params#Aff_img_groupe(0 ou 1) Si le bloc n'a pas de titre,
     * 'Les groupes' sera utilisé. Liste des groupes AVEC membres et lien pour demande d'adhésion pour l'utilisateur.
     *
     * @param   string   $im  [$im description]
     *
     * @return  void
     */
    function bloc_groupes(string $im): void
    {
        global $block_title;

        $title = $block_title == '' ? 'Les groupes' : $block_title;

        themesidebox($title, Groupe::fab_groupes_bloc(Users::getUser(), $im));
    }
}
