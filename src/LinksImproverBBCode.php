<?php

namespace Piedweb\LinksImprover;

class LinksImproverBBCode extends LinksImprover
{
    protected $hrefRegex = '/url=(["\']([^"\'\]]+)["\']|([^ \]]+))/i';

    protected $base;

    protected function removeClosedTags($rawHtml)
    {
        $rawHtml = preg_replace('/(\[([^ \/\]]+).*\]([^\]]*)\[\/(\2)\])/', '', $rawHtml);

        return preg_match('/(\[([^ \/\]]+).*\]([^\]]*)\[\/(\2)\])/', $rawHtml) ? $this->removeClosedTags($rawHtml) : $rawHtml;
    }

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
            $this->removeClosedTags($content), // we remove closed tags
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
        return '[url='.(strpos($url, ' ') !== false ? '"'.$this->formatUrl($url).'"' : $this->formatUrl($url)).']'.trim($anchor).'[/url]';
    }

    protected function formatUrl($url)
    {
        if ($url[0] == '/') {
            return $this->base.$url;
        }

        return $url;
    }

    /**
     * Set the value of base
     *
     * @return  self
     */
    public function setBase($base)
    {
        $this->base = $base;

        return $this;
    }
}
