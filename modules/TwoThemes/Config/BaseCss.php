<?php

/*
|--------------------------------------------------------------------------
| Module Configuration
|--------------------------------------------------------------------------
|
| Here is where you can register all of the Configuration for the Module.
*/

return array(
    ['type' => 'module', 'locale' => null, 'folder' => 'TwoThemes', 'position' => 'header', 'order' => 10.1, 'mode' => 'default', 'url' => 'vendor/font-awesome/css/all.min.css'],
    ['type' => 'module', 'locale' => null, 'folder' => 'TwoThemes', 'position' => 'header', 'order' => 10.2, 'mode' => 'default', 'url' => 'vendor/bootstrap/dist/css/bootstrap.min.css'],
    ['type' => 'module', 'locale' => null, 'folder' => 'TwoThemes', 'position' => 'header', 'order' => 10.3, 'mode' => 'default', 'url' => 'vendor/bootstrap/dist/css/extra.css'],
    ['type' => 'module', 'locale' => null, 'folder' => 'TwoThemes', 'position' => 'header', 'order' => 10.4, 'mode' => 'default', 'url' => 'vendor/formvalidation/dist/css/formValidation.min.css'],
    ['type' => 'module', 'locale' => null, 'folder' => 'TwoThemes', 'position' => 'header', 'order' => 10.5, 'mode' => 'default', 'url' => 'css/jquery-ui.min.css'],
    ['type' => 'module', 'locale' => null, 'folder' => 'TwoThemes', 'position' => 'header', 'order' => 10.6, 'mode' => 'default', 'url' => 'vendor/bootstrap-table/dist/bootstrap-table.min.css'],
    ['type' => 'module', 'locale' => null, 'folder' => 'TwoThemes', 'position' => 'header', 'order' => 10.7, 'mode' => 'default', 'url' => 'css/prism.css'],

    ['type' => 'theme', 'locale' => 'code', 'folder' => 'TwoFrontend', 'position' => 'header', 'order' => 10.8, 'mode' => 'default', 'url' => 'css/%s-style.css'],
    ['type' => 'theme', 'locale' => 'code', 'folder' => 'TwoFrontend', 'position' => 'header', 'order' => 10.9, 'mode' => 'default', 'url' => 'css/%s-style-AA.css'],
    ['type' => 'theme', 'locale' => 'iso',  'folder' => 'TwoFrontend', 'position' => 'header', 'order' => 11.0, 'mode' => 'default', 'url' => 'css/%s-print.css'],
    ['type' => 'theme', 'locale' => null,   'folder' => 'TwoFrontend', 'position' => 'header', 'order' => 11.1, 'mode' => 'default', 'url' => 'css/style.css'],
    ['type' => 'theme', 'locale' => null,   'folder' => 'TwoFrontend', 'position' => 'header', 'order' => 11.2, 'mode' => 'default', 'url' => 'css/style-AA.css'],
    ['type' => 'theme', 'locale' => null,   'folder' => 'TwoFrontend', 'position' => 'header', 'order' => 11.3, 'mode' => 'default', 'url' => 'css/print.css'],
    ['type' => 'theme', 'locale' => null,   'folder' => 'TwoFrontend', 'position' => 'header', 'order' => 11.3, 'mode' => 'default', 'url' => 'css/admin.css'],

    // test
    //['type' => 'theme', 'locale' => null, 'folder' => 'TWoFrontend', 'position' => 'header', 'order' => 12, 'mode' => 'default', 'url' => 'css/printnicolas.css'],
    //['type' => 'theme', 'locale' => null, 'folder' => 'TWoFrontend', 'position' => 'header', 'order' => 12.1, 'mode' => 'default', 'url' => 'css/coco.css'],
);