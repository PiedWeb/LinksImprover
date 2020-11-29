<?php

namespace Piedweb\LinksImprover\Tests;

use PHPUnit\Framework\TestCase;
use Piedweb\LinksImprover\LinksImproverBBCode;
use Piedweb\LinksImprover\LinksManager;

class LinksImproverBBCodeTest extends TestCase
{
    public static function getBBCode()
    {
        return 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore '
            .'et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut '
            .'aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum '
            .'dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui '
            .'officia deserunt mollit anim id est laborum.'.chr(10).chr(10)
            .'Lorem ipsum dolor sit amet, consectetur adipiscing elit, [URL=https://google.com]google[/url].</p>';
    }

    public static function getLinksManager()
    {
        return LinksManager::load(
            'url,kws,force,counter'.chr(10)
            .'https://piedweb.com,"Pied Web,Robin from Pied Web",2,11'.chr(10)
            .'https://google.com,"Google,google.com",2,10'.chr(10)
            .'https://lorem.com,"minim veniam,Excepteur sint occaecat",2,10'.chr(10)
        );
    }

    /** @test */
    public function impoveContentTest()
    {
        $linksImprover = new LinksImproverBBCode(self::getBBCode());

        $newContent = $linksImprover->improve(self::getLinksManager(), 1 / 20);

        $this->assertSame(
            $newContent,
            str_replace('Excepteur sint occaecat', '[url=https://lorem.com]Excepteur sint occaecat[/url]', self::getBBCode())
        );
    }
}
