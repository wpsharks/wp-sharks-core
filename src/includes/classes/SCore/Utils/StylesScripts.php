<?php
/**
 * Styles/scripts.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Styles/scripts.
 *
 * @since 160524 Scripts/styles.
 */
class StylesScripts extends Classes\SCore\Base\Core
{
    /**
     * Version.
     *
     * @since 170128.18158
     *
     * @type string
     */
    protected $cv;

    /**
     * Enqueued.
     *
     * @since 170218.31677
     *
     * @type array Enqueued.
     */
    protected $did_enqueue_libs;

    /**
     * Enqueued.
     *
     * @since 170218.31677
     *
     * @type array Enqueued.
     */
    protected $did_enqueue_styles;

    /**
     * Enqueued.
     *
     * @since 170218.31677
     *
     * @type array Enqueued.
     */
    protected $did_enqueue_scripts;

    /**
     * Class constructor.
     *
     * @since 160524 Scripts/styles.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $core     = $this->c::appCore();
        $this->cv = $core::VERSION;

        $this->did_enqueue_libs    = [];
        $this->did_enqueue_styles  = [];
        $this->did_enqueue_scripts = [];

        add_filter('style_loader_tag', [$this, 'onStyleLoaderTag'], 10, 2);
        add_filter('script_loader_tag', [$this, 'onScriptLoaderTag'], 10, 2);
    }

    /**
     * Did enqueue libs?
     *
     * @since 170218.31677 Scripts/styles.
     *
     * @param string $caller `__METHOD__`.
     *
     * @return array Details, else empty array.
     */
    public function didEnqueueLibs(string $caller): array
    {
        return $this->did_enqueue_libs[$caller] ?? [];
    }

    /**
     * Did enqueue a style?
     *
     * @since 170218.31677 Scripts/styles.
     *
     * @param string $handle Style handle.
     *
     * @return array Details, else empty array.
     */
    public function didEnqueueStyle(string $handle): array
    {
        return $this->did_enqueue_styles[$handle] ?? [];
    }

    /**
     * Did enqueue a script?
     *
     * @since 170218.31677 Scripts/styles.
     *
     * @param string $handle Script handle.
     *
     * @return array Details, else empty array.
     */
    public function didEnqueueScript(string $handle): array
    {
        return $this->did_enqueue_scripts[$handle] ?? [];
    }

    /**
     * On `style_loader_tag`.
     *
     * @since 170218.31677 Scripts/styles.
     *
     * @param string|scalar $tag    HTML markup.
     * @param string|scalar $handle Style handle.
     *
     * @return array Details, else empty array.
     */
    public function onStyleLoaderTag($tag, $handle): string
    {
        $tag    = (string) $tag;
        $handle = (string) $handle;

        if (!($style = $this->didEnqueueStyle($handle))) {
            return $tag; // We did not enqueue this style.
        }
        if ($style['sri'] !== '') { // Obey explicitly empty SRI in config.
            if (($sri = $style['sri']) || ($sri = $this->c::sri($style['url']))) {
                $tag = str_replace(' rel=', ' integrity="'.esc_attr($sri).'" crossorigin="anonymous" rel=', $tag);
            } // NOTE: SRI may or may not be possible right now. Always check if `$sri` is non-empty.
            // NOTE: `c::sri()` also will not return an SRI for local resources on the current hostname.
        }
        return $tag; // Possibly modifed above.
    }

    /**
     * On `script_loader_tag`.
     *
     * @since 170218.31677 Scripts/styles.
     *
     * @param string|scalar $tag    HTML markup.
     * @param string|scalar $handle Script handle.
     *
     * @return array Details, else empty array.
     */
    public function onScriptLoaderTag($tag, $handle): string
    {
        $tag    = (string) $tag;
        $handle = (string) $handle;

        if (!($script = $this->didEnqueueScript($handle))) {
            return $tag; // We did not enqueue.
        }
        if ($script['async']) { // Load script async?
            $tag = str_replace(' src=', ' async src=', $tag);
        }
        if ($script['sri'] !== '') { // Obey explicitly empty SRI in config.
            if (($sri = $script['sri']) || ($sri = $this->c::sri($script['url']))) {
                $tag = str_replace(' src=', ' integrity="'.esc_attr($sri).'" crossorigin="anonymous" src=', $tag);
            } // NOTE: SRI may or may not be possible right now. Always check if `$sri` is non-empty.
            // NOTE: `c::sri()` also will not return an SRI for local resources on the current hostname.
        }
        return $tag; // Possibly modifed above.
    }

    /**
     * Enqueue styles/scripts.
     *
     * @since 170218.31677 Scripts/styles.
     *
     * @param string $caller `__METHOD__`.
     * @param array  $data   Library data.
     *
     * @return array Reverberated `$data`.
     */
    public function enqueueLibs(string $caller, array $data)
    {
        if (!$data) {
            return []; // No data.
        } // Data cannot be empty here.
        // Empty data would cause `did*()` functions
        // to return an empty array, which is unexpected!

        foreach ($data['styles'] ?? [] as $_handle => $_style) {
            if (!$_handle || !is_string($_handle)) {
                continue; // Invalid handle.
            }
            $_version = $_style['version'] ?? '';
            $_ver     = $_style['ver'] ?? null;
            $_url     = $_style['url'] ?? '';
            $_sri     = $_style['sri'] ?? null;
            $_deps    = $_style['deps'] ?? [];
            $_media   = $_style['media'] ?? 'all';

            if ($_version && $_url) { // Version in URL?
                $_url = sprintf($_url, urlencode($_version));
            }
            if ($_url) { // Only if there is a URL to register.
                wp_register_style($_handle, $_url, $_deps, $_ver, $_media);
            }// If no URL, enqueue handle only.

            wp_enqueue_style($_handle); // Immediately.

            $this->did_enqueue_styles[$_handle] = [
                'version' => $_version,
                'ver'     => $_ver,
                'url'     => $_url,
                'sri'     => $_sri,
                'deps'    => $_deps,
                'media'   => $_media,
            ];
        } // unset($_handle, $_script, $_version, $_ver, $_url, $_deps, $_media);

        foreach ($data['scripts'] ?? [] as $_handle => $_script) {
            if (!$_handle || !is_string($_handle)) {
                continue; // Invalid handle.
            }
            $_version   = $_script['version'] ?? '';
            $_ver       = $_script['ver'] ?? null;
            $_url       = $_script['url'] ?? '';
            $_sri       = $_script['sri'] ?? null;
            $_deps      = $_script['deps'] ?? [];
            $_async     = $_script['async'] ?? false;
            $_in_footer = $_script['in_footer'] ?? true;
            $_localize  = $_script['localize'] ?? [];

            if ($_version && $_url) { // Version in URL?
                $_url = sprintf($_url, urlencode($_version));
            }
            if ($_url) { // Only if there is a URL to register.
                wp_register_script($_handle, $_url, $_deps, $_ver, $_in_footer);
            }// If no URL, enqueue handle only.

            wp_enqueue_script($_handle); // Immediately.

            if (!empty($_localize['key']) && array_key_exists('data', $_localize)) {
                wp_localize_script($_handle, $_localize['key'], $_localize['data']);
            }
            $this->did_enqueue_scripts[$_handle] = [
                'version'   => $_version,
                'ver'       => $_ver,
                'url'       => $_url,
                'sri'       => $_sri,
                'deps'      => $_deps,
                'async'     => $_async,
                'in_footer' => $_in_footer,
                'localize'  => $_localize,
            ];
        } // unset($_handle, $_script, $_version, $_ver, $_url, $_deps, $_in_footer, $_localize);

        return $this->did_enqueue_libs[$caller] = $data;
    }

    /**
     * Enqueue Require.js.
     *
     * @since 170128.18158 Require.js.
     *
     * @param array $deps An array of any script dependencies.
     *                    This is helpful in cases where RequireJS should be loaded up
     *                    after other scripts that contain anonymous defines.
     *
     * @return array Library details.
     */
    public function enqueueRequireJsLibs(array $deps = []): array
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'scripts' => [
                'requirejs' => [
                    'version' => '2.3.2',
                    'deps'    => $deps, // If applicable.
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/require.js/%1$s/require.min.js',
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue jQuery.
     *
     * @since 170128.18158 jQuery libs.
     *
     * @return array Library details.
     */
    public function enqueueLatestJQuery(): array
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'scripts' => [
                'jquery' => [
                    'version' => '3.1.1',
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/jquery/%1$s/jquery.min.js',
                ],
            ],
        ];
        wp_deregister_script('jquery');
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue Unicode Gcs libs.
     *
     * @since 170218.31677 Unicode Gcs libs.
     *
     * @return array Library details.
     */
    public function enqueueUnicodeGcsLibs()
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'scripts' => [
                'unicode-gcs' => [
                    'version' => '1.0.3',
                    'url'     => '//cdn.rawgit.com/websharks/unicode-gcs/%1$s/dist/index.min.js',
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue Pako libs.
     *
     * @since 170128.18158 Pako libs.
     *
     * @param string $which Which library?
     *
     * @return array Library details.
     */
    public function enqueuePakoLibs(string $which = 'base')
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        if ($which === 'base') {
            $data = [
                'scripts' => [
                    'pako' => [
                        'version' => '1.0.4',
                        'url'     => '//cdnjs.cloudflare.com/ajax/libs/pako/%1$s/pako.min.js',
                    ],
                ],
            ];
        } elseif ($which === 'deflate') {
            $data = [
                'scripts' => [
                    'pako-deflate' => [
                        'version' => '1.0.4',
                        'url'     => '//cdnjs.cloudflare.com/ajax/libs/pako/%1$s/pako_deflate.min.js',
                    ],
                ],
            ];
        } elseif ($which === 'inflate') {
            $data = [
                'scripts' => [
                    'pako-inflate' => [
                        'version' => '1.0.4',
                        'url'     => '//cdnjs.cloudflare.com/ajax/libs/pako/%1$s/pako_inflate.min.js',
                    ],
                ],
            ];
        }
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue Font Awesome libs.
     *
     * @since 170128.18158 Font Awesome libs.
     *
     * @return array Library details.
     */
    public function enqueueFontAwesomeLibs()
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'styles' => [
                'font-awesome' => [
                    'version' => '4.7.0',
                    'url'     => '//maxcdn.bootstrapcdn.com/font-awesome/%1$s/css/font-awesome.min.css',
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue Sharkicon libs.
     *
     * @since 160709 Sharkicon libs.
     *
     * @return array Library details.
     */
    public function enqueueSharkiconLibs()
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'styles' => [
                'sharkicons' => [
                    'ver' => $this->cv,
                    'url' => $this->c::appCoreUrl('/vendor/websharks/sharkicons/src/long-classes.min.css'),
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue Semantic UI libs.
     *
     * @since 170128.18158 Semantic UI libs.
     *
     * @return array Library details.
     */
    public function enqueueSemanticUiLibs()
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'styles' => [
                'semantic-ui' => [
                    'version' => '2.2.9',
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/semantic-ui/%1$s/semantic.min.css',
                ],
            ],
            'scripts' => [
                'semantic-ui' => [
                    'version' => '2.2.9',
                    'deps'    => ['jquery'],
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/semantic-ui/%1$s/semantic.min.js',
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue Behave libs.
     *
     * @since 170128.18158 Behave libs.
     *
     * @return array Library details.
     */
    public function enqueueBehaveLibs()
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'scripts' => [
                'behave' => [
                    'version' => '0.1',
                    'url'     => '//cdn.jsdelivr.net/behave.js/%1$s/behave.js',
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue Ace libs.
     *
     * @since 170218.31677 Ace libs.
     *
     * @param string $mode  Mode.
     * @param string $theme Theme.
     *
     * @return array Library details.
     */
    public function enqueueAceLibs(string $mode, string $theme)
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $version  = '170216';
        $base_url = 'https://cdn.rawgit.com/websharks/ace-builds/%1$s/src-min-noconflict';

        $data = [
            'scripts' => [
                'ace' => [
                    'version' => $version,
                    'url'     => $base_url.'/ace.js',
                ],
                'ace-ext-linking' => [
                    'deps'    => ['ace'],
                    'version' => $version,
                    'url'     => $base_url.'/ext-linking.js',
                ],
                'ace-ext-searchbox' => [
                    'deps'    => ['ace'],
                    'version' => $version,
                    'url'     => $base_url.'/ext-searchbox.js',
                ],
                'ace-ext-spellcheck' => [
                    'deps'    => ['ace'],
                    'version' => $version,
                    'url'     => $base_url.'/ext-spellcheck.js',
                ],
                'ace-ext-language_tools' => [
                    'deps'    => ['ace'],
                    'version' => $version,
                    'url'     => $base_url.'/ext-language_tools.js',
                ],
                'ace-mode-'.$mode => [
                    'deps'    => ['ace'],
                    'version' => $version,
                    'url'     => $base_url.'/mode-'.$mode.'.js',
                ],
                'ace-theme-'.$theme => [
                    'deps'    => ['ace'],
                    'version' => $version,
                    'url'     => $base_url.'/theme-'.$theme.'.js',
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue Markdown-It libs.
     *
     * @since 170128.18158 Markdown-It libs.
     *
     * @return array Library details.
     */
    public function enqueueMarkdownItLibs()
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'scripts' => [
                'markdown-it' => [
                    'version' => '8.2.2',
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/markdown-it/%1$s/markdown-it.min.js',
                ],
                'markdown-it-attrs' => [
                    'version' => '0.8.0',
                    'deps'    => ['markdown-it'],
                    'url'     => 'https://cdn.rawgit.com/arve0/markdown-it-attrs/v%1$s/markdown-it-attrs.browser.js',
                ],
                'markdown-it-deflist' => [
                    'version' => '2.0.1',
                    'deps'    => ['markdown-it'],
                    'url'     => '//cdn.rawgit.com/markdown-it/markdown-it-deflist/%1$s/dist/markdown-it-deflist.min.js',
                ],
                'markdown-it-abbr' => [
                    'version' => '1.0.4',
                    'deps'    => ['markdown-it'],
                    'url'     => '//cdn.rawgit.com/markdown-it/markdown-it-abbr/%1$s/dist/markdown-it-abbr.min.js',
                ],
                'markdown-it-footnote' => [
                    'version' => '3.0.1',
                    'deps'    => ['markdown-it'],
                    'url'     => '//cdn.rawgit.com/markdown-it/markdown-it-footnote/%1$s/dist/markdown-it-footnote.min.js',
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue Highlight.js libs.
     *
     * @since 170128.18158 Highlight.js libs.
     *
     * @param string|null $style Highlight.js style.
     *
     * @return array Library details.
     */
    public function enqueueHighlightJsLibs($style = '')
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        if (isset($style)) {
            $style = $style ?: 'default';
        } else {
            $style = ''; // No style.
        }
        $data = [
            'scripts' => [
                'highlight-js' => [
                    'version' => '9.9.0',
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/highlight.js/%1$s/highlight.min.js',
                ],
                'highlight-js-lang-wp' => [
                    'ver'  => $this->cv,
                    'deps' => ['highlight-js'],
                    'url'  => $this->c::appWsCoreUrl('/client-s/js/hljs/langs/wp.min.js'),
                ],
            ],
        ];
        if ($style) {
            $data['styles'] = [
                'highlight-js' => $this->highlightJsStyleData($style),
            ];
        }
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Highlight.js style data.
     *
     * @since 170128.18158 Highlight.js libs.
     *
     * @param string $style Highlight.js style.
     *
     * @return array Library details.
     */
    public function highlightJsStyleData(string $style): array
    {
        return [ // Note: Caller still needs to `sprintf()` `version` into place.
            'version' => '9.9.0',
            'url'     => '//cdnjs.cloudflare.com/ajax/libs/highlight.js/%1$s/styles/'.urlencode($style).'.min.css',
        ]; // This is separate so that callers can get stylesheet data for multiple styles.
    }

    /**
     * Enqueue Moment libs.
     *
     * @since 160524 Moment libs.
     *
     * @return array Library details.
     */
    public function enqueueMomentLibs()
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'scripts' => [
                'moment' => [
                    'version'  => '2.17.1',
                    'url'      => '//cdnjs.cloudflare.com/ajax/libs/moment.js/%1$s/moment.min.js',
                    'localize' => [
                        'key'  => 'rgvfbtgzxqrdbpcdjvzpcrfrsbtgpdvpMomentData',
                        'data' => [
                            'format'     => _x('MMM Do, YYYY h:mm a', 'moment-libs', 'wp-sharks-core'), // Same as: `M jS, Y g:i a`
                            'formatDate' => _x('MMM Do, YYYY', 'moment-libs', 'wp-sharks-core'), // Same as: `M jS, Y`
                            'formatTime' => _x('h:mm a', 'moment-libs', 'wp-sharks-core'), // Same as: `g:i a`

                            'locale' => _x('en', 'moment-libs', 'wp-sharks-core'), // Override to translate.
                            'i18n'   => [ // Or, you can change the `en` language here.
                                'en'  => [], // See: <http://momentjs.com/docs/#/i18n/>
                                'utc' => _x('UTC', 'moment-libs', 'wp-sharks-core'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue jQuery animate.css libs.
     *
     * @since 170128.18158 jQuery animate.css libs.
     *
     * @param bool $with_styles Enqueue styles too?
     *
     * @return array Library details.
     */
    public function enqueueJQueryAnimateCssLibs(bool $with_styles = false)
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'scripts' => [
                'jquery-animate-css' => [
                    'version' => '1.2.1',
                    'deps'    => ['jquery'],
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/animateCSS/%1$s/jquery.animatecss.min.js',
                ],
            ],
        ];
        if ($with_styles) { // It's generally unnecessary to load all of these animation styles.
            // Instead, steal the ones you need an include individually in your own CSS please.
            $data['styles'] = [
                'animate-css' => [
                    'version' => '3.5.2',
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/animate.css/%1$s/animate.min.css',
                ],
            ];
        }
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue jQuery Pickadate libs.
     *
     * @since 160524 jQuery Pickadate libs.
     *
     * @param string $which One of `date-time`, `date`, `time`.
     *
     * @return array Library details.
     */
    public function enqueueJQueryPickadateLibs(string $which = 'date-time')
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'styles' => [
                'jquery-pickadate' => [
                    'version' => '3.5.6',
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/%1$s/compressed/themes/default.css',
                ],
            ],
            'scripts' => [
                'jquery-pickadate' => [
                    'version'  => '3.5.6',
                    'deps'     => ['jquery'],
                    'url'      => '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/%1$s/compressed/picker.js',
                    'localize' => [
                        'key'  => 'bvtnafpwxwhxtzqwqumtmwfywfmmgffdPickadateData',
                        'data' => [
                            'defaultDateOptions' => [
                                'selectYears'   => true, 'selectMonths' => true,
                                'closeOnSelect' => true, 'closeOnClear' => true,

                                'format'       => _x('mmm dd, yyyy', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                'momentFormat' => _x('MMM DD, YYYY', 'jquery-pickadate-libs', 'wp-sharks-core'),

                                'formatSubmit' => _x('yyyy-mm-dd', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                'hiddenName'   => true, // <http://amsul.ca/pickadate.js/date/#formats>

                                'today' => _x('Today', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                'clear' => _x('Clear', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                'close' => _x('Close', 'jquery-pickadate-libs', 'wp-sharks-core'),

                                'labelMonthNext'   => _x('Next Month', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                'labelMonthPrev'   => _x('Previous Month', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                'labelMonthSelect' => _x('Select Month', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                'labelYearSelect'  => _x('Select Year', 'jquery-pickadate-libs', 'wp-sharks-core'),

                                'weekdaysShort' => [
                                    _x('Sun', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Mon', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Tue', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Wed', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Thu', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Fri', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Sat', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                ],
                                'weekdaysFull' => [
                                    _x('Sunday', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Monday', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Tuesday', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Wednesday', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Thursday', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Friday', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Saturday', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                ],
                                'monthsShort' => [
                                    _x('Jan', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Feb', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Mar', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Apr', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('May', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Jun', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Jul', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Aug', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Sep', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Oct', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Nov', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('Dec', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                ],
                                'monthsFull' => [
                                    _x('January', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('February', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('March', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('April', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('May', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('June', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('July', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('August', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('September', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('October', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('November', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                    _x('December', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                ],
                            ],
                            'defaultTimeOptions' => [
                                'interval'      => 15,
                                'closeOnSelect' => true, 'closeOnClear' => true,

                                'format'       => _x('h:i A', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                'momentFormat' => _x('h:mm A', 'jquery-pickadate-libs', 'wp-sharks-core'),

                                'formatSubmit' => _x('HH:i', 'jquery-pickadate-libs', 'wp-sharks-core'),
                                'hiddenName'   => true, // <http://amsul.ca/pickadate.js/time/#formats>

                                'clear' => _x('Clear', 'jquery-pickadate-libs', 'wp-sharks-core'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        if ($which === 'date-time' || $which === 'date') {
            $data['styles']['jquery-pickadate-date'] = [
                'version' => '3.5.6',
                'deps'    => ['jquery-pickadate'],
                'url'     => '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/%1$s/compressed/themes/default.date.css',
            ];
            $data['scripts']['jquery-pickadate-date'] = [
                'version' => '3.5.6',
                'deps'    => ['jquery-pickadate'],
                'url'     => '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/%1$s/compressed/picker.date.js',
            ];
        }
        if ($which === 'date-time' || $which === 'time') {
            $data['styles']['jquery-pickadate-time'] = [
                'version' => '3.5.6',
                'deps'    => ['jquery-pickadate'],
                'url'     => '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/%1$s/compressed/themes/default.time.css',
            ];
            $data['scripts']['jquery-pickadate-time'] = [
                'version' => '3.5.6',
                'deps'    => ['jquery-pickadate'],
                'url'     => '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/%1$s/compressed/picker.time.js',
            ];
        }
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue jQuery Chosen libs.
     *
     * @since 160524 jQuery Chosen libs.
     *
     * @return array Library details.
     */
    public function enqueueJQueryChosenLibs()
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $data = [
            'styles' => [
                'jquery-chosen' => [
                    'version' => '1.6.2',
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/chosen/%1$s/chosen.min.css',
                ],
            ],
            'scripts' => [
                'jquery-chosen' => [
                    'version'  => '1.6.2',
                    'deps'     => ['jquery'],
                    'url'      => '//cdnjs.cloudflare.com/ajax/libs/chosen/%1$s/chosen.jquery.min.js',
                    'localize' => [
                        'key'  => 'jazssggqbtujeebgvnskynzyzwqttqqzJQueryChosenData',
                        'data' => [
                            'defaultOptions' => [
                                'width'                     => '100%',
                                'search_contains'           => true,
                                'no_results_text'           => _x('No results.', 'jquery-chosen-libs', 'wp-sharks-core'),
                                'placeholder_text_multiple' => _x('Search or click to select options...', 'jquery-chosen-libs', 'wp-sharks-core'),
                                'placeholder_text_single'   => _x('Search or click to select an option...', 'jquery-chosen-libs', 'wp-sharks-core'),
                            ],
                        ],
                    ],
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue jQuery grid libs.
     *
     * @since 160524 jQuery grid libs.
     *
     * @return array Library details.
     */
    public function enqueueJQueryJsGridLibs()
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $this->enqueueMomentLibs();
        $this->enqueueJQueryPickadateLibs();

        $data = [
            'styles' => [
                'jquery-jsgrid' => [
                    'version' => '1.5.3',
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/jsgrid/%1$s/jsgrid.min.css',
                ],
                'jquery-jsgrid-theme' => [
                    'version' => '1.5.3',
                    'deps'    => ['jquery-jsgrid'],
                    'url'     => '//cdnjs.cloudflare.com/ajax/libs/jsgrid/%1$s/jsgrid-theme.min.css',
                ],
            ],
            'scripts' => [
                'jquery-jsgrid' => [
                    'version'  => '1.5.3',
                    'deps'     => ['jquery'],
                    'url'      => '//cdnjs.cloudflare.com/ajax/libs/jsgrid/%1$s/jsgrid.min.js',
                    'localize' => [
                        'key'  => 'neyjfbxruwddgfeedwacfbggzbxkwfxhJQueryJsGridData',
                        'data' => [
                            'defaultOptions' => [
                                'width'  => '100%',
                                'height' => 'auto',

                                'inserting' => true,
                                'editing'   => true,
                                'sorting'   => true,
                                'paging'    => true,

                                'pageSize'              => 10,
                                'pageButtonCount'       => 10,
                                'pageFirstText'         => _x('««', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'pagePrevText'          => _x('«', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'pageNextText'          => _x('»', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'pageLastText'          => _x('»»', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'pageNavigatorNextText' => _x('…', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'pageNavigatorPrevText' => _x('…', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'pagerFormat'           => _x('Pages:', 'jquery-jsgrid-libs', 'wp-sharks-core').
                                    ' {first} {prev} {pages} {next} {last}',

                                'invalidMessage' => _x('A slight problem...', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'loadMessage'    => _x('loading...', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'noDataContent'  => _x('Nothing to display.', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'deleteConfirm'  => _x('Are you sure?', 'jquery-jsgrid-libs', 'wp-sharks-core'), 'confirmDeleting' => false,
                            ],
                            'controlDefaultOptions' => [
                                'width' => '10%',
                                'type'  => 'control',
                                'align' => 'center',

                                'editButton'        => true,
                                'deleteButton'      => true,
                                'clearFilterButton' => true,
                                'modeSwitchButton'  => true,

                                'editButtonTooltip'        => _x('Edit', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'deleteButtonTooltip'      => _x('Delete', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'searchButtonTooltip'      => _x('Search', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'insertButtonTooltip'      => _x('Insert', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'updateButtonTooltip'      => _x('Update', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'cancelEditButtonTooltip'  => _x('Cancel edit', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'clearFilterButtonTooltip' => _x('Clear filter', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'insertModeButtonTooltip'  => _x('Switch to inserting', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                                'searchModeButtonTooltip'  => _x('Switch to searching', 'jquery-jsgrid-libs', 'wp-sharks-core'),
                            ],
                        ],
                    ],
                ],
                'jquery-jsgrid-select-field' => [
                    'ver'  => $this->cv,
                    'deps' => ['underscore', 'jquery-jsgrid'],
                    'url'  => $this->c::appCoreUrl('/client-s/js/jquery-plugins/jsgrid/select-field.min.js'),
                ],
                'jquery-jsgrid-control-field' => [
                    'ver'  => $this->cv,
                    'deps' => ['underscore', 'jquery-jsgrid'],
                    'url'  => $this->c::appCoreUrl('/client-s/js/jquery-plugins/jsgrid/control-field.min.js'),
                ],
                'jquery-jsgrid-date-time-fields' => [
                    'ver'  => $this->cv,
                    'deps' => ['underscore', 'jquery-jsgrid', 'moment', 'jquery-pickadate'],
                    'url'  => $this->c::appCoreUrl('/client-s/js/jquery-plugins/jsgrid/date-time-fields.min.js'),
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }

    /**
     * Enqueue menu-page libs.
     *
     * @since 160709 Menu page libs.
     *
     * @return array Library details.
     */
    public function enqueueMenuPageLibs()
    {
        if (($data = $this->didEnqueueLibs(__METHOD__))) {
            return $data; // Did this already.
        } // We only need to enqueue once.

        $this->enqueueSharkiconLibs();

        $data = [
            'styles' => [
                $this->App::CORE_CONTAINER_SLUG.'-menu-page' => [
                    'ver'  => $this->cv,
                    'deps' => ['sharkicons', 'wp-color-picker'],
                    'url'  => $this->c::appCoreUrl('/client-s/css/admin/menu-page/core.min.css'),
                ],
            ],
            'scripts' => [
                $this->App::CORE_CONTAINER_SLUG.'-menu-page' => [
                    'ver'      => $this->cv,
                    'deps'     => ['jquery', 'underscore', 'jquery-ui-tooltip', 'wp-color-picker'],
                    'url'      => $this->c::appCoreUrl('/client-s/js/admin/menu-page/core.min.js'),
                    'localize' => [
                        'key'  => 'nuqvUt59Aqv9RhzvhjafETjNS5hAFScXMenuPageData',
                        'data' => [
                            'coreContainerSlug' => $this->App::CORE_CONTAINER_SLUG,
                            'coreContainerVar'  => $this->App::CORE_CONTAINER_VAR,
                            'coreContainerName' => $this->App::CORE_CONTAINER_NAME,

                            'currentMenuPage'    => $this->s::currentMenuPage(),
                            'currentMenuPageTab' => $this->s::currentMenuPageTab(),
                        ],
                    ],
                ],
            ],
        ];
        return $this->enqueueLibs(__METHOD__, $data);
    }
}
