<?php

namespace Piedweb\LinksImprover;

class Link
{
    protected $url;
    protected $kws = [];
    protected $force = 0;
    protected $counter = 0;

    /**
     * Get the value of url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set the value of url
     *
     * @return  self
     */
    public function setUrl($url, $removeBase = false)
    {
        if ($removeBase === true) {
            $url = preg_replace('/^https?:\/\/[^#?\/]+/i', '', $url);
        }

        $this->url = $url;

        return $this;
    }

    /**
     * Get the value of force
     */
    public function getForce()
    {
        return $this->force;
    }

    /**
     * Set the value of force
     *
     * @return  self
     */
    public function setForce($force)
    {
        $this->force = $force;

        return $this;
    }

    /**
     * Get the value of counter
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * Set the value of counter
     *
     * @return  self
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;

        return $this;
    }

    public function incrementCounter($int = 1)
    {
        $this->counter = $this->counter + $int;

        return $this;
    }

    /**
     * Get the value of kws
     */
    public function getKws()
    {
        return $this->kws;
    }

    /**
     * Set the value of kws
     *
     * Parse kws wildcard and keep original submitted kw in index (avoid duplicate kw too)
     *
     * @return  self
     */
    public function setKws($kws)
    {
        $kws = explode(',', $kws);

        foreach ($kws as $kw) {
            $this->kws[$kw] = ' '.trim(str_replace('*', '[^<]*', preg_quote($kw, '/'))).' ';
        }

        uksort($this->kws, function ($a, $b) {
            return strlen($b) <=> strlen($a);
        });

        if (empty($this->kws)) {
            throw new \Exception('empty kws for '.$this->getUrl());
        }

        return $this;
    }
}
