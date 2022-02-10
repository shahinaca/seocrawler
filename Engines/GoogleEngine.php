<?php

namespace shahinaca\seocrawler\Engines;

use shahinaca\seocrawler\CrawlerResult;
use Symfony\Component\DomCrawler\Crawler;
use shahinaca\seocrawler\Engines\SearchEngine;

class GoogleEngine extends SearchEngine{

    protected $fetchUrl = "https://www.google.es/search?q=[KEYWORD]&start=[FROM]";

	
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

        $crawler->filter('#search .rc')->each(function(Crawler $match, $i) use ($object){
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
            $descriptionMatch = $match->filter(".s .st");
            if ($descriptionMatch->count() == 1){
                $r->setDescription($descriptionMatch->eq(0)->text());
            }

            // Add result
            $object->addResult($r);
            $object->increaseResultsFetched(1);
        });

        // Check if there are more pages to retrieve
        if($crawler->filter('#pnnext')->count() == 0){
            $this->finished = true;
        }
	}
	
}