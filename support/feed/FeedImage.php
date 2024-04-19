<?php

declare(strict_types=1);

namespace npds\support\feed;

use npds\support\feed\HtmlDescribable;


/**
 * An FeedImage may be added to a FeedCreator feed.
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 * @since 1.3
 */
class FeedImage extends HtmlDescribable
{
    /**
     * Mandatory attributes of an image.
     */
    var $title, $url, $link;

    /**
     * Optional attributes of an image.
     */
    var $width, $height, $description;
}