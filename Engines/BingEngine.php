<?php

namespace shahinaca\seocrawler\Engines;

use shahinaca\seocrawler\CrawlerResult;
use Symfony\Component\DomCrawler\Crawler;
use shahinaca\seocrawler\Engines\SearchEngine;

class BingEngine extends SearchEngine{

    protected $fetchUrl = "http://www.bing.com/search?q=[KEYWORD]&first=[FROM]";

    
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

        $crawler->filter('#b_results > li.b_algo')->each(function(Crawler $match, $i) use ($object){
            if ($this->finished){
                return;
            }

            $r = new CrawlerResult();

            // Get title and url
            $titleMatch = $match->filter("h2 a");
            if ($titleMatch->count() == 1){
                $r->setTitle($titleMatch->eq(0)->text());
                $r->setUrl($titleMatch->eq(0)->attr("href"));
            }

            // Get description
            $descriptionMatch = $match->filter(".b_caption p");
            if ($descriptionMatch->count() == 1){
                $r->setDescription($descriptionMatch->eq(0)->text());
            }

            // Add result
            $object->addResult($r);
            $object->increaseResultsFetched(1);
        });

        // Check if there are more pages to retrieve
        if($crawler->filter('.b_pag .sb_pagN')->count() == 0){
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