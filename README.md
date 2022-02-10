
## Install

### Composer

Via command line:

```
$ composer require shahinaca/seocrawler
```

## Usage

```php
  
  // Get a engine using parameter, options available are: "Google", "Yahoo", "Bing"
  $client = new SearchEngineCrawler("Google");
  
  // Set maximum amount of results to fetch (default: 100)
  $client->setMaxResults(30);
  
  // Set timeout between each request (default: 500)
  $client->setTimeout(200);
  
  // Set a header option (default: 
  $client->setHeader("User-Agent", "My awesome user agent/2.3");
  
  // Search results, $r is an array of CrawlerResult object with getTitle(), getDescription() and getUrl() operations
  $r = $client->search('madrid');
  
```
