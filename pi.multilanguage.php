<?php

class Plugin_multilanguage extends Plugin
{
    public $meta = array(
    'name'       => 'Multilanguage Plugin',
    'version'    => '0.1.0',
    'author'     => 'Max Westen',
    'author_url' => 'http://DLMax.org'
  );

  function __construct()
  {
    parent::__construct();
    $this->default_lang = $this->fetchConfig('ml_default_language');
    $this->all_lang = $this->fetchConfig('ml_languages');
    $this->segments = explode('/', ltrim(str_replace(Config::getSiteRoot(), '/', URL::getCurrent()), '/'));
  }


  /*
   * Display debug info -- TESTING
   */
  public function index()
  {

    return "Segment1: " . print_r($this->segments[0],true);
  }

  /**
   * return language url-part based on current language
   *
   * Checks if the segment_1 contains one of the accepted language parts.
   * If it does, it returns that part of the url.
   * If it doesn't, it returns the default language part
   *
   **/
  public function url()
  {
    return '/' . $this->currlang();
  }

  /**
   * return the 2 letter language-code based on current language
   *
   * Checks if the segment_1 contains one of the accepted language parts.
   * If it does, it returns that part of the url.
   * If it doesn't, it returns the default language part
   *
   **/
  public function lang()
  {
    return $this->currlang();
  }

  /**
   * Redirect to language page
   *
   * Redirect the user from the root URL to the site-language that is the same as the
   * language the browser is using. If that is not found or available, redirect to the default language.
   *
   **/
  public function autoselect()
  {
    $site_url = Config::getSiteURL();

    $sites = array();
    foreach ($this->all_lang as $lang) {
      $sites[$lang] = $site_url . "/" . $lang;
    }

    // Get 2 char lang code from the browser
    if ((isset($_SERVER)) && (array_key_exists('HTTP_ACCEPT_LANGUAGE',$_SERVER))) {
      $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    } else {
      $lang = $this->default_lang;
    }
    

    // Set default language if a '$lang' version of site is not available
    if (!isset($sites[$lang])) {
      $lang = $this->default_lang;
    }
    // Finally redirect to desired location
    header('Location: ' . $sites[$lang]);
    exit;
  }


  /**
   * Insert a language switcher on the page
   */
  public function switcher()
  {
    $default_lang = $this->fetchConfig('ml_default_language');
    $all_lang = $this->fetchConfig('ml_languages');
    $lang_text = $this->fetchConfig('ml_switch_text', null, null, false, false);
    $prod_paths = $this->fetchConfig('ml_prod_paths', null, null, false, false);
    $prod_urlroot = $this->fetchConfig('ml_prod_produrlroot', null, null, false, false);
    $currlang = $this->currlang();
    $output = array();
    $output['switch_text'] = $lang_text[$currlang];
    $output['languages'] = array();
    $curr_prod_paths = $prod_paths[$currlang];
    
    $curr_page = Path::pretty(URL::getCurrent());
    $entry_data = Content::get($curr_page);

    $uri = ltrim($curr_page, '/');
    $urlparts = explode("/",$uri);

    $curr_prod_paths_country = null;
    $curr_prod_paths_path = null;
    if (count($entry_data) < 1)
    {      
      if (array_key_exists($urlparts[1], $curr_prod_paths))
      {
        $curr_prod_paths_path = $curr_prod_paths[$urlparts[1]];
        $curr_prod_paths_prod = $urlparts[2];
        $curr_page = "/" .$curr_prod_paths_path . "/" . $curr_prod_paths_prod;
      }

    }
    $entry_data = Content::get($curr_page);
    foreach ($all_lang as $lang) {  
      $data = array();
      $data['code'] = $lang;
      $data['text'] = $lang_text[$lang];
      $data['url'] = '/' . $lang;
      $alturl = 'alternative_url_' . $lang;
      if (array_key_exists($alturl, $entry_data)) {
        $data['alturl'] = $entry_data[$alturl];
      }
      $altprodurl = 'alternative_prod_url_' . $lang;
      if (array_key_exists($altprodurl, $entry_data)) {
        $currpp = $prod_paths[$lang];
        reset($currpp);
        $first_key = key($currpp);
        $data['altprodurl'] = "/" .$currpp[$first_key] . "/" . $entry_data[$altprodurl];
        $data['altprod'] = $entry_data[$altprodurl];
        $data['produrl'] = $prod_urlroot[$lang] . "/" . $entry_data[$altprodurl];
      }

      $data['is_current'] = ($lang == $currlang) ? true : false;
      array_push($output['languages'], $data);
    }

    return $output;
  }

  /**
   * return the locale based on the language (used by social metadata)
   */
  public function get_locale()
  {
    $locale = $this->fetchConfig('ml_locales', null, null, false, false);
    $currlang = $this->currlang();
    $output = array();
    
    return $locale[$currlang];
  }

  /**
   * Redirects the current URL to a version with the language in it
   *
   * Redirect the user from the current URL to the site-language that was used on
   * the previous page. URI stays the same after the lang-part.
   *
   **/
  public function getcurrentwithlang()
  {
    $lang = $this->default_lang;
    if (array_key_exists('tls_lang', $_COOKIE)) {
      if (in_array($_COOKIE['tls_lang'], $this->all_lang, true)) {
        $lang = $_COOKIE['tls_lang'];
      }
    }
    $raw_url   = Request::getResourceURI();
    $page_url  = Path::tidy($raw_url);
    
    $site_url = Config::getSiteURL();
    $newurl = $site_url . "/" . $lang . $page_url;
    
    header('Location: ' . $newurl);
    exit;
  }



  // Shows a vardump of the provided $var
  public function mldump($var="No value given...")
  {
    echo "<pre>";
    echo var_dump($var);
    echo "</pre>";
  }

  /**
   * Returns the current language based on the URL or the default language if the URL isn't valid
   */
  private function currlang()
  {
    return $this->tasks->currlang();
  }



}
