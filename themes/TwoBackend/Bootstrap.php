<?php

/**
 * Which CSS files should be loaded by the Content Editors?
 */
Event::listen('content.editor.stylesheets.two_backend', function ()
{
    return array(
        'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
        'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
    );
});
