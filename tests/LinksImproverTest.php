<?php

namespace Piedweb\LinksImprover\Tests;

use PHPUnit\Framework\TestCase;
use Piedweb\LinksImprover\LinksImprover;
use Piedweb\LinksImprover\LinksManager;

class LinksImproverTest extends TestCase
{
    public static function getLinks()
    {
        return [
                'https://google.com',
        ];
    }

    public static function getHtml()
    {
        return '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore '
            .'et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut '
            .'aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum '
            .'dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui '
            .'officia deserunt mollit anim id est laborum.</p>'
            .'<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, <a href=https://google.com>GOogle</a>.</p>';
    }

    public static function getLinksManager()
    {
        return LinksManager::load(
            'url,kws,force,counter'.chr(10)
            .'https://piedweb.com,"Pied Web,Robin from Pied Web",2,11'.chr(10)
            .'https://google.com,"Google,google.com",2,10'.chr(10)
            .'https://lorem.com,"minim veniam2,Excepteur sint occaecat",2,10'.chr(10)
        );
    }

    /** @test */
    public function analyzeContentTest()
    {
        $linksImprover = new LinksImprover(self::getHtml());

        $this->assertSame($linksImprover->getExistingLinks()[0], self::getLinks()[0]);
        $this->assertSame($linksImprover->getWordCount(), 78);
    }

    /** @test */
    public function impoveContentTest()
    {
        $linksImprover = new LinksImprover(self::getHtml());

        $newContent = $linksImprover->improve(self::getLinksManager(), 1 / 20);

        $this->assertSame(
            $newContent,
            str_replace('Excepteur sint occaecat', '<a href="https://lorem.com">Excepteur sint occaecat</a>', self::getHtml())
        );
    }

    /** @test */
    public function addedLinksTest2()
    {
        $linksImprover = new LinksImprover(self::getHtml());

        $newContent = $linksImprover->improve(self::getLinksManager(), 1 / 20);

        $this->assertSame($linksImprover->getAddedLinksCount(), 1) ;
        $this->assertSame($linksImprover->getAddedLinks(), [['Excepteur sint occaecat', 'https://lorem.com']]);
        $this->assertSame($linksImprover->getAddedLinks(true), [ 'https://lorem.com']);
    }

    /** @test */
    public function addLinkOnTextInTagTest()
    {
        $linksImprover = new LinksImprover(self::getHtml().'<h3>Create a link on me</h3>');
        $linksImprover->setTagsInsideLinkCouldBeAdded(LinksImprover::TAGS_EXTENDED);
        $newContent = $linksImprover->improve(LinksManager::load('https://exemple.tld,"Create a link on me"'), 1 / 20);
        $this->assertSame($linksImprover->getAddedLinksCount(), 1) ;
    }
}
