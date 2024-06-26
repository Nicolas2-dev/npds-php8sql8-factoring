<?php



if (! function_exists('blocnotes'))
{
    #autodoc blocnotes ($typeBlocNote="shared", $nomBlocNote="", $largeur="", $nblBlocNote="5", $bnclass="") : Bloc blocnotes<br />=> syntaxe :
    #autodoc : function#blocnotes<br />params#shared OU context (partagé ou contextuel), nom_du_bloc OU le texte : $username (nom du bloc=nom du membre ou de l'admin), classe du form, nb de ligne de la textarea, classe pour la zone de saisie (textarea)<br />
    #autodoc : function#blocnotes<br />params#shared,TNT (blocnote partagé s'appelant TNT)
    function blocnotes($typeBlocNote = 'shared', $nomBlocNote = '', $largeur = '', $nblBlocNote = '5', $bnclass = '', $affiche = true)
    {
        global $REQUEST_URI;
        
        $aff = '';

        settype($block_title, 'string');

        if ($typeBlocNote == "shared") {
            if ($nomBlocNote == "\$username") {
                
                global $cookie;
                $nomBlocNote = $cookie[1];
            }

            $bnid = md5($nomBlocNote);
        } elseif ($typeBlocNote == "context") {

            if ($nomBlocNote == "\$username") {

                global $cookie, $admin;

                $nomBlocNote = $cookie[1];
                $cur_admin = explode(':', base64_decode($admin));

                if ($cur_admin) {
                    $nomBlocNote = $cur_admin[0];
                }
            }

            if (stristr($REQUEST_URI, "article.php")) {
                $bnid = md5($nomBlocNote . substr($REQUEST_URI, 0, strpos($REQUEST_URI, "&")));
            } else {
                $bnid = md5($nomBlocNote . $REQUEST_URI);
            }
        } else {
            $nomBlocNote = '';
        }

        if ($nomBlocNote) {

            if ($block_title == '') {
                $title = $nomBlocNote;
            } else {
                $title = $block_title;
            }

            $aff .= '
                <form class="' . $largeur . '" method="post" action="'. site_url('modules.php?ModPath=BlocNotes&amp;ModStart=blocnotes') .'" name="A' . $bnid . '">
                    <div class="mb-3">
                    <textarea class="form-control ' . $bnclass . '" rows="' . $nblBlocNote . '" name="texteBlocNote" id="texteBlocNote_' . $bnid . '" placeholder="..."></textarea>
                    </div>
                    <div class="mb-3">
                    <input type="hidden" name="uriBlocNote" value="' . urlencode($REQUEST_URI ?? '') . '" />
                    <input type="hidden" name="typeBlocNote" value="' . $typeBlocNote . '" />
                    <input type="hidden" name="nomBlocNote" value="' . $nomBlocNote . '" />
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="okBlocNote" class="btn btn-outline-primary btn-sm btn-block" > <i class="fa fa-check me-1"></i>' . __d('two_blocntes', 'Valider') . '</button>
                            <button type="submit" name="supBlocNote" class="btn btn-outline-danger btn-sm btn-block" value="RAZ"><i class="fas fa-times me-1"></i>' . __d('two_blocntes', 'Effacer') . '</button>
                        </div>
                    </div>
                    </div>
                </form>
                <script type="text/javascript" src="'. site_url('modules.php?ModPath=BlocNotes&amp;ModStart=blocnotes-read&amp;bnid=' . $bnid) .'"></script>';
        }

        if ($affiche) {
            themesidebox($title, $aff);
        } else {
            return $aff;
        }
    }
}
