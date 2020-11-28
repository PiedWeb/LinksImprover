<?php

namespace Piedweb\LinksImprover;

use Countable;
use IteratorAggregate;
use League\Csv\Reader;

class LinksManager //implements IteratorAggregate, Countable
{
    protected $values = [];

    /**
     *
        // expected
        // 0: url
        // 1: kws
        // 2: force
        // 3: counter
     *
     */
    public function __construct($values, $base = null)
    {
        foreach ($values as $index => $row) {
            if ($index === 0 && $row[0] == 'url') {
                continue;
            }

            $this->values[$index] = new Link();

            $this->values[$index]->setUrl($row[0], $base === null || strpos($row[0], $base) !== 0 ? false : true);

            $this->values[$index]->setKws(trim($row[1], '"'));

            if (isset($row[2])) {
                $this->values[$index]->setForce(intval($row[2]));
            }

            if (isset($row[3])) {
                $this->values[$index]->setCounter(intval($row[3]));
            }
        }
    }

    public function reOrder()
    {
        // On trie d'abord par counter (les plus petits compteurs d'abord)
        uasort($this->values, function ($a, $b) {
            return $a->getCounter() <=> $b->getCounter();
        });

        // On trie ensuite par force (pour maintenir le trie par force prioritaire)
        uasort($this->values, function ($a, $b) {
            return $b->getForce() <=> $a->getForce();
        });
    }

    public static function load($raw = '', $base = null)
    {
        if (file_exists($raw)) {
            $csv = Reader::createFromPath($raw, 'r');
        } else {
            $csv = Reader::createFromString($raw);
        }

        return new self($csv->getRecords(), $base);
    }

    public function return()
    {
        $csv = 'url,kws,force,counter'.chr(10);

        foreach ($this->get() as $l) {
            $csv .= $l->getUrl().',';
            $csv .= '"'.str_replace('"', '\"', implode(',', array_keys($l->getKws()))).'",';
            $csv .= $l->getForce();
            $csv .= $l->getCounter();
            $csv .= chr(10);
        }

        return $csv;
    }

    public function get()
    {
        return $this->getIterator();
    }

    public function getIterator()
    {
        return array_values($this->values);
    }

    public function clear() : void
    {
        $this->values = [];
    }

    public function copy() : LinksManager
    {
        return new LinksManager($this->values);
    }

    public function isEmpty() : bool
    {
        return empty($this->values);
    }

    public function toArray() : array
    {
        return $this->values;
    }

    public function count(): int
    {
        return count($this->values);
    }
}
