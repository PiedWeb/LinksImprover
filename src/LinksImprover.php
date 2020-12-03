<?php

namespace Piedweb\LinksImprover;

class LinksImprover
{
    protected $linksManager;
    protected $content;

    protected $existingLinks = [];

    /**
     * Caching links added format
     * [anchor, url],
     * ...
     *
     * @var array
     */
    protected $addedLinks = [];

    protected $wordCount = 0;

    protected $hrefRegex = '/href=(["\']([^"\'>]+)["\']|([^ >]+))/i';

    protected $tagsInsideLinkCouldBeAdded = [
        'p', 'p ', 'span', 'span ', 'b', 'b ',
        'strong', 'strong ', 'em', 'em ', 'i', 'i ',
    ];
    const TAGS_EXTENDED = [
        'p', 'p ', 'span', 'span ', 'b', 'b ',
        'strong', 'strong ', 'em', 'em ', 'i', 'i ',
        'h2', 'h2 ', 'h3', 'h3 ', 'h4', 'h4 ', 'h5', 'h5 ',
        'div', 'div ',
    ];

    public function __construct(string $content)
    {
        $this->content = $content;
        //$this->linksManager = $linksManager;

        $this->countWords();
        $this->indexLinks();
    }

    protected function linkEverExist($link)
    {
        if (in_array($link->getUrl(), $this->getExistingLinks())) { // it don't check without or with domain !!!
            return true;
        }
    }

    public function improve(LinksManager $linksManager, $maxLink, $linkAttrToAdd = '') : string
    {
        foreach ($linksManager->get() as $link) {
            if ($this->mustStop($maxLink)) {
                break;
            }

            if ($this->linkEverExist($link) === true) {
                continue;
            }

            if (! preg_match('/('.implode('|', $link->getKws()).')/si', $this->content, $matches1)
                && ! preg_match('/>('.implode(' |>', array_map("trim", $link->getKws())).') /si', $this->content, $matches2)
                && ! preg_match('/ ('.implode('<| ', array_map("trim", $link->getKws())).')</si', $this->content, $matches3)
                && ! preg_match('/>('.implode('>|<', array_map("trim", $link->getKws())).')</si', $this->content, $matches4)
            ) {
                continue;
            }

            for ($i = 1;$i <= 4;$i++) {
                $var = 'matches'.$i;
                if (isset($$var[1]) && $this->canWeCreateALink($$var[1])) {
                    $this->createLink($link, $$var[1], $linkAttrToAdd, $i === 1 ? false : true);
                }
            }
        }

        return $this->content;
    }

    protected function advancedTrim($str)
    {
        $str = str_replace('> <a href', '><a href', $str);
        $str = str_replace('</a> <', '</a><', $str);

        return preg_replace("/\s+/", ' ', $str);
    }

    protected function createLink(Link $link, $anchor, $attr, $trimed = false)
    {
        $newContent = substr($this->content, 0, strpos($this->content, $anchor));
        $newContent .= $this->getLinkToAdd($link->getUrl(), $anchor, $attr);
        $newContent .= substr($this->content, strpos($this->content, $anchor) + strlen($anchor));
        $this ->content = $trimed ? $this->advancedTrim($newContent) : $newContent;

        $this->existingLinks[] = $link->getUrl();
        $this->addedLinks[] = [trim($anchor), $link->getUrl()];

        $link->incrementCounter();
    }

    protected function getLinkToAdd($url, $anchor, $attr)
    {
        return ' <a href="'.$url.'"'.($attr ? ' '.$attr:'').'>'.trim($anchor).'</a> ';
    }

    public function setTagsInsideLinkCouldBeAdded(array $tags)
    {
        $this->tagsInsideLinkCouldBeAdded = $tags;

        return $this;
    }

    protected function canWeCreateALink($word)
    {
        $content = substr($this->content, 0, strpos($this->content, $word));
        //var_dump($content);
        // First we check if we are inside a tag
        if (preg_match('/[<](.)([^><]*)$/D', $content)) {
            return false;
        }

        // If not, are we in a P tag or in a SPAN or
        if (! preg_match(
            '/<([a-z0-9]+ ?)[^>]*>([^<>])*$/D',
            preg_replace('/(<[^p\/].*>([^>]*)<\/[^p]*>)/i', '', $content), // we remove closed tags
            $match
        )
            ) {
            return false;
        }

        if (! in_array($match[1], $this->tagsInsideLinkCouldBeAdded)) {
            return false;
        }

        if (preg_match('/<\/a>$/i', $content)) {// If yes, does the previous content is a word or a link
            return false;
        }

        return true;
    }

    protected function mustStop($maxLink)
    {
        return ($maxLink < 1 ? $this->getWordCount() * $maxLink : $maxLink) - $this->getLinksCount() < 1;
    }

    protected function countWords()
    {
        $this->wordCount = str_word_count(strip_tags($this->content));
    }

    protected function indexLinks()
    {
        preg_match_all($this->hrefRegex, $this->content, $matches);

        $matches = array_merge($matches[2], $matches[3]);
        $matches = array_filter($matches, fn ($value) => $value != '');
        $this->existingLinks = array_values($matches);
    }

    /**
     * Get the value of wordCount
     */
    public function getWordCount(): int
    {
        return $this->wordCount;
    }

    /**
     * Get the value of existingLinks
     */
    public function getExistingLinks(): array
    {
        return $this->existingLinks;
    }

    public function addExistingLink($link)
    {
        $this->existingLinks[] = $link;

        return $this;
    }

    public function addExistingLinks($links)
    {
        $this->existingLinks = array_merge($links, $this->existingLinks);

        return $this;
    }

    public function getLinksCount(): int
    {
        return count($this->getExistingLinks());
    }

    public function getAddedLinksCount(): int
    {
        return count($this->addedLinks);
    }

    public function getAddedLinks($urlOnly = false)
    {
        if ($urlOnly) {
            return array_column($this->addedLinks, 1);
        }

        return $this->addedLinks;
    }
}
