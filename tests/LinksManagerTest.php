<?php

namespace Piedweb\LinksImprover\Tests;

use PHPUnit\Framework\TestCase;
use Piedweb\LinksImprover\LinksManager;

class LinksManagerTest extends TestCase
{
    public function getFirstUrl()
    {
        return 'https://piedweb.com';
    }

    public function getFirstKws()
    {
        return [
            "Robin from Pied Web" => " Robin from Pied Web ",
            "Pied Web" => " Pied Web ",
        ];
    }

    public function getRawData()
    {
        return 'url,kws,force,counter'.chr(10)
            .'https://piedweb.com,"Pied Web,Robin from Pied Web",2,11'.chr(10)
            .'https://google.com,"Google,google.com",2,10'.chr(10)
        ;
    }

    /** @test */
    public function loadRawDataTest()
    {
        $linksManager = LinksManager::load($this->getRawData());

        $this->assertSame($linksManager->getIterator()[0]->getUrl(), $this->getFirstUrl());
        $this->assertSame($linksManager->getIterator()[0]->getKws(), $this->getFirstKws());
    }

    /** @test */
    public function reOrderTest()
    {
        $linksManager = LinksManager::load($this->getRawData());
        $linksManager->reOrder();

        $this->assertSame($linksManager->getIterator()[0]->getUrl(), 'https://google.com');
    }

    /** @test */
    public function loadCSVDataTest()
    {
        $linksManager = LinksManager::load(dirname(__FILE__).'/links.csv');

        $this->assertSame($linksManager->getIterator()[0]->getUrl(), $this->getFirstUrl());
        $this->assertSame($linksManager->getIterator()[0]->getKws(), $this->getFirstKws());
    }

    /** @test */
    public function returnTest()
    {
        $linksManager = LinksManager::load($this->getRawData());

        $this->assertTrue(is_string($this->getRawData())); // ppor test
    }
}
