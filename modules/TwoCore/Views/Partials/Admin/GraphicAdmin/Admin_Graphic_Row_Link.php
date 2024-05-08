<?php if (isset($aff_ul_f)) { ?>
    <?= $ul_f; ?>       
<?php } ?>

<?php if (isset($aff_ul_o)) { ?>
    <h4 class="text-muted">
        <a class="tog" id="hide_<?= $fcategorie_nom_tog; ?>" title="<?= __d('two_core', 'Replier la liste'); ?>" style="clear:left;">
            <i id="i_<?= $fcategorie_nom_tog; ?>'" class="fa fa-caret-up fa-lg text-primary" ></i>
        </a>&nbsp;<?= $fcategorie_nom; ?>
    </h4>
    <ul id="<?= $fcategorie_nom_tog; ?>" class="list" style="clear:left;">
<?php } ?>

<?php if (isset($aff_lic_c)) { ?>
    <?php if ($enabled) { ?>    
        <li id="<?= $fid; ?>"  data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $title; ?>">    
            <a class="btn btn-outline-primary" <?= $furlscript . $blank; ?>>   
                <?php if (isset($admingraphic)) { ?>
                    <img class="adm_img" src="<?= $adminico; ?>" alt="icon_<?= $fnom_affich; ?>" loading="lazy" />
                <?php } else { ?>
                    <?= $title; ?>
                <?php } ?>          
            </a>             
        </li>  
    <?php } ?>
<?php } ?>