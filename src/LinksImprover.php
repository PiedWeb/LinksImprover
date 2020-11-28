<?php

namespace Piedweb\LinksImprover;

class LinksImprover
{
    protected $linksManager;
    protected $content;

    protected $existingLinks = [];

    protected $wordCount = 0;
    protected $addedLinksCount = 0;

    public function __construct(string $content)
    {
        $this->content = $content;
        //$this->linksManager = $linksManager;

        $this->countWords();
        $this->indexLinks();
    }

    public function getXmlContent()
    {
        if (! $this->xmlContent) {
            $this->xmlContent = simplexml_load_file($this->content);
        }

        return $this->xmlContent;
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

            if (preg_match('/('.implode('|', $link->getKws()).')/si', $this->content, $matches)) {
                $potentialAnchor = $matches[1];
                if ($this->canWeCreateALink($potentialAnchor)) {
                    $newContent = substr($this->content, 0, strpos($this->content, $potentialAnchor));
                    $newContent .= '<a href="'.$link->getUrl().'"'.($linkAttrToAdd ? ' '.$linkAttrToAdd:'').'>';
                    $newContent .= $potentialAnchor.'</a>';
                    $newContent .= substr($this->content, strpos($this->content, $potentialAnchor) + strlen($matches[1]));
                    $this ->content = $newContent;
                    $this->existingLinks[] = $link->getUrl();
                    $link->incrementCounter();
                    $this->addedLinksCount++;
                }
            }
        }

        return $this->content;
    }

    protected function canWeCreateALink($word)
    {
        $content = substr($this->content, 0, strpos($this->content, $word));

        // First we check if we are inside a tag
        if (preg_match('/[<](.)([^><]*)$/D', $content)) {
            return false;
        }

        // If not, are we in a P tag or in a SPAN or
        if (! preg_match(
            '/<([^>]* ?)[^>]*>([^<>])*$/D',
            preg_replace('/(<[^p].*>([^>]*)<\/[^p]*>)/i', '', $content), // we remove closed tags
            $match
        )
            ) {
            return false;
        }

        if (! in_array($match[1], ['p', 'p ', 'span', 'span ', 'strong', 'strong ', 'em', 'em ', 'i', 'i '])) {
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
        preg_match_all('/href=(["\']([^"\'>]+)["\']|([^ >]+))/i', $this->content, $matches);

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

    public function getLinksCount(): int
    {
        return count($this->getExistingLinks());
    }

    /**
     * Get the value of addedLinksCount
     */
    public function getAddedLinksCount(): int
    {
        return $this->addedLinksCount;
    }

    public function resetAddedLinkCount(): void
    {
        $this->addedLinksCount = 0;
    }
}
