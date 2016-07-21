<?php

/**
* Plugin: Multi-language support for Pico CMS (http://pico.dev7studios.com)
*
* A plugin to support content in different languages. Introduces "language" and
* "pid" page headers. They are accessible through the $meta and $data arrays
* of a page. Essentially, each language version gets a separate content file
* with a different "language" but the same "pid".
*
* "language" is  a string that identifies the language. An IETF language tag
* like "en" or "de" is suggested. A page without a language header gets assigned
* the "default language", which can be set in config.php as
* config["default_language"]. (It is "en" otherwise.)
*
* "pid" identifies the same page (content-wise) across different languages,
* making it possible to find a different language version of a certain page.
* Same content, different language -> same pid.
*
* Template variables defined for Twig:
*  - {{ languages }}
*       array of all strings defined in language headers;
*  - {{ page_languages }}
*       page data array of all different language versions of the current page,
*       including the current page.
*
* All this can be used to build a language switcher:
*
* <ul>
* {% for page in page_languages %}
*     {% if page.language == meta.language %}
*         <li>{{ page.language }}</li>
*     {% else %}
*         <li><a href="{{ page.url }}">{{ page.language }}</a></li>
*     {% endif %}
* {% endfor %}
* </ul>
*
* @version 0.2.0
* @author Til Boerner <tilmanboerner@gmx.net>, Richard Martin-Nielsen <richard.martin@utoronto.ca>
* @copyright 2014 Til Boerner
* @link https://github.com/tilboerner/pico_languages
* @license http://opensource.org/licenses/MIT
*
*/

final class PicoMultiLanguage extends AbstractPicoPlugin
{
    /**
     * This plugin is enabled by default?
     *
     * @see AbstractPicoPlugin::$enabled
     * @var boolean
     */
    protected $enabled = true;

    /**
     * This plugin depends on ...
     *
     * @see AbstractPicoPlugin::$dependsOn
     * @var string[]
     */
    protected $dependsOn = array();

	private
        $default_language = 'en',       // override with config['default_language']
        $pages_by_language = array(),   // array of page data grouped by language
        $pages_by_id = array();         // array of page data grouped by id

    /**
     * Triggered after Pico has loaded all available plugins
     *
     * This event is triggered nevertheless the plugin is enabled or not.
     * It is NOT guaranteed that plugin dependencies are fulfilled!
     *
     * @see    Pico::getPlugin()
     * @see    Pico::getPlugins()
     * @param  object[] &$plugins loaded plugin instances
     * @return void
     */
    public function onPluginsLoaded(array &$plugins)
    {
        // your code
    }

    /**
     * Triggered after Pico has read its configuration
     *
     * @see    Pico::getConfig()
     * @param  array &$config array of config variables
     * @return void
     */
    public function onConfigLoaded(array &$config)
    {
        // your code
	  if (isset($settings['default_language'])) {
		$this->default_language = $settings['default_language'];
	  }
    }

    /**
     * Triggered after Pico has evaluated the request URL
     *
     * @see    Pico::getRequestUrl()
     * @param  string &$url part of the URL describing the requested contents
     * @return void
     */
    public function onRequestUrl(&$url)
    {
        // your code
    }

    /**
     * Triggered after Pico has discovered the content file to serve
     *
     * @see    Pico::getBaseUrl()
     * @see    Pico::getRequestFile()
     * @param  string &$file absolute path to the content file to serve
     * @return void
     */
    public function onRequestFile(&$file)
    {
        // your code
    }

    /**
     * Triggered before Pico reads the contents of the file to serve
     *
     * @see    Pico::loadFileContent()
     * @see    DummyPlugin::onContentLoaded()
     * @param  string &$file path to the file which contents will be read
     * @return void
     */
    public function onContentLoading(&$file)
    {
        // your code
    }

    /**
     * Triggered after Pico has read the contents of the file to serve
     *
     * @see    Pico::getRawContent()
     * @param  string &$rawContent raw file contents
     * @return void
     */
    public function onContentLoaded(&$rawContent)
    {
        // your code
    }

    /**
     * Triggered before Pico reads the contents of a 404 file
     *
     * @see    Pico::load404Content()
     * @see    DummyPlugin::on404ContentLoaded()
     * @param  string &$file path to the file which contents were requested
     * @return void
     */
    public function on404ContentLoading(&$file)
    {
        // your code
    }

    /**
     * Triggered after Pico has read the contents of the 404 file
     *
     * @see    Pico::getRawContent()
     * @param  string &$rawContent raw file contents
     * @return void
     */
    public function on404ContentLoaded(&$rawContent)
    {
        // your code
    }

    /**
     * Triggered when Pico reads its known meta header fields
     *
     * @see    Pico::getMetaHeaders()
     * @param  string[] &$headers list of known meta header
     *     fields; the array value specifies the YAML key to search for, the
     *     array key is later used to access the found value
     * @return void
     */
    public function onMetaHeaders(array &$headers)
    {
        // your code
	  $headers['language'] = 'Language';
	  $headers['pid'] = 'pid';
    }

    /**
     * Triggered before Pico parses the meta header
     *
     * @see    Pico::parseFileMeta()
     * @see    DummyPlugin::onMetaParsed()
     * @param  string   &$rawContent raw file contents
     * @param  string[] &$headers    known meta header fields
     * @return void
     */
    public function onMetaParsing(&$rawContent, array &$headers)
    {
        // your code
    }

