<?php

namespace shahinaca\seocrawler\Engines;

use shahinaca\seocrawler\CrawlerResult;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

abstract class SearchEngine
{
    /**
     * Crawler name
     * @var String
     */
    protected $name = "";

    /**
     * Fetch url setted to get results, it replaces the wildcards provided:
     * [KEYWORD]   Current keyword to search
     * [PAGE]      Current page to search
     * [FROM]      From index to search
     * [TO]        To index to search
     * [RPP]       Results per page to search 
     * @var [type]
     */
    protected $fetchUrl = "";

    /**
     * Max number of results to retrieve
     * @var integer
     */
    protected $maxResults = 100;

    /**
     * Counts the number of results fetched as not all pages has 10 results
     * @var integer
     */
    protected $resultsFetched = 0;

    /**
     * The current page code
     * @var string
     */
    protected $currentPageCode = "";

    /**
     * Last results fetched
     * @var Array
     */
    protected $results = [];

    /**
     * Checks if ended results
     * @var boolean
     */
    protected $finished = false;

    /**
     * Domain to end finding values
     * @var string
     */
    protected $domain = null;

    public function buildUrl($keyword, $currentPage, $currentIndex, $rpp){
        return str_replace(
            ['[KEYWORD]', '[PAGE]', '[FROM]', '[TO]', '[RPP]'],
            [urlencode($keyword), $currentPage, $currentIndex, $currentIndex + $rpp, $rpp],
            $this->fetchUrl
        );
    }

    protected function increaseResultsFetched($n){
        $this->resultsFetched += $n;
        if ($this->resultsFetched >= $this->maxResults){
            $this->finished = true;
        }
    }

    public function addResult(CrawlerResult $r){
        $r->setPosition(count($this->results));
        
        $this->results[] = $r;

        if ($this->domain != null){
            if ($r->hasDomain($this->domain)) {
                $this->finished = true;
            }
        }
    }

    public function hasNextPage(){
        return !$this->finished;
    }

    /**
     * Clears previous searched data
     * @return
     */
    public function clear(){
        $this->results = [];
        $this->finished = false;
        $this->currentPageCode = "";
        $this->resultsFetched = 0;
    }

    public abstract function parsePage($content);

    /**
     * Gets the The current page code.
     *
     * @return string
     */
    public function getCurrentPageCode()
    {
        return $this->currentPageCode;
    }

    /**
     * Gets the Last results fetched.
     *
     * @return Array
     */
    public function getResults()
    {
        return $this->results;
    }

    public function hasResults(){
        return count($this->results) > 0;
    }

    /**
     * Gets the Counts the number of results fetched as not all pages has 10 results.
     *
     * @return integer
     */
    public function getResultsFetched()
    {
        return $this->resultsFetched;
    }

    /**
     * Sets the Counts the number of results fetched as not all pages has 10 results.
     *
     * @param integer $resultsFetched the results fetched
     *
     * @return self
     */
    public function setResultsFetched($resultsFetched)
    {
        $this->resultsFetched = $resultsFetched;

        return $this;
    }

    /**
     * Gets the Max number of results to retrieve.
     *
     * @return integer
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * Sets the Max number of results to retrieve.
     *
     * @param integer $maxResults the max results
     *
     * @return self
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;

        return $this;
    }

    /**
     * Gets the Domain to end finding values.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the Domain to end finding values.
     *
     * @param string $domain the domain
     *
     * @return self
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }
}