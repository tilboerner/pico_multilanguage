Pico Multilanguage
==================

[Pico][1] plugin for multi-language support.

## Instructions

 Place the PicoMultiLanguage.php into your plugins directory.

## Usage

A plugin to support content in different languages. Introduces `language` and
`pid` page headers. They are accessible through the $meta and $data arrays
of a page. Essentially, each language version gets a separate content file
with a different `language` but the same `pid`.

`language` is  a string that identifies the language. An IETF language tag
like `en` or `de` is suggested. A page without a language header gets assigned
the `default language`, which can be set in config.php as
config[`default_language`]. (It is `en` otherwise.)

`pid` identifies the same page (content-wise) across different languages,
making it possible to find a different language version of a certain page.
Same content, different language -> same pid.

Template variables defined for Twig:

* `{{ languages }}` -
      array of all strings defined in language headers;
* `{{ page_languages }}` -
      page data array of all different language versions of the current page,
      including the current page.

All this can be used to build a language switcher:

      <ul>
      {% for page in page_languages %}
        {% if page.language == meta.language %}
          <li>{{ page.language }}</li>
        {% else %}
          <li><a href="{{ page.url }}">{{ page.language }}</a></li>
        {% endif %}
      {% endfor %}
      </ul>

## Changing the site title automatically

In addition to the regular `site_title` defined in `config.php`, the
multilingual plugin can adjust the `{{site_title}}` twig variable according
to the language of the given page.

      /*
       * BASIC
       */
      $config['site_title'] = 'Website title'; // Site title

      /* ... */

      /*
       * Multilingual site titles
       */
      $config['site_titles'] = array();
      $config['site_titles']['en'] = 'Website title';     // English
      $config['site_titles']['fr'] = 'Titre du site-web'; // French

This creates an array called `site_titles` (as opposed to the default)
`site_title`. `site_titles` uses the same language codes used within
individual pages. If there is no entry given for a page's language, it
will default to the original `site_title` variable set.

This feature means that the header portions of your site's template
(for example) don't need to do any of the language-switching logic.
You may still need it for building your language switcher.

## Notes

This plug-in has been updated to be compatible with pico 1.0, largely by
transferring the code and logic of the earlier version into the framework
of the template plugin provided.

The `id` field used before has been replaced with `pid` to avoid a name collision.


## License

http://opensource.org/licenses/MIT

[1]: http://picocms.org/
