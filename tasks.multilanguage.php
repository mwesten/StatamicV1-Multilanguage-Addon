<?php

class Tasks_multilanguage extends Tasks
{

    /**
     * Slim App Instance
     * @var \Slim
     */
    private $app;

    /**
     * @var array Language data
     */
    protected $default_lang;
    protected $all_lang;

    private $segments;

    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();

        // Get the Slim Instance
        $this->app = \Slim\Slim::getInstance();

        $this->default_lang = $this->fetchConfig('ml_default_language');
        $this->all_lang = $this->fetchConfig('ml_languages');
        $this->segments = explode('/', ltrim(str_replace(Config::getSiteRoot(), '/', URL::getCurrent()), '/'));

    }



    /**
    * Returns the current language based on the URL,
    * the cookie that was set or the default language if the cookie is not set and the
    * URL isn't valid
    */
    public function currlang()
    {
        $lang = $this->default_lang;
        if (array_key_exists('tls_lang', $_COOKIE)) {
          if (in_array($_COOKIE['tls_lang'], $this->all_lang, true)) {
            $lang = $_COOKIE['tls_lang'];
          }
        }

        if (in_array($this->segments[0], $this->all_lang, true)) {
            return $this->segments[0];
        } else {
            return $lang;
        }    
    }






}
