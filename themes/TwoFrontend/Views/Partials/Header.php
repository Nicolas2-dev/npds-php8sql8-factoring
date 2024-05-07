<nav id="uppernavbar" <?= $headerclasses; ?>>
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><span data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="right" title="&lt;i class='fa fa-home fa-lg' &gt;&lt;/i&gt;">NPDS^ 16</span></a>
        <button href="#" class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#barnav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="barnav">
            <ul class="navbar-nav">
                <li class="nav-item dropdown"><a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">News</a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a class="dropdown-item" href="index.php?op=index.php">[fr]Les articles[/fr][en]Stories[/en][zh]&#x6587;&#x7AE0;[/zh][es]Art&#xED;culo[/es][de]Artikeln[/de]</a></li>
                        <li><a class="dropdown-item" href="search.php">[fr]Les archives[/fr][en]Archives[/en][zh]&#x6863;&#x6848;&#x9986;[/zh][es]Los archivos[/es][de]Die Archive[/de]</a></li>
                        <li><a class="dropdown-item" href="submit.php">[fr]Soumettre un article[/fr][en]Submit a New[/en][zh]&#x63D0;&#x8BAE;&#x51FA;&#x7248;&#x4E00;&#x4EFD;&#x51FA;&#x7248;&#x7269;[/zh][es]Enviar un artículo[/es][de]Publikation vorschlagen[/de]</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="forum.php">[fr]Forums[/fr][en]Forums[/en][zh]&#x7248;&#x9762;&#x7BA1;&#x7406;[/zh][es]Foros[/es][de]Foren[/de]</a></li>
                <li class="nav-item"><a class="nav-link" href="download.php">[fr]T&eacute;l&eacute;chargements[/fr][en]Downloads[/en][zh]Downloads[/zh][es]Descargas[/es][de]Downloads[/de]</a></li>
                <li class="nav-item"><a class="nav-link" href="modules.php?ModPath=links&amp;ModStart=links">[fr]Liens[/fr][en]Links[/en][zh]&#x7F51;&#x9875;&#x94FE;&#x63A5;[/zh][es]Enlaces web[/es][de]Internetlinks[/de]</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa fa-user fa-lg"></i>&nbsp;
                        <?php if (isset($username)) { ?>
                            <?= $username; ?>
                        <?php } ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <?php if (isset($avatar)) { ?>
                                <?= $avatar; ?>
                            <?php } ?>
                        </li>
                        <?php if (isset($usermenu)) { ?>
                            <?= $usermenu; ?>
                        <?php } ?>
                        <li class="dropdown-divider"></li>
                        <li>
                            <?php if (isset($btn_con)) { ?>
                                <?= $btn_con; ?>
                            <?php } ?>
                        </li>
                    </ul>
                </li>
                <?php if (isset($privmsgs)) { ?>
                    <?= $privmsgs; ?>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
<div class="page-header">
    <div class="row">
        <div class="col-sm-2"><img class="img-fluid" src="<?= $theme->asset_theme_url('images/header.png', '!theme_hint!'); ?>" alt="image_entete" /></div>
        <div id="logo_header" class="col-sm-6">
            <h1 class="my-4">NPDS<br /><small class="text-muted">Responsive</small></h1>
        </div>
        <div id="ban" class="col-sm-4 text-end">!banner!</div>
    </div>
    <div class="row">
        <div id="slogan" class="col-sm-8 text-muted slogan"><strong>!slogan!</strong></div>
        <div id="online" class="col-sm-4 text-muted text-end">!nb_online!</div>
    </div>
</div>
<script type="text/javascript">
    //<![CDATA[
    $(document).ready(function() {
        var chat_pour = ['chat_tous', 'chat_membres', 'chat_anonyme', 'chat_admin'];
        chat_pour.forEach(function(ele) {
            if ($('#' + ele + '_encours').length) {
                var clon = $('#' + ele + '_encours').clone()
                    .attr('id', ele + '_ico');
                $(".navbar-nav").append(clon);
                $('#' + ele + '_ico').wrapAll('<li class="nav-item" />');
            }
        })
    })
    //]]>
</script>