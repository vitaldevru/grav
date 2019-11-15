<?php

/**
 * @package    Grav\Common\Page
 *
 * @copyright  Copyright (C) 2015 - 2019 Trilby Media, LLC. All rights reserved.
 * @license    MIT License; see LICENSE file for details.
 */

namespace Grav\Common\Page\Medium;

use Grav\Common\Markdown\Parsedown;
use Grav\Common\Page\Markdown\Excerpts;

trait ParsedownHtmlTrait
{
    /**
     * @var \Grav\Common\Markdown\Parsedown|null
     */
    protected $parsedown;

    /**
     * Return HTML markup from the medium.
     *
     * @param string|null $title
     * @param string|null $alt
     * @param string|null $class
     * @param string|null $id
     * @param bool $reset
     * @return string
     */
    public function html($title = null, $alt = null, $class = null, $id = null, $reset = true)
    {
        $element = $this->parsedownElement($title, $alt, $class, $id, $reset);

        if (!$this->parsedown) {
            $this->parsedown = new Parsedown(new Excerpts());
        }

        return $this->parsedown->elementToHtml($element);
    }
}
