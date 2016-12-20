<?php
require("Parsedown.php");

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

    # Override for img elements with title: put them inside figure elements and give them a caption.
    protected function element(array $Element) {

        if ($Element['name'] != 'img') {
            return parent::element($Element);
        }

        $markup = '';
        if (isset($Element['attributes']['title'])) {
            $markup = '<figure>';
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
