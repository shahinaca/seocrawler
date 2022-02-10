<?php

namespace shahinaca\crawling;

class CrawlerResult{

    /**
     * Result title
     * @var string
     */
    protected $title = "";

    /**
     * Result description
     * @var string
     */
    protected $description = "";

    /**
     * Result url
     * @var string
     */
    protected $url = "";

    /**
     * Current position result
     * @var integer
     */
    protected $position;
    
    public function hasDomain($domain){
        $urlData = parse_url($this->url);

        return $urlData['host'] == $domain;
    }

    /**
     * Gets the Result title.
     *
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the Result title.
     *
     * @param String $title the title
     *
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Gets the Result description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the Result description.
     *
     * @param string $description the description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Gets the Result url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the Result url.
     *
     * @param string $url the url
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Gets the Current position result.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Sets the Current position result.
     *
     * @param integer $position the position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
}