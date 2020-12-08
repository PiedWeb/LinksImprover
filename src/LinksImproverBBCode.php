<?php

namespace Piedweb\LinksImprover;

class LinksImproverBBCode extends LinksImprover
{
    protected $hrefRegex = '/url=(["\']([^"\'\]]+)["\']|([^ \]]+))/i';

    protected function canWeCreateALink($wordPos)
    {
        $content = substr($this->content, 0, $wordPos);

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

        if (preg_match('/\[\/url\]\s+$/i', $content)) {// If yes, does the previous content is a word or a link
            return false;
        }

        return true;
    }

    protected function getLinkToAdd($url, $anchor, $attr)
    {
        return '[url='.(strpos($url, ' ') !== false?'"'.$url.'"':$url).']'.trim($anchor).'[/url]';
    }
}
