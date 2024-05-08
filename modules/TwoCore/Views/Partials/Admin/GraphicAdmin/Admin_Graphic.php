<script type="text/javascript">
    //<![CDATA[
    function openwindow() {
        window.open("<?= $hlpfile; ?>", "Help", "toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=400");
        }

        $(document).ready(function() {
            $('ul.list').sortable({
                update: function() {
                    Cookies.set('items', getItems('#lst_men_main'));
                    console.log(Cookies.get('items'));
                }

            });

            var htmmll = [];

            // Get all items from a container
            function getItems(container) {
                var columns = [];
                
                $(container + ' ul.list').each(function() {
                    columns.push($(this).sortable('toArray').join(','));
                    htmmll.push($(this).html());
                });

                return columns.join('|');

            }

            var itemStr = getItems('#lst_men_main');
            // console.log(htmmll);

            $('[data-bs-toggle="tooltip"]').tooltip();
            $('[data-bs-toggle="popover"]').popover();
            $('table').on('all.bs.table', function(e, name, args) {
                $('[data-bs-toggle="tooltip"]').tooltip();
                $('[data-bs-toggle="popover"]').popover();
            });
        });

        // date d'expiration connection admin
        $(function() {
            var dae = Cookies.get('adm_exp') * 1000,
                dajs = new Date(dae);

            // console.log(Cookies.get('adm_exp'));

            // arevoir trad !!!
            $('#adm_connect_status').attr('title', 'Connexion ouverte jusqu \'au : ' + dajs.getDate() + '/' + (dajs.getMonth() + 1) + '/' + dajs.getFullYear() + '/' + dajs.getHours() + ':' + dajs.getMinutes() + ':' + dajs.getSeconds() + ' GMT');

            deCompte = function() {
                var date1 = new Date(),
                    sec = (dae - date1) / 1000,
                    n = 24 * 3600;
                if (sec > 0) {
                    j = Math.floor(sec / n);
                    h = Math.floor((sec - (j * n)) / 3600);
                    mn = Math.floor((sec - ((j * n + h * 3600))) / 60);
                    sec = Math.floor(sec - ((j * n + h * 3600 + mn * 60)));
                    $('#tempsconnection').text(j + 'j ' + h + ':' + mn + ':' + sec);
                }
                t_deCompte = setTimeout(deCompte, 1000);
            }
            deCompte();
        })
        // date d'expiration connection admin

        // menu admin tog
        tog = function(lst, sho, hid) {
            $('#adm_men, #adm_workarea').on('click', 'a.tog', function() {
                var buttonID = $(this).attr('id');
                lst_id = $('#' + lst);
                i_id = $('#i_' + lst);
                btn_show = $('#' + sho);
                btn_hide = $('#' + hid);
                if (buttonID == sho) {
                    lst_id.fadeIn(1000); //show();
                    btn_show.attr('id', hid)
                    btn_show.attr('title', '<?= __d('two_core', 'Replier la liste'); ?>');
                    i_id.attr('class', 'fa fa-caret-up fa-lg text-primary me-1');
                } else if (buttonID == hid) {
                    lst_id.fadeOut(1000); //hide();
                    btn_hide = $('#' + hid);
                    btn_hide.attr('id', sho);
                    btn_hide.attr('title', '<?= html_entity_decode(__d('two_core', 'Déplier la liste'), ENT_QUOTES | ENT_HTML401, 'utf-8'); ?>');
                    i_id.attr('class', 'fa fa-caret-down fa-lg text-primary me-1');
                }
            });
        };

        // modal sur alerte et version update
        $(function() {
            $('#messageModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');

                $('#messageModalId').val(id);
                $('#messageModalForm').attr('action', '<?= site_url('admin.php?op=alerte_update'); ?>');
                
                $.ajax({
                    url: '<?= site_url('admin.php?op=alerte_api'); ?>',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'JSON',
                    success: function(data) {
                        $('#messageModalLabel').html(JSON.parse(JSON.stringify(data['fretour_h'])));
                        $('#messageModalContent').html(JSON.parse(JSON.stringify(data['fnom_affich'])));
                        
                        // a revoir pour chargement de l'image !!!
                        $('#messageModalIcon').html('<img src="assets/images/admin/' + JSON.parse(JSON.stringify(data['ficone'])) + '.png\" />');
                    }
                });
            });
        });
    //]]>
</script>

<div id="adm_tit" class="row">
    <div id="adm_tit_l" class="col-12">
        <h1><?= __d('two_core', 'Administration'); ?></h1>
    </div>
</div>
<div id="adm_men" class="mb-4">
    <div id="adm_header" class="row justify-content-end">
        <div class="col-6 col-lg-6 men_tit align-self-center">
            <h2><a href="<?= site_url('admin.php'); ?>"><?= __d('two_core', 'Menu'); ?></a></h2>
        </div>
        <div id="adm_men_man" class="col-6 col-lg-6 men_man text-end">
            <ul class="liste" id="lst_men_top">
                <li data-bs-toggle="tooltip" title="<?= __d('two_core', 'Déconnexion'); ?>" >
                    <a class="btn btn-outline-danger btn-sm" href="<?= site_url('admin.php?op=logout'); ?>">
                        <i class="fas fa-sign-out-alt fa-2x"></i>
                    </a>
                </li>
                <?php if ($hlpfile) { ?>
                    <li class="ms-2" data-bs-toggle="tooltip" title="<?= __d('two_core', 'Manuel en ligne'); ?>">
                        <a class="btn btn-outline-primary btn-sm" href="javascript:openwindow();">
                            <i class="fa fa-question-circle fa-2x"></i>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div> 
            
    <?php if ($short_menu_admin) { ?>
        </div>
        <?php return; ?>
    <?php } ?>

    <div id="adm_men_corps" class="my-3">
        <div id="lst_men_main">
            <?= $bloc_foncts; ?>
        </div>
    </div>
</div>
