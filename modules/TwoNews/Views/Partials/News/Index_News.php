<div class="card mb-3">
    <div class="card-img-top n-sujetsize"> !N_sujet! </div>
    <div class="card-body">
        <h3 class="card-title"> !N_categorie! !N_titre! </h3>
        <div class="card-text article_texte"> !N_texte! </div>
        <?php
        if ($notes != '') {
            echo '<blockquote class="blockquote">' . $notes . '</blockquote>';
        }
        ?>
        <hr />
        <p class="card-text"><small class="text-muted">!N_emetteur! [fr]Publi&eacute; le[/fr][en]Published on[/en][zh]&#x53D1;&#x8868;[/zh][es]Publicado[/es][de]Ver&#246;ffentlicht[/de] : !N_date! </small></p>

    </div>
    <div class="card-footer pied_art">
        <div class="row">
            <div class="col-sm-6">!N_read_more! <small class="text-muted">!N_nb_carac!</small></div>
            <div class="col-sm-6 text-end">!N_print! !N_friend! <span class="badge rounded-pill bg-secondary">!N_nb_comment!</span> !N_link_comment!</div>
        </div>
    </div>
</div>