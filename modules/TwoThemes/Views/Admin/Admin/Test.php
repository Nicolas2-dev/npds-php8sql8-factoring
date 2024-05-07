<div id="adm_men_art" class="adm_workarea">
    <h2>
        <img src="<?= asset_url('images/admin/submissions.' . config('two_core::config.admf_ext'), 'modules/two_themes'); ?>" class="adm_img" title="<?= __d('platform', 'Articles'); ?>" alt="icon_<?= __d('platform', 'Articles'); ?>" />&nbsp;<?= __d('platform', 'Derniers'); ?> <?= $admart; ?> <?= __d('platform', 'Articles'); ?>
    </h2>

    <?php if (isset($nbre_articles)) : ?>
        <table id="lst_art_adm" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-buttons-class="outline-secondary" data-mobile-responsive="true" data-icons-prefix="fa" data-icons="icons">
            <thead>
                <tr>
                    <th data-sortable="true" data-halign="center" data-align="right" class="n-t-col-xs-1">ID</th>
                    <th data-halign="center" data-sortable="true" data-sorter="htmlSorter" class="n-t-col-xs-5"><?= __d('platform', 'Titre'); ?></th>
                    <th data-sortable="true" data-halign="center" class="n-t-col-xs-4"><?= __d('platform', 'Sujet'); ?></th>
                    <th data-halign="center" data-align="center" class="n-t-col-xs-2"><?= __d('platform', 'Fonctions'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($stories)) : ?>
                    <?php foreach ($stories as $storie) : ?>
                        <tr>
                            <td><?= $storie['sid']; ?></td>
                            <td>

                                <?php if ($storie['archive']) : ?>
                                    <?= $storie['title']; ?> <i>(archive)</i>
                                <?php else : ?>

                                        <?php if ($storie['affiche']) : ?>
                                            <a data-bs-toggle="popover" data-bs-placement="left" data-bs-trigger="hover" href="article.php?sid=<?= $storie['sid']; ?>" data-bs-content='   <div class="thumbnail"><img class="img-rounded" src="<?= asset_url('images/' . $storie['topicimage'], 'modules/two_themes'); ?>" height="80" width="80" alt="topic_logo" /><div class="caption"><?= $storie['hometext']; ?></div></div>\' title="ID : <?= $storie['sid']; ?> - <?= ucfirst($storie['title']); ?>" data-bs-html="true"><?= ucfirst($storie['title']); ?></a>

                                            <?php if ($storie['ihome'] == 1) : ?>
                                                <br /><small><span class="badge bg-secondary" title="<?= __d('platform', 'Catégorie'); ?>" data-bs-toggle="tooltip"><?= $cat_title; ?></span> <span class="text-danger">non publié en index</span></small>
                                            <?php else : ?>

                                                <?php if ($storie['catid'] > 0) : ?>
                                                    jhgjgkgg
                                                <?php endif; ?>
                                                
                                            <?php endif; ?>

                                        <?php else : ?>
                                            <i><?= $storie['title']; ?></i>
                                        <?php endif; ?>

                                <?php endif; ?>

                            <?php if ($storie['topictext'] == '') : ?>
                                </td>
                                <td>
                            <?php else : ?>
                                </td>
                                <td><?= $storie['topictext']; ?><a href="index.php?op=newtopic&amp;topic=<?= $storie['topic']; ?>" class="tooltip"><?= $storie['topictext']; ?></a>
                            <?php endif; ?>

                            <?php if ($storie['affiche']) : ?>
                                </td>
                                <td>
                                    <a href="admin.php?op=EditStory&amp;sid=<?= $storie['sid']; ?>"><i class="fas fa-edit fa-lg me-2" title="<?= __d('platform', 'Editer'); ?>" data-bs-toggle="tooltip"></i></a>
                                    <a href="admin.php?op=RemoveStory&amp;sid=<?= $storie['sid']; ?>"><i class="fas fa-trash fa-lg text-danger" title="<?= __d('platform', 'Effacer'); ?>" data-bs-toggle="tooltip"></i></a>
                            <?php else : ?>
                                </td>
                                <td>
                            <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

            </tbody>
        </table>
        <div class="d-flex my-2 justify-content-between flex-wrap">
            <ul class="pagination pagination-sm">
                <li class="page-item disabled"><a class="page-link" href="#"><?= $nbre_articles; ?> <?= __d('platform', 'Articles'); ?></a></li>
                <li class="page-item disabled"><a class="page-link" href="#"><?= $nbPages; ?> <?= __d('platform', 'Page(s)'); ?></a></li>
            </ul>

            <?php if (isset($paginate)) : ?>
                <?= $paginate; ?>
            <?php endif; ?>
        </div>


        <form id="fad_articles" class="form-inline" action="admin.php" method="post">
            <label class="me-2 mt-sm-1"><?= __d('platform', 'ID Article:'); ?></label>
            <input class="form-control  me-2 mt-sm-3 mb-2" type="number" name="sid" />
            <select class="form-select me-2 mt-sm-3 mb-2" name="op">
                <option value="EditStory" selected="selected"><?= __d('platform', 'Editer un Article'); ?></option>
                <option value="RemoveStory"><?= __d('platform', 'Effacer l\'Article'); ?></option>
            </select>
            <button class="btn btn-primary ms-sm-2 mt-sm-3 mb-2" type="submit"><?= __d('platform', 'Ok'); ?> </button>
        </form>
    <?php endif; ?>

</div>