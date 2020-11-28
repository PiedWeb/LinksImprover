<?php

namespace Piedweb\LinksImprover;

class LinksImproverBBCode extends LinksImprover
{
    protected $hrefRegex = '/url=(["\']([^"\'\]]+)["\']|([^ \]]+))/i';

    protected function canWeCreateALink($word)
    {
        $content = substr($this->content, 0, strpos($this->content, $word));

        // First we check if we are inside a tag
        if (preg_match('/[\[](.)([^\]\[]*)$/D', $content)) {
            return false;
        }

        // Are we in an other bbc tag
        if (preg_match(
            '/\[([^\]]* ?)[^\]]*\]([^\[\]])*$/D',
            preg_replace('/(\[[^p].*\]([^\]]*)\[\/[^p]*\])/i', '', $content), // we remove closed tags
            $match
        )
            ) {
            return false;
        }

        if (preg_match('/\[\/url\]$/i', $content)) {// If yes, does the previous content is a word or a link
            return false;
        }

        return true;
    }

    protected function createLink($url, $anchor, $attr)
    {
        return ' [url='.$url.']'.trim($anchor).'[/url] ';
    }
}
