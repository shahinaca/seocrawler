<?php

namespace shahinaca\seocrawler\Engines;

use shahinaca\seocrawler\CrawlerResult;
use Symfony\Component\DomCrawler\Crawler;
use shahinaca\seocrawler\Engines\SearchEngine;

class YahooEngine extends SearchEngine{

    protected $fetchUrl = "https://es.search.yahoo.com/search?p=[KEYWORD]&b=[FROM]";

    
    public function parsePage($content){
        // Set current content
        $this->currentPageCode = $content;

        // Get results
        $crawler = new Crawler();
        $crawler->addHtmlContent($content);

        $object = &$this;
        $count = $this->resultsFetched;
        $fetched = $this->resultsFetched;
        $maxResults = $this->maxResults;

        $crawler->filter('#web > ol > li .algo')->each(function(Crawler $match, $i) use ($object){
            if ($this->finished){
                return;
            }

            $r = new CrawlerResult();

            // Get title and url
            $titleMatch = $match->filter("h3 a");
            if ($titleMatch->count() == 1){
                $r->setTitle($titleMatch->eq(0)->text());
                $r->setUrl($titleMatch->eq(0)->attr("href"));
            }

            // Get description
            $descriptionMatch = $match->filter(".compText");
            if ($descriptionMatch->count() == 1){
                $r->setDescription($descriptionMatch->eq(0)->text());
            }

            // Add result
            $object->addResult($r);
            $object->increaseResultsFetched(1);
        });

        // Check if there are more pages to retrieve
        if($crawler->filter('.compPagination .next')->count() == 0){
            echo "No next page";
            $this->finished = true;
        }
    }
    

    public function buildUrl($keyword, $currentPage, $currentIndex, $rpp){
        return str_replace(
            ['[KEYWORD]', '[FROM]'],
            [urlencode($keyword), $currentIndex+1],
            $this->fetchUrl
        );
    }
}