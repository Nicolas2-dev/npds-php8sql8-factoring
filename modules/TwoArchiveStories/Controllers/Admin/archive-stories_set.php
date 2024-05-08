<?php





function ConfigureArchive()
{

    echo '
    <hr />
    <h3 class="mb-3">' . __d('two_archive_storie', 'Paramètres') . '</h3>
    <form id="archiveadm" action="'. site_url('admin.php') .'" method="post">
        <div class="form-floating mb-3">
            <textarea id="arch_titre" class="form-control" type="text" name="arch_titre"  maxlength="400" style="height: 100px" placeholder="' . __d('two_archive_storie', 'Titre de votre page') . '" >' . $arch_titre . '</textarea>
            <label for="arch_titre">' . __d('two_archive_storie', 'Titre de la page') . '</label>
        </div>
        <span class="help-block text-end"><span id="countcar_arch_titre"></span></span>
        <div class="form-floating mb-3">
            <select class="form-select" name="arch">';

    if (isset($arch) and $arch == 1) {
        $sel_a = 'selected="selected"';
        $sel_i = '';
    } else {
        $sel_i = 'selected="selected"';
        $sel_a = '';
    }

    echo '
                <option name="status" value="1" ' . $sel_a . '>' . __d('two_archive_storie', 'Les articles en archive') . '</option>
                <option name="status" value="0" ' . $sel_i . '>' . __d('two_archive_storie', 'Les articles en ligne') . '</option>
            </select>
            <label for="arch">' . __d('two_archive_storie', 'Affichage') . '</label>
        </div>
        <div class="row g-2">
            <div class="col-sm-6">
                <div class="form-floating mb-3">
                <input class="form-control" type="text" id="maxcount" name="maxcount" value="' . $maxcount . '" min="0" max="500" maxlength="3" required="required" />
                <label for="maxcount">' . __d('two_archive_storie', 'Nombre d\'article par page') . '</label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-floating mb-3">
                <input class="form-control" type="text" id="retcache" name="retcache" value="' . $retcache . '" min="0" maxlength="7" required="required" />
                <label for="retcache">' . __d('two_archive_storie', 'Rétention') . '</label>
                </div>
                <span class="help-block text-end">' . __d('two_archive_storie', 'Temps de rétention en secondes') . '</span>
            </div>
        </div>
        <input type="hidden" name="op" value="Extend-Admin-SubModule" />
        <input type="hidden" name="subop" value="SaveSetArchive_stories" />
        <input type="hidden" name="adm_img_mod" value="1" />
        <button class="btn btn-primary" type="submit">' . __d('two_archive_storie', 'Sauver') . '</button>
    </form>
    <hr />
    <a href= "'. site_url('modules.php?ModPath=' . $ModPath . '&amp;ModStart=' . $ModPath) .'" ><i class="fas fa-external-link-alt fa-lg me-1" title="' . __d('two_archive_storie', 'Voir le module en mode utilisation.') .'" data-bs-toggle="tooltip" data-bs-placement="right"></i>' . __d('two_archive_storie', 'Voir le module en mode utilisation.') .'</a>';

    $fv_parametres = '
    maxcount: {
        validators: {
            regexp: {
                regexp:/^[1-9](\d{0,2})$/,
                message: "0-9"
            },
            between: {
                min: 0,
                max: 500,
                message: "1 ... 500"
            }
        }
    },
    retcache: {
        validators: {
            regexp: {
                regexp:/^[1-9]\d{0,6}$/,
                message: "0-9"
            }
        }
    },';

    $arg1 = '
    var formulid=["archiveadm"];
    inpandfieldlen("arch_titre",400);';

    css::adminfoot('fv', $fv_parametres, $arg1, '');
}

function SaveSetArchive_stories($maxcount, $arch, $arch_titre, $retcache)
{
    $file = fopen("modules/$ModPath/config/archive-stories.conf.php", "w");

    $content = "<?php \n";
    $content .= "// Nombre de Stories par page \n";
    $content .= "\$maxcount = $maxcount;\n";
    $content .= "// Les news en ligne ($arch=0;) ou les archives ($arch=1;) ? \n";
    $content .= "\$arch = $arch;\n";
    $content .= "// Titre de la liste des news (par exemple : \"<h2>Les Archives</h2>\") / si \$arch_titre est vide rien ne sera affiché \n";
    $content .= "\$arch_titre = \"$arch_titre\";\n";
    $content .= "// Temps de rétention en secondes\n";
    $content .= "\$retcache = $retcache;\n";
    $content .= "?>";
    fwrite($file, $content);
    fclose($file);

    @chmod("modules/$ModPath/config/archive-stories.conf.php", 0666);


}

settype($subop, 'string');

switch ($subop) {
    case "SaveSetArchive_stories":
        SaveSetArchive_stories($maxcount, $arch, $arch_titre, $retcache);

    default:
        ConfigureArchive();
        break;
}
