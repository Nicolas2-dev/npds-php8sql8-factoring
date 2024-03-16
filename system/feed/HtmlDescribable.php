<?php

declare(strict_types=1);

namespace npds\system\feed;

use npds\system\feed\FeedHtmlField;


/**
 * An HtmlDescribable is an item within a feed that can have a description that may
 * include HTML markup.
 */
class HtmlDescribable
{
    /**
     * Indicates whether the description field should be rendered in HTML.
     */
    var $descriptionHtmlSyndicated;

    /**
     * Indicates whether and to how many characters a description should be truncated.
     */
    var $descriptionTruncSize;

    /**
     * Returns a formatted description field, depending on descriptionHtmlSyndicated and
     * $descriptionTruncSize properties
     * @return    string    the formatted description
     */
    function getDescription()
    {
        $descriptionField = new FeedHtmlField($this->description); // a revoir pas claire !!!!
        $descriptionField->syndicateHtml = $this->descriptionHtmlSyndicated;
        $descriptionField->truncSize = $this->descriptionTruncSize;
        
        return $descriptionField->output();
    }
}