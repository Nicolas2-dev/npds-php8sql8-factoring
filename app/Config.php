<?php

/**
 * Setup the Storage Path.
 */
define('STORAGE_PATH', BASEPATH .'storage' .DS);

/**
 * PREFER to be used in database calls default is mini_
 */
define('PREFIX', 'npds_');

/**
 * Sform
 */
if (!defined("CRLF")) {
    define('CRLF', "\n");
}

/**
 * Feed Creator
 */
if (!defined("TIME_ZONE")) {
    define("TIME_ZONE", "");
}

/**
 * Feed Creator : Version string.
 **/
if (!defined("FEEDCREATOR_VERSION")) {
    define("FEEDCREATOR_VERSION", "FeedCreator 2.0 for NPDS");
}
