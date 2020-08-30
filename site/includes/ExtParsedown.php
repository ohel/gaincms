<?php
# Copyright 2015-2018, 2020 Olli Helin
# This file is part of GainCMS, a free software released under the terms of the
# GNU General Public License v3: http://www.gnu.org/licenses/gpl-3.0.en.html

require "Parsedown.php";

class ExtParsedown extends Parsedown
{
    static function isLocal($path) {
        return preg_match('~^(\w+:)?//~', $path) === 0;
    }

    protected $localPath;

    # Setting local path here will fix relative links in Markdown articles.
    function setLocalPath($localPath) {
        $this->localPath = $localPath;
        return $this;
    }

    # Fixes non-phrasing elements inside paragraphs, inserted by the overridden element method below.
    # Browsers do fix these anyway on the fly, but the end result isn't clean.
    function text($text) {

        $html = parent::text($text);

        # Fixes <p><a><div>... into <p>...</p><div><a>... (the order of <a> and <div> changes)
        # The regex is known as a tempered greedy token, using a negative lookahead.
        $html = preg_replace(
            "/(<p>(?:(?!<\/p>).)*)(<a.*>)(<div class=\"img-container\">)(.*)<\/div><\/a>(.*)<\/p>/",
            '$1</p>$3$2$4</a></div><p>$5</p>', $html);

        # Fixes <p><div>... into <p>...</p><div>...
        $html = preg_replace(
            "/(<p>(?:(?!<\/p>).)*)(<div class=\"img-container\">.*<\/div>)(.*)<\/p>/",
            '$1</p>$2<p>$3</p>', $html);

        # Removes empty paragraphs possibly created by the two fixes above.
        return str_replace('<p></p>', '', $html);
    }

    # Override for img elements with title: put them inside figure elements and give them a caption.
    # This needs the text method to fix the resulting HTML:
    # non-phrasing elements (div, figure) are not allowed inside paragraphs.
    protected function element(array $Element) {

        if ($Element['name'] != 'img') {
            return parent::element($Element);
        }

        $markup = '<div class="img-container">';
        if (isset($Element['attributes']['title'])) {
            $markup .= '<figure>';
        }
        $markup .= '<img';

        foreach ($Element['attributes'] as $name => $value)
        {
            if ($value === null)
            {
                continue;
            }

            $markup .= ' '.$name.'="'.$value.'"';
        }

        $markup .= ' />';
        if (isset($Element['attributes']['title'])) {
            $markup .= '<figcaption>' . $Element['attributes']['title'] . '</figcaption></figure>';
        }

        $markup .= '</div>';
        return $markup;
    }

    protected function inlineLink($Excerpt) {

        $Link = parent::inlineLink($Excerpt);
        if (!is_array($Link)) {
            return;
        }

        if (ExtParsedown::isLocal($Link['element']['attributes']['href']) && isset($this->localPath)) {
            $Link['element']['attributes']['href'] = $this->localPath . $Link['element']['attributes']['href'];
        }

        return $Link;

    }
}
?>
