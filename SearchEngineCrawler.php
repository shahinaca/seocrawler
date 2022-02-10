<?php

namespace shahinaca\crawling;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Client;

class SearchEngineCrawler
{

    /**
     * Current search page
     * @var integer
     */
    protected $currentPage = 0;

    /**
     * Results per page to retrieve
     * @var integer
     */
    protected $rpp = 20;

    /**
     * 
     * @var Engines\SearchEngine
     */
    protected $engine = null;

    /**
     * Keyword to search
     * @var string
     */
    protected $keyword = "";

    /**
     * Timeout between calls in miliseconds
     * @var integer
     */
    protected $timeout = 500;
    
    /**
     * Timeout between calls in miliseconds
     * @var integer
     */
    protected $maxResults = 1;


    /**
     * Constructor
     * @param String
     */
    public function __construct($type){
        $this->type = $type;
        $class = __NAMESPACE__.'\\Engines\\'.$type.'Engine';

        $this->engine = new $class($this->maxResults);
    }

    public function search($keyword = null){
        $this->clear();

        if($keyword != null){
            $this->keyword = $keyword;
        }

        while ($this->next()){
            continue;
        }

        return $this->engine->getResults();
    }

    public function searchForDomain($domain, $keyword = null){
        $this->clear();
        $this->engine->setDomain($domain);

        if ($keyword != null){
            $this->keyword = $keyword;
        }

        while($this->next()){
            continue;
        }

        $results = $this->engine->getResults();
        $last = $results[count($results)-1];

        if($last->hasDomain($domain)){
            var_dump($results[count($results)-1]);
            return $results[count($results)-1];
        }
        else{
            return null;
        }
    }

    /**
     * Clears previous crawlerd data
     * @return [type] [description]
     */
    function clear(){
        $this->currentPage = 0;
        $this->engine->clear();
    }

    /**
     * Loads the next page and returns true if there are new results
     * @return boolean
     */
    public function next(){
        if ($this->engine->hasNextPage()){
            $newPage = $this->fetchPage();
            $this->engine->parsePage($newPage);
            if ($this->engine->hasResults()){
                $this->currentPage++;
                usleep($this->timeout);
                return true;
            }
            else{
              return false;
            }
        }
        else{
          return false;
        }
    }

    public function fetchPage(){
        $url = $this->engine->buildUrl($this->keyword, $this->currentPage, $this->engine->getResultsFetched(), $this->rpp);
        $code = $this->fetchPageCode($url);
        return mb_convert_encoding($code, 'HTML-ENTITIES', "UTF-8");
    }

    public function fetchPageCode($url){
        $client = new Client();
        $response = $client->get($url, $this->crawlerOptions);
        return $response->getBody(true);
    }

    /**
     * Adds a header value to the requests
     * @param string $key   Header key
     * @param string $value Header value
     */
    public function setHeader($key, $value){
        $this->crawlerOptions['headers'][$key] = $value;
    }

    /**
     * Sets a Guzzle request option.
     * See more: http://guzzle3.readthedocs.org/http-client/client.html#request-options
     * @param string $key   Guzzle request option key
     * @param string $value Guzzle request option value
     */
    public function setCrawlerOption($key, $value){
        $this->crawlerOptions[$key] = $value;
    }

    /**
     * Gets the Crawler name.
     *
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the Crawler name.
     *
     * @param String $name the name
     *
     * @return self
     */
    public function setName(String $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the Fetch url
     *
     * @return [type]
     */
    public function getFetchUrl()
    {
        return $this->fetchUrl;
    }

    /**
     * Sets the Fetch url
     *
     * @param String $fetchUrl the fetch url
     *
     * @return self
     */
    public function setFetchUrl(String $fetchUrl)
    {
        $this->fetchUrl = $fetchUrl;

        return $this;
    }

    /**
     * Gets the Limits the amount of results to search for the current keyword.
     *
     * @return integer
     */
    public function getMaxResults()
    {
        return $this->engine->getMaxResults();
    }

    /**
     * Sets the Limits the amount of results to search for the current keyword.
     *
     * @param integer $maxResults the max results
     *
     * @return self
     */
    public function setMaxResults($maxResults)
    {
        $this->engine->setMaxResults($maxResults);

        return $this;
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

    /**
     * Gets the Results per page to retrieve.
     *
     * @return integer
     */
    public function getRpp()
    {
        return $this->rpp;
    }

    /**
     * Gets the Keyword to search.
     *
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Sets the Keyword to search.
     *
     * @param string $keyword the keyword
     *
     * @return self
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Gets the Timeout between calls in miliseconds.
     *
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Sets the Timeout between calls in miliseconds.
     *
     * @param integer $timeout the timeout
     *
     * @return self
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }
}