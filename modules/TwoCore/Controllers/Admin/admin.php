<?php

/************************************************************************/
/* DUNE by NPDS - admin prototype                                       */
/* ===========================                                          */
/*                                                                      */
/*                                                                      */
/* NPDS Copyright (c) 2002-2023 by Philippe Brunier                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
declare(strict_types=1);

use npds\support\auth\authors;
use npds\system\config\Config;
use npds\support\language\language;
use npds\system\support\facades\DB;
use npds\support\pagination\paginator;

if (!function_exists("Mysql_Connexion")) {
    include('boot/bootstrap.php');
}

include('language/'.Config::get('npds.language').'/language-adm.php');



 




function adminMain($deja_affiches)
{
    global $aid, $NPDS_Prefix;

    include("themes/default/header.php");

    Config::set('npds.short_menu_admin', false);
    
    $radminsuper = GraphicAdmin(manuel('admin'));

    echo '
    <div id="adm_men_art" class="adm_workarea">
    <h2><img src="assets/images/admin/submissions.' . Config::get('npds.admf_ext') . '" class="adm_img" title="' . adm_translate("Articles") . '" alt="icon_' . adm_translate("Articles") . '" />&nbsp;' . adm_translate("Derniers") . ' ' . Config::get('npds.admart') . ' ' . adm_translate("Articles") . '</h2>';

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $resul = sql_query("SELECT sid FROM " . $NPDS_Prefix . "stories");
    $nbre_articles = sql_num_rows($resul);

    settype($deja_affiches, "integer");

    // = DB::table('')->select()->where('', )->orderBy('')->get();

    $result = sql_query("SELECT sid, title, hometext, topic, informant, time, archive, catid, ihome FROM " . $NPDS_Prefix . "stories ORDER BY sid DESC LIMIT $deja_affiches, ".Config::get('npds.admart'));

    $nbPages = ceil($nbre_articles / Config::get('npds.admart'));
    $current = 1;

    if ($deja_affiches >= 1) {
        $current = $deja_affiches / Config::get('npds.admart');
    } else if ($deja_affiches < 1) {
        $current = 0;
    } else {
        $current = $nbPages;
    }

    $start = ($current * Config::get('npds.admart'));

    if ($nbre_articles) {
        echo '
        <table id ="lst_art_adm" data-toggle="table" data-striped="true" data-search="true" data-show-toggle="true" data-buttons-class="outline-secondary" data-mobile-responsive="true" data-icons-prefix="fa" data-icons="icons">
            <thead>
                <tr>
                <th data-sortable="true" data-halign="center" data-align="right" class="n-t-col-xs-1">ID</th>
                <th data-halign="center" data-sortable="true" data-sorter="htmlSorter" class="n-t-col-xs-5">' . adm_translate("Titre") . '</th>
                <th data-sortable="true" data-halign="center" class="n-t-col-xs-4">' . adm_translate("Sujet") . '</th>
                <th data-halign="center" data-align="center" class="n-t-col-xs-2">' . adm_translate("Fonctions") . '</th>
                </tr>
            </thead>
            <tbody>';
        
        $i = 0;
        
        while ((list($sid, $title, $hometext, $topic, $informant, $time, $archive, $catid, $ihome) = sql_fetch_row($result)) and ($i < Config::get('npds.admart'))) {
            $affiche = false;
            
            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result2 = sql_query("SELECT topicadmin, topictext, topicimage FROM " . $NPDS_Prefix . "topics WHERE topicid='$topic'");
            list($topicadmin, $topictext, $topicimage) = sql_fetch_row($result2);
            
            // = DB::table('')->select()->where('', )->orderBy('')->get();

            $result3 = sql_query("SELECT title FROM " . $NPDS_Prefix . "stories_cat WHERE catid='$catid'");
            list($cat_title) = sql_fetch_row($result3);

            if ($radminsuper) {
                $affiche = true;
            } else {
                $topicadminX = explode(',', $topicadmin);
                for ($iX = 0; $iX < count($topicadminX); $iX++) {
                    if (trim($topicadminX[$iX]) == $aid) { 
                        $affiche = true;
                    }
                }
            }

            $hometext = strip_tags($hometext, '<br><br />');
            $lg_max = 200;

            if (strlen($hometext) > $lg_max) {
                $hometext = substr($hometext, 0, $lg_max) . ' ...';
            }
            
            echo '
            <tr>
                <td>' . $sid . '</td>
                <td>';

            $title = language::aff_langue($title);

            if ($archive) {
                echo $title . ' <i>(archive)</i>';
            } else {
                if ($affiche) {
                    echo '<a data-bs-toggle="popover" data-bs-placement="left" data-bs-trigger="hover" href="article.php?sid=' . $sid . '" data-bs-content=\'   <div class="thumbnail"><img class="img-rounded" src="assets/images/topics/' . $topicimage . '" height="80" width="80" alt="topic_logo" /><div class="caption">' . htmlentities($hometext, ENT_QUOTES) . '</div></div>\' title="' . $sid . '" data-bs-html="true">' . ucfirst($title) . '</a>';
                    if ($ihome == 1) {
                        echo '<br /><small><span class="badge bg-secondary" title="' . adm_translate("Catégorie") . '" data-bs-toggle="tooltip">' . language::aff_langue($cat_title) . '</span> <span class="text-danger">non publié en index</span></small>';
                    } else {
                        if ($catid > 0) {
                            echo '<br /><small><span class="badge bg-secondary" title="' . adm_translate("Catégorie") . '" data-bs-toggle="tooltip"> ' . language::aff_langue($cat_title) . '</span> <span class="text-success"> publié en index</span></small>';
                        }
                    }
                } else {
                    echo '<i>' . $title . '</i>';
                }
            }

            if ($topictext == '') {
                echo '</td>
                <td>';
            } else {
                echo '</td>
                <td>' . $topictext . '<a href="index.php?op=newtopic&amp;topic=' . $topic . '" class="tooltip">' . language::aff_langue($topictext) . '</a>';
            }

            if ($affiche) {
                echo '</td>
                <td>
                <a href="admin.php?op=EditStory&amp;sid=' . $sid . '" ><i class="fas fa-edit fa-lg me-2" title="' . adm_translate("Editer") . '" data-bs-toggle="tooltip"></i></a>
                <a href="admin.php?op=RemoveStory&amp;sid=' . $sid . '" ><i class="fas fa-trash fa-lg text-danger" title="' . adm_translate("Effacer") . '" data-bs-toggle="tooltip"></i></a>';
           } else {
                echo '</td>
                <td>';
            }

            echo '</td>
            </tr>';
            $i++;
        }

        echo '
            </tbody>
        </table>
        <div class="d-flex my-2 justify-content-between flex-wrap">
        <ul class="pagination pagination-sm">
            <li class="page-item disabled"><a class="page-link" href="#">' . $nbre_articles . ' ' . adm_translate("Articles") . '</a></li>
            <li class="page-item disabled"><a class="page-link" href="#">' . $nbPages . ' ' . adm_translate("Page(s)") . '</a></li>
        </ul>';

        echo paginator::paginate('admin.php?op=suite_articles&amp;deja_affiches=', '', $nbPages, $current, 1, Config::get('npds.admart'), $start);

        echo '
        </div>';

        echo '
        <form id="fad_articles" class="form-inline" action="admin.php" method="post">
            <label class="me-2 mt-sm-1">' . adm_translate("ID Article:") . '</label>
            <input class="form-control  me-2 mt-sm-3 mb-2" type="number" name="sid" />
            <select class="form-select me-2 mt-sm-3 mb-2" name="op">
                <option value="EditStory" selected="selected">' . adm_translate("Editer un Article") . '</option>
                <option value="RemoveStory">' . adm_translate("Effacer l'Article") . '</option>
            </select>
            <button class="btn btn-primary ms-sm-2 mt-sm-3 mb-2" type="submit">' . adm_translate("Ok") . ' </button>
        </form>';
    }

    echo '</div>';

    include("themes/default/footer.php");
}

if ($admintest) {

    settype($op, 'string');

    switch ($op) {
        case 'GraphicAdmin':
            GraphicAdmin();
            break;

        case 'logout':
            authors::logout();
            break;

            // FILES MANAGER
        case 'FileManager':
            if ($admintest and Config::get('filemanager.manager')) {
                header("location: modules.php?ModPath=f-manager&ModStart=f-manager&FmaRep=$aid");
            }
            break;

        case 'FileManagerDisplay':
            if ($admintest and Config::get('filemanager.manager')) {
                header("location: modules.php?ModPath=f-manager&ModStart=f-manager&FmaRep=download");
            }
            break;

            //BLACKBOARD
        case 'abla':
            include('abla.php');
            break;

            // CRITIQUES
        case 'reviews':
        case 'mod_main':
        case 'add_review':
            include('admin/reviews.php');
            break;

        case 'deleteNotice':
            DB::table('reviews_add')->where('id', $id)->delete();

            Header("Location: admin.php?op=$op_back");
            break;

            // FORUMS
        case "ForumConfigAdmin":
            include("admin/phpbbconfig.php");
            ForumConfigAdmin();
            break;

        case "ForumConfigChange":
            include("admin/phpbbconfig.php");

            settype($allow_html, 'int');
            settype($allow_bbcode, 'int');
            settype($allow_sig, 'int');
            settype($posts_per_page, 'int');
            settype($hot_threshold, 'int');
            settype($topics_per_page, 'int');
            settype($allow_upload_forum, 'int');
            settype($allow_forum_hide, 'int');
            settype($rank1, 'string');
            settype($rank2, 'string');
            settype($rank3, 'string');
            settype($rank4, 'string');
            settype($rank5, 'string');
            settype($anti_flood, 'int');
            settype($solved, 'int');

            ForumConfigChange($allow_html, $allow_bbcode, $allow_sig, $posts_per_page, $hot_threshold, $topics_per_page, $allow_upload_forum, $allow_forum_hide, $rank1, $rank2, $rank3, $rank4, $rank5, $anti_flood, $solved);
            break;

        case "MaintForumAdmin":
            include("admin/phpbbmaint.php");
            ForumMaintAdmin();
            break;

        case "MaintForumMarkTopics":
            include("admin/phpbbmaint.php");
            ForumMaintMarkTopics();
            break;

        case "MaintForumTopics":
            include("admin/phpbbmaint.php");

            settype($before, 'int');
            settype($forum_name, 'string');

            ForumMaintTopics($before, $forum_name);
            break;

        case "MaintForumTopicDetail":
            include("admin/phpbbmaint.php");

            settype($topic, 'int');
            settype($topic_title, 'string');

            ForumMaintTopicDetail($topic, $topic_title);
            break;

        case "SynchroForum":
            include("admin/phpbbmaint.php");
            SynchroForum();
            break;

        case "ForumMaintTopicSup":
            include("admin/phpbbmaint.php");

            settype($topic, 'int');

            ForumMaintTopicSup($topic);
            break;

        case "ForumMaintTopicMassiveSup":
            include("admin/phpbbmaint.php");

            settype($topic, 'int');

            ForumMaintTopicMassiveSup($topics);
            break;

        case "MergeForum":
            include("admin/phpbbmaint.php");
            MergeForum();
            break;

        case "MergeForumAction":
            include("admin/phpbbmaint.php");

            settype($oriforum, 'int');
            settype($destforum, 'int');

            MergeForumAction($oriforum, $destforum);
            break;

        case "ForumGoAdd":
            settype($forum_pass, 'string');
            settype($forum_desc, 'string');
            settype($forum_access, 'int');
            settype($forum_mod, 'string');
            settype($cat_id, 'int');
            settype($forum_type, 'int');
            settype($forum_pass, 'string');
            settype($arbre, 'int');
            settype($attachement, 'int');
            settype($forum_index, 'int');
            settype($ctg, 'string');

            include("admin/phpbbforum.php");
            ForumGoAdd($forum_name, $forum_desc, $forum_access, $forum_mod, $cat_id, $forum_type, $forum_pass, $arbre, $attachement, $forum_index, $ctg);
            break;

        case "ForumGoSave":
            include("admin/phpbbforum.php");

            settype($forum_id, 'int');
            settype($forum_name, 'string');
            settype($forum_desc, 'string');
            settype($forum_access, 'int');
            settype($forum_mod, 'string');
            settype($cat_id, 'int');
            settype($forum_type, 'int');
            settype($forum_pass, 'string');
            settype($arbre, 'int');
            settype($attachement, 'int');
            settype($forum_index, 'int');
            settype($ctg, 'string');

            ForumGoSave($forum_id, $forum_name, $forum_desc, $forum_access, $forum_mod, $cat_id, $forum_type, $forum_pass, $arbre, $attachement, $forum_index, $ctg);
            break;

        case "ForumCatDel":
            include("admin/phpbbforum.php");

            settype($cat_id, 'int');
            settype($ok, 'int');

            ForumCatDel($cat_id, $ok);
            break;

        case "ForumGoDel":
            include("admin/phpbbforum.php");

            settype($forum_id, 'int');
            settype($ok, 'int');

            ForumGoDel($forum_id, $ok);
            break;

        case "ForumCatSave":
            include("admin/phpbbforum.php");

            settype($old_cat_id, 'int');
            settype($cat_id, 'int');
            settype($cat_title, 'string');

            ForumCatSave($old_cat_id, $cat_id, $cat_title);
            break;

        case "ForumCatEdit":
            include("admin/phpbbforum.php");

            settype($cat_id, 'int');

            ForumCatEdit($cat_id);
            break;

        case "ForumGoEdit":
            include("admin/phpbbforum.php");

            settype($forum_id, 'int');
            settype($ctg, 'string');

            ForumGoEdit($forum_id, $ctg);
            break;

        case "ForumGo":
            include("admin/phpbbforum.php");

            settype($cat_id, 'int');

            ForumGo($cat_id);
            break;

        case "ForumCatAdd":
            include("admin/phpbbforum.php");

            settype($categories, 'string');

            ForumCatAdd($catagories);
            break;

        case "ForumAdmin":
            include("admin/phpbbforum.php");
            ForumAdmin();
            break;

            // DOWNLOADS
        case "DownloadDel":
            include("admin/download.php");
            DownloadDel($did, $ok);
            break;

        case "DownloadAdd":
            include("admin/download.php");
            DownloadAdd($dcounter, $durl, $dfilename, $dfilesize, $dweb, $duser, $dver, $dcategory, $sdcategory, $xtext, $privs, $Mprivs);
            break;

        case "DownloadSave":
            include("admin/download.php");

            $ddate = isset($ddate) ? $ddate : '';
            $Mprivs = isset($Mprivs) ? $Mprivs : [];

            DownloadSave($did, $dcounter, $durl, $dfilename, $dfilesize, $dweb, $duser, $ddate, $dver, $dcategory, $sdcategory, $xtext, $privs, $Mprivs);
            break;

        case "DownloadAdmin":
            include("admin/download.php");
            DownloadAdmin();
            break;

        case "DownloadEdit":
            include("admin/download.php");
            DownloadEdit($did);
            break;

            // FAQ
        case "FaqCatSave":
            include("admin/adminfaq.php");

            settype($old_id_cat, 'int');
            settype($id_cat, 'int');
            settype($categories, 'string');

            FaqCatSave($old_id_cat, $id_cat, $categories);
            break;

        case "FaqCatGoSave":
            include("admin/adminfaq.php");

            settype($id, 'int');
            settype($question, 'string');
            settype($answer, 'string');

            FaqCatGoSave($id, $question, $answer);
            break;

        case "FaqCatAdd":
            include("admin/adminfaq.php");

            settype($categories, 'string');

            FaqCatAdd($categories);
            break;

        case "FaqCatGoAdd":
            include("admin/adminfaq.php");

            settype($id_cat, 'int');
            settype($question, 'string');
            settype($answer, 'string');

            FaqCatGoAdd($id_cat, $question, $answer);
            break;

        case "FaqCatEdit":
            include("admin/adminfaq.php");

            settype($id_cat, 'int');

            FaqCatEdit($id_cat);
            break;

        case "FaqCatGoEdit":
            include("admin/adminfaq.php");

            settype($id, 'int');

            FaqCatGoEdit($id);
            break;

        case "FaqCatDel":
            include("admin/adminfaq.php");

            settype($id_cat, 'int');
            settype($ok, 'int');

            FaqCatDel($id_cat, $ok);
            break;

        case "FaqCatGoDel":
            include("admin/adminfaq.php");

            settype($id, 'int');
            settype($ok, 'int');

            FaqCatGoDel($id, $ok);
            break;

        case "FaqAdmin":
            include("admin/adminfaq.php");
            FaqAdmin();
            break;

        case "FaqCatGo":
            include("admin/adminfaq.php");

            settype($id_cat, 'int');

            FaqCatGo($id_cat);
            break;

            // AUTOMATED
        case 'autoStory':
        case 'autoEdit':
        case 'autoDelete':
        case 'autoSaveEdit':
            include("admin/automated.php");
            break;

            // NEWS
        case 'submissions':
            include("admin/submissions.php");
            break;

            // REFERANTS
        case 'HeadlinesDel':
        case 'HeadlinesAdd':
        case 'HeadlinesSave':
        case 'HeadlinesAdmin':
        case 'HeadlinesEdit':
            include("admin/headlines.php");
            break;

            // PREFERENCES
        case 'Configure':
        case 'ConfigSave':
            include("admin/settings.php");
            break;

            // EPHEMERIDS
        case 'Ephemeridsedit':
        case 'Ephemeridschange':
        case 'Ephemeridsdel':
        case 'Ephemeridsmaintenance':
        case 'Ephemeridsadd':
        case 'Ephemerids':
            include("admin/ephemerids.php");
            break;

            // LINKS
        case 'links':
        case 'LinksDelNew':
        case 'LinksAddCat':
        case 'LinksAddSubCat':
        case 'LinksAddLink':
        case 'LinksAddEditorial':
        case 'LinksModEditorial':
        case 'LinksDelEditorial':
        case 'LinksCleanVotes':
        case 'LinksListBrokenLinks':
        case 'LinksDelBrokenLinks':
        case 'LinksIgnoreBrokenLinks':
        case 'LinksListModRequests':
        case 'LinksChangeModRequests':
        case 'LinksChangeIgnoreRequests':
        case 'LinksDelCat':
        case 'LinksModCat':
        case 'LinksModCatS':
        case 'LinksModLink':
        case 'LinksModLinkS':
        case 'LinksDelLink':
        case 'LinksDelVote':
        case 'LinksDelComment':
        case 'suite_links':
            include("admin/links.php");
            break;

            // BANNERS
        case 'BannersAdmin':
        case 'BannersAdd':
        case 'BannerAddClient':
        case 'BannerFinishDelete':
        case 'BannerDelete':
        case 'BannerEdit':
        case 'BannerChange':
        case 'BannerClientDelete':
        case 'BannerClientEdit':
        case 'BannerClientChange':
            include("admin/banners.php");
            break;

            // HTTP Referer
        case 'hreferer':
        case 'delreferer':
        case 'archreferer':
            include("admin/referers.php");
            break;

            // TOPIC Manager
        case 'topicsmanager':
        case 'topicedit':
        case 'topicmake':
        case 'topicdelete':
        case 'topicchange':
        case 'relatedsave':
        case 'relatededit':
        case 'relateddelete':
            include("admin/topics.php");
            break;

            // SECTIONS - RUBRIQUES
        case 'new_rub_section':
        case 'sections':
        case 'sectionedit':
        case 'sectionmake':
        case 'sectiondelete':
        case 'sectionchange':
        case 'rubriquedit':
        case 'rubriquemake':
        case 'rubriquedelete':
        case 'rubriquechange':
        case 'secarticleadd':
        case 'secartedit':
        case 'secartchange':
        case 'secartchangeup':
        case 'secartdelete':
        case 'secartpublish':
        case 'secartupdate':
        case 'secartdelete2':
        case 'ordremodule':
        case 'ordrechapitre':
        case 'ordrecours':
        case 'majmodule':
        case 'majchapitre':
        case 'majcours':
        case 'publishcompat':
        case 'updatecompat':
        case 'droitauteurs':
        case 'updatedroitauteurs':
            include("admin/sections.php");
            break;

            // BLOCKS
        case 'blocks':
            include("admin/blocks.php");
            break;

        case 'makerblock':
        case 'deleterblock':
        case 'changerblock':
        case 'gaucherblock':
            include("admin/rightblocks.php");
            break;

        case 'makelblock':
        case 'deletelblock':
        case 'changelblock':
        case 'droitelblock':
            include("admin/leftblocks.php");
            break;

        case 'ablock':
        case 'changeablock':
            include("admin/adminblock.php");
            break;

        case 'mblock':
        case 'changemblock':
            include("admin/mainblock.php");
            break;

            // STORIES
        case 'DisplayStory':
        case 'PreviewAgain':
        case 'PostStory':
        case 'DeleteStory':
        case 'EditStory':
        case 'ChangeStory':
        case 'RemoveStory':
        case 'adminStory':
        case 'PreviewAdminStory':
            // CATEGORIES des NEWS
        case 'EditCategory':
        case 'DelCategory':
        case 'YesDelCategory':
        case 'NoMoveCategory':
        case 'SaveEditCategory':
        case 'AddCategory':
        case 'SaveCategory':
            include("admin/stories.php");
            break;

            // AUTHORS
        case 'mod_authors':
        case 'modifyadmin':
        case 'UpdateAuthor':
        case 'AddAuthor':
        case 'deladmin':
        case 'deladminconf':
            include("admin/authors.php");
            break;

            // USERS
        case 'mod_users':
        case 'modifyUser':
        case 'updateUser':
        case 'delUser':
        case 'delUserConf':
        case 'addUser':
        case 'extractUserCSV':
        case 'unsubUser':
        case 'nonallowed_users':
        case 'checkdnsmail_users':
            include("admin/users.php");
            break;

            // SONDAGES
        case 'create':
        case 'createPosted':
        case 'remove':
        case 'removePosted':
        case 'editpoll':
        case 'editpollPosted':
        case 'SendEditPoll':
            include("admin/polls.php");
            break;

            // DIFFUSION MI ADMIN
        case "email_user":
        case "send_email_to_user":
            include("admin/email_user.php");
            break;

            // LNL
        case "lnl":
            include("admin/lnl.php");
            break;

        case "lnl_Sup_Header":
            $op = "Sup_Header";
            include("admin/lnl.php");
            break;

        case "lnl_Sup_Body":
            $op = "Sup_Body";
            include("admin/lnl.php");
            break;

        case "lnl_Sup_Footer":
            $op = "Sup_Footer";
            include("admin/lnl.php");
            break;

        case "lnl_Sup_HeaderOK":
            $op = "Sup_HeaderOK";
            include("admin/lnl.php");
            break;

        case "lnl_Sup_BodyOK":
            $op = "Sup_BodyOK";
            include("admin/lnl.php");
            break;

        case "lnl_Sup_FooterOK":
            $op = "Sup_FooterOK";
            include("admin/lnl.php");
            break;

        case "lnl_Shw_Header":
            $op = "Shw_Header";
            include("admin/lnl.php");
            break;

        case "lnl_Shw_Body":
            $op = "Shw_Body";
            include("admin/lnl.php");
            break;

        case "lnl_Shw_Footer":
            $op = "Shw_Footer";
            include("admin/lnl.php");
            break;

        case "lnl_Add_Header":
            $op = "Add_Header";
            include("admin/lnl.php");
            break;

        case "lnl_Add_Header_Submit":
            $op = "Add_Header_Submit";
            include("admin/lnl.php");
            break;

        case "lnl_Add_Header_Mod":
            $op = "Add_Header_Mod";
            include("admin/lnl.php");
            break;

        case "lnl_Add_Body":
            $op = "Add_Body";
            include("admin/lnl.php");
            break;

        case "lnl_Add_Body_Submit":
            $op = "Add_Body_Submit";
            include("admin/lnl.php");
            break;

        case "lnl_Add_Body_Mod":
            $op = "Add_Body_Mod";
            include("admin/lnl.php");
            break;

        case "lnl_Add_Footer":
            $op = "Add_Footer";
            include("admin/lnl.php");
            break;

        case "lnl_Add_Footer_Submit":
            $op = "Add_Footer_Submit";
            include("admin/lnl.php");
            break;

        case "lnl_Add_Footer_Mod":
            $op = "Add_Footer_Mod";
            include("admin/lnl.php");
            break;

        case "lnl_Test":
            $op = "Test";
            include("admin/lnl.php");
            break;

        case "lnl_Send":
            $op = "Send";
            include("admin/lnl.php");
            break;

        case "lnl_List":
            $op = "List";
            include("admin/lnl.php");
            break;

        case "lnl_User_List":
            $op = "User_List";
            include("admin/lnl.php");
            break;

        case "lnl_Sup_User":
            $op = "Sup_User";
            include("admin/lnl.php");
            break;

            // SUPERCACHE
        case 'supercache':
        case 'supercache_save':
        case 'supercache_empty':
            include("admin/overload.php");
            break;

            // OPTIMYSQL
        case 'OptimySQL':
            include("admin/optimysql.php");
            break;

            // SAVEMYSQL
        case 'SavemySQL':
            include("admin/savemysql.php");
            break;

            // EDITO
        case 'Edito':
        case 'Edito_save':
        case 'Edito_load':
            include("admin/adminedito.php");
            break;

            // METATAGS
        case 'MetaTagAdmin':
        case 'MetaTagSave':
            include("admin/metatags.php");
            break;

            // META-LANG
        case 'Meta-LangAdmin':
        case 'List_Meta_Lang':
        case 'Creat_Meta_Lang':
        case 'Edit_Meta_Lang':
        case 'Kill_Meta_Lang':
        case 'Valid_Meta_Lang':
            include("admin/meta_lang.php");
            break;

            // ConfigFiles
        case 'ConfigFiles':
        case 'ConfigFiles_load':
        case 'ConfigFiles_save':
        case 'ConfigFiles_create':
        case 'delete_configfile':
        case 'ConfigFiles_delete':
            include("admin/configfiles.php");
            break;

            // NPDS-Admin-Plugins
        case 'Extend-Admin-Module':
        case 'Extend-Admin-SubModule':
            include("admin/plugins.php");
            // include("themes/default/header.php");
            
            // if ($ModPath != '') {
            //     if (file_exists("modules/$ModPath/http/$ModStart.php")) {
            //         include("modules/$ModPath/http/$ModStart.php"); 
            //     } 
            
            // } else {
            //     url::redirect_url(urldecode($ModStart));
            // }

            break;

            // NPDS-Admin-Groupe
        case 'groupes';
        case 'groupe_edit':
        case 'groupe_maj':
        case 'groupe_add':
        case 'groupe_add_finish':
        case 'bloc_groupe_create':
        case 'retiredugroupe':
        case 'retiredugroupe_all':
        case 'membre_add':
        case 'membre_add_finish':
        case 'pad_create':
        case 'pad_remove':
        case 'note_create':
        case 'note_remove':
        case 'workspace_create':
        case 'workspace_archive':
        case 'forum_groupe_delete':
        case 'forum_groupe_create':
        case 'moderateur_update':
        case 'groupe_mns_create':
        case 'groupe_mns_delete':
        case 'groupe_chat_create':
        case 'groupe_chat_delete':
        case 'groupe_member_ask':
            include('admin/groupes.php');
            break;

            // NPDS-Instal-Modules
        case 'modules':
            include("admin/modules.php");
            break;

        case 'Module-Install':
            include("admin/module-install.php");
            break;

        case 'alerte_api':
        case 'alerte_update':
            include("npds_api.php");
            break;

            // NPDS-Admin-Main
        case 'suite_articles':
            settype($deja_affiches, 'int');

            adminMain($deja_affiches);
            break;
            
        case 'adminMain':
        default:
            adminMain(0);
            break;
    }
} else {
    authors::login();
}
