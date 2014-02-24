<?php

/**
* Plugin: Multi-language support for Pico CMS (http://pico.dev7studios.com)
*
* A plugin to support content in different languages. Introduces "language" and
* "id" page headers. They are accessible through the $meta and $data arrays
* of a page. Essentially, each language version gets a separate content file
* with a different "language" but the same "id".
*
* "language" is  a string that identifies the language. An IETF language tag
* like "en" or "de" is suggested. A page without a language header gets assigned
* the "default language", which can be set in config.php as
* config["default_language"]. (It is "en" otherwise.)
*
* "id" identifies the same page (content-wise) across different languages,
* making it possible to find a different language version of a certain page.
* Same content, different language -> same id.
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
*
*
* @package Pico
* @subpackage Languages
* @version 0.1.1
* @author Til Boerner <tilmanboerner@gmx.net>
* @copyright 2014 Til Boerner
* @link https://github.com/tilboerner/pico_languages
* @license http://opensource.org/licenses/MIT
*
*/
class Pico_Multilanguage {

    private
        $default_language = 'en',       // override with config['default_language']
        $pages_by_language = array(),   // array of page data grouped by language
        $pages_by_id = array();         // array of page data grouped by id

    public function config_loaded(&$settings) {
        if (isset($settings['default_language'])) {
            $this->default_language = $settings['default_language'];
        }
    }

    public function before_read_file_meta(&$headers) {
        $headers['language'] = 'Language';
        $headers['id'] = 'Id';      //
    }

    public function file_meta(&$meta) {
        if (!$meta['language']) {
            $meta['language'] = $this->default_language;
        }
    }

    public function get_page_data(&$data, $page_meta) {
        if ($page_meta['language']) {
            $lang = $page_meta['language'];
        } else {
            $lang = $this->default_language;
        }
        $page_id = $page_meta['id'];

        // set page.language and page.id
        $data['language'] = $lang;
        $data['id'] = $page_id;

        // add page to languages[$lang]
        if (!isset($this->pages_by_language[$lang])) {
            $this->pages_by_language[$lang] = array();
        }
        $this->pages_by_language[$lang][] = $data;

        // add pages with Id to page_languages[ID]
        if($page_id){
            if (!isset($this->pages_by_id[$page_id])) {
                $this->pages_by_id[$page_id] = array();
            }
            $this->pages_by_id[$page_id][] = $data;
        }
    }

    public function get_pages(&$pages, &$current_page, &$prev_page, &$next_page) {

        // only keep pages with same language as current
        $current_lang = $current_page['language'];
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
            if($current === $current_page){
                    break;
            }
            next($pages);
        }
        $prev_page = next($pages);
        prev($pages);
        $next_page = prev($pages);
    }

    public function before_render(&$twig_vars, &$twig, &$template) {
        $twig_vars['languages'] = array_keys($this->pages_by_language);
        $page_id = $twig_vars['meta']['id'];
        $alt_languages = $this->get_item($this->pages_by_id, $page_id, array());
        $twig_vars['page_languages'] = $alt_languages;
    }

    private function get_item($array, $key, $default='') {
        return isset($array[$key]) ? $array[$key] : $default;
    }
}

?>