    /**
     * Triggered after Pico has parsed the meta header
     *
     * @see    Pico::getFileMeta()
     * @param  string[] &$meta parsed meta data
     * @return void
     */
    public function onMetaParsed(array &$meta)
    {
        // your code
	  if (!$meta['language']) {
		$meta['language'] = $this->default_language;
	  }
    }

    /**
     * Triggered before Pico parses the pages content
     *
     * @see    Pico::prepareFileContent()
     * @see    DummyPlugin::prepareFileContent()
     * @see    DummyPlugin::onContentParsed()
     * @param  string &$rawContent raw file contents
     * @return void
     */
    public function onContentParsing(&$rawContent)
    {
        // your code
    }

    /**
     * Triggered after Pico has prepared the raw file contents for parsing
     *
     * @see    Pico::parseFileContent()
     * @see    DummyPlugin::onContentParsed()
     * @param  string &$content prepared file contents for parsing
     * @return void
     */
    public function onContentPrepared(&$content)
    {
        // your code
    }

    /**
     * Triggered after Pico has parsed the contents of the file to serve
     *
     * @see    Pico::getFileContent()
     * @param  string &$content parsed contents
     * @return void
     */
    public function onContentParsed(&$content)
    {
        // your code
    }

    /**
     * Triggered before Pico reads all known pages
     *
     * @see    Pico::readPages()
     * @see    DummyPlugin::onSinglePageLoaded()
     * @see    DummyPlugin::onPagesLoaded()
     * @return void
     */
    public function onPagesLoading()
    {
        // your code
    }

    /**
     * Triggered when Pico reads a single page from the list of all known pages
     *
     * The `$pageData` parameter consists of the following values:
     *
     * | Array key      | Type   | Description                              |
     * | -------------- | ------ | ---------------------------------------- |
     * | id             | string | relative path to the content file        |
     * | url            | string | URL to the page                          |
     * | title          | string | title of the page (YAML header)          |
     * | description    | string | description of the page (YAML header)    |
     * | author         | string | author of the page (YAML header)         |
     * | time           | string | timestamp derived from the Date header   |
     * | date           | string | date of the page (YAML header)           |
     * | date_formatted | string | formatted date of the page               |
     * | raw_content    | string | raw, not yet parsed contents of the page |
     * | meta           | string | parsed meta data of the page             |
     *
     * @see    DummyPlugin::onPagesLoaded()
     * @param  array &$pageData data of the loaded page
     * @return void
     */
    public function onSinglePageLoaded(array &$pageData)
    {
        // your code
	  $page_meta = $pageData['meta'];
	     if ($page_meta['language']) {
            $lang = $page_meta['language'];
        } else {
            $lang = $this->default_language;
        }
        $page_id = $page_meta['pid'];

        // set page.language and page.id
        $pageData['language'] = $lang;
        $pageData['pid'] = $page_id;

        // add page to languages[$lang]
        if (!isset($this->pages_by_language[$lang])) {
            $this->pages_by_language[$lang] = array();
        }
        $this->pages_by_language[$lang][] = $pageData;

        // add pages with Id to page_languages[ID]
        if($page_id){
            if (!isset($this->pages_by_id[$page_id])) {
                $this->pages_by_id[$page_id] = array();
            }
            $this->pages_by_id[$page_id][] = $pageData;
        }
    }

    /**
     * Triggered after Pico has read all known pages
     *
     * See {@link DummyPlugin::onSinglePageLoaded()} for details about the
     * structure of the page data.
     *
     * @see    Pico::getPages()
     * @see    Pico::getCurrentPage()
     * @see    Pico::getPreviousPage()
     * @see    Pico::getNextPage()
     * @param  array[]    &$pages        data of all known pages
     * @param  array|null &$currentPage  data of the page being served
     * @param  array|null &$previousPage data of the previous page
     * @param  array|null &$nextPage     data of the next page
     * @return void
     */
    public function onPagesLoaded(
        array &$pages,
        array &$currentPage = null,
        array &$previousPage = null,
        array &$nextPage = null
    ) {
              // only keep pages with same language as current
        $current_lang = $currentPage['language'];
        if (!$current_lang) {
            return;
        }
        foreach($pages as $key => $page) {
            if($page['language'] != $current_lang) {
                unset($pages[$key]);
            }
        }

        // reset next and previous pages
        reset($pages);
        while($current = current($pages)){
		  if($current === $currentPage){
                    break;
            }
            next($pages);
        }
        $previousPage = next($pages);
        prev($pages);
        $nextPage = prev($pages);
    }

    /**
     * Triggered before Pico registers the twig template engine
     *
     * @return void
     */
    public function onTwigRegistration()
    {
        // your code
    }

    /**
     * Triggered before Pico renders the page
     *
     * @see    Pico::getTwig()
     * @see    DummyPlugin::onPageRendered()
     * @param  Twig_Environment &$twig          twig template engine
     * @param  array            &$twigVariables template variables
     * @param  string           &$templateName  file name of the template
     * @return void
     */
    public function onPageRendering(Twig_Environment &$twig, array &$twigVariables, &$templateName)
    {
        // your code
	    $twigVariables['languages'] = array_keys($this->pages_by_language);
        $page_id = $twigVariables['meta']['pid'];
        $alt_languages = $this->get_item($this->pages_by_id, $page_id, array());
        $twigVariables['page_languages'] = $alt_languages;

    }

    /**
     * Triggered after Pico has rendered the page
     *
     * @param  string &$output contents which will be sent to the user
     * @return void
     */
    public function onPageRendered(&$output)
    {
        // your code
    }

    private function get_item($array, $key, $default='') {
        return isset($array[$key]) ? $array[$key] : $default;
    }

}
