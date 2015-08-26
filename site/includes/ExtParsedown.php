<?php
require("Parsedown.php");

class ExtParsedown extends Parsedown
{
    # Setting local path here will fix relative links in Markdown articles.
    function setLocalPath($localPath) {
        $this->localPath = $localPath;
        return $this;
    }

    protected $localPath;

    static function isLocal($path) {
        return preg_match('~^(\w+:)?//~', $path) === 0;
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
