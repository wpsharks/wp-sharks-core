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
     * @since 17xxxx
     *
     * @type string
     */
    protected $cv;

    /**
     * Enqueued.
     *
     * @since 160524
     *
     * @type array Enqueued.
     */
    protected $did_enqueue;

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

        $core              = $this->c::appCore();
        $this->cv          = $core::VERSION;
        $this->did_enqueue = []; // Initialize.
    }

    /**
     * Did enqueue libs?
     *
     * @since 160524 Scripts/styles.
     *
     * @param string $identifier Identifier.
     * @param bool   $flag       If setting the flag.
     *
     * @return bool True if identifier enqueued already.
     */
    protected function didEnqueue(string $identifier, bool $flag = null): bool
    {
        if (isset($flag)) {
            $this->did_enqueue[$identifier] = $flag;
        }
        return isset($this->did_enqueue[$identifier]);
    }

    /**
     * Enqueue jQuery.
     *
     * @since 17xxxx jQuery libs.
     */
    public function enqueueLatestJQuery()
    {
        if ($this->didEnqueue(__FUNCTION__)) {
            return; // Did this already.
        } // We only need to enqueue these libs once.
        $this->didEnqueue(__FUNCTION__, true); // Flag as done.

        wp_deregister_script('jquery'); // Ditch this and use the latest version of jQuery.
        wp_register_script('jquery', '//cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js', [], null, true);

        add_filter('script_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'jquery') {
                $tag = str_replace(' src=', ' integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous" src=', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Enqueue Font Awesome libs.
     *
     * @since 17xxxx Font Awesome libs.
     */
    public function enqueueFontAwesomeLibs()
    {
        if ($this->didEnqueue(__FUNCTION__)) {
            return; // Did this already.
        } // We only need to enqueue these libs once.
        $this->didEnqueue(__FUNCTION__, true); // Flag as done.

        wp_enqueue_style('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', [], null);

        add_filter('style_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'font-awesome') {
                $tag = str_replace(' rel=', ' integrity="sha256-eZrrJcwDc/3uDhsdt61sL2oOBY362qM3lon1gyExkL0=" crossorigin="anonymous" rel=', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Enqueue Sharkicon libs.
     *
     * @since 160709 Sharkicon libs.
     */
    public function enqueueSharkiconLibs()
    {
        if ($this->didEnqueue(__FUNCTION__)) {
            return; // Did this already.
        } // We only need to enqueue these libs once.
        $this->didEnqueue(__FUNCTION__, true); // Flag as done.

        wp_enqueue_style('sharkicons', $this->c::appCoreUrl('/vendor/websharks/sharkicons/src/long-classes.min.css'), [], $this->cv);
    }

    /**
     * Enqueue Semantic UI libs.
     *
     * @since 17xxxx Semantic UI libs.
     */
    public function enqueueSemanticUiLibs()
    {
        if ($this->didEnqueue(__FUNCTION__)) {
            return; // Did this already.
        } // We only need to enqueue these libs once.
        $this->didEnqueue(__FUNCTION__, true); // Flag as done.

        wp_enqueue_style('semantic-ui', '//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.7/semantic.min.css', [], null);
        wp_enqueue_script('semantic-ui', '//cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.7/semantic.min.js', ['jquery'], null, true);

        add_filter('style_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'semantic-ui') {
                $tag = str_replace(' rel=', ' integrity="sha256-wT6CFc7EKRuf7uyVfi+MQNHUzojuHN2pSw0YWFt2K5E=" crossorigin="anonymous" rel=', $tag);
            }
            return $tag;
        }, 10, 2);
        add_filter('script_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'semantic-ui') {
                $tag = str_replace(' src=', ' integrity="sha256-flVaeawsBV96vCHiLmXn03IRJym7+ZfcLVvUWONCas8=" crossorigin="anonymous" src=', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Enqueue Highlight.js libs.
     *
     * @since 17xxxx Highlight.js libs.
     *
     * @param string Highlight.js syntax style.
     */
    public function enqueueHighlightJsLibs(string $style = '')
    {
        $style = $style ?: 'default';

        if ($this->didEnqueue(__FUNCTION__)) {
            return; // Did this already.
        } // We only need to enqueue these libs once.
        $this->didEnqueue(__FUNCTION__, true); // Flag as done.

        wp_enqueue_style('highlight-js', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/styles/'.urlencode($style).'.min.css', [], null);
        wp_enqueue_script('highlight-js', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.9.0/highlight.min.js', [], null, true);
        wp_enqueue_script('highlight-js-lang-wp', $this->c::appCoreUrl('/client-s/js/hljs/langs/wp.min.js'), ['highlight-js'], $this->cv, true);

        $sha256['github']         = 'sha256-3YM6A3pH4QFCl9WbSU8oXF5N6W/2ylvW0o2g+Z6TmLQ=';
        $sha256['codepen-embed']  = 'sha256-o5BnFXfXTynYnUOhQkOiLwZ8LjYORl/68na9YLOvm/U=';
        $sha256['atom-one-dark']  = 'sha256-akwTLZec/XAFvgYgVH1T5/369lhA2Efr22xzCNl1nHs=';
        $sha256['atom-one-light'] = 'sha256-aw9uGjVU5OJyMYN70Vu2kZ1DDVc1slcJCS2XvuPCPKo=';
        $sha256['hybrid']         = 'sha256-7XQMS8TcWkntQbO7LIrjEG4uPGwq1hBOF0DLRTk2A10=';
        $sha256['ir-black']       = 'sha256-DHDpPa1qGl7RmS/xSreA5cg+zRUBGoqk58TlsDHzoSg=';
        $sha256['default']        = 'sha256-Zd1icfZ72UBmsId/mUcagrmN7IN5Qkrvh75ICHIQVTk=';

        add_filter('style_loader_tag', function (string $tag, string $handle) use ($sha256, $style): string {
            if ($handle === 'highlight-js' && !empty($sha256[$style])) {
                $tag = str_replace(' rel=', ' integrity="'.esc_attr($sha256[$style]).'" crossorigin="anonymous" rel=', $tag);
            }
            return $tag;
        }, 10, 2);
        add_filter('script_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'highlight-js') {
                $tag = str_replace(' src=', ' integrity="sha256-KbfTjB0WZ8vvXngdpJGY3Yp3xKk+tttbqClO11anCIU=" crossorigin="anonymous" src=', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Enqueue Simple MDE libs.
     *
     * @since 17xxxx Simple MDE libs.
     *
     * @param string Highlight.js syntax style.
     */
    public function enqueueSimpleMdeLibs(string $style = '')
    {
        if ($this->didEnqueue(__FUNCTION__)) {
            return; // Did this already.
        } // We only need to enqueue these libs once.
        $this->didEnqueue(__FUNCTION__, true); // Flag as done.

        $this->enqueueFontAwesomeLibs();
        $this->enqueueHighlightJsLibs($style);

        wp_enqueue_style('simplemde', '//cdnjs.cloudflare.com/ajax/libs/simplemde/1.11.2/simplemde.min.css', ['font-awesome', 'highlight-js'], null);
        wp_enqueue_script('simplemde', '//cdnjs.cloudflare.com/ajax/libs/simplemde/1.11.2/simplemde.min.js', ['highlight-js', 'highlight-js-lang-wp'], null, true);

        add_filter('style_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'simplemde') {
                $tag = str_replace(' rel=', ' integrity="sha256-Is0XNfNX8KF/70J2nv8Qe6BWyiXrtFxKfJBHoDgNAEM=" crossorigin="anonymous" rel=', $tag);
            }
            return $tag;
        }, 10, 2);
        add_filter('script_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'simplemde') {
                $tag = str_replace(' src=', ' integrity="sha256-6sZs7OGP0Uzcl7UDsLaNsy1K0KTZx1+6yEVrRJMn2IM=" crossorigin="anonymous" src=', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Enqueue Moment libs.
     *
     * @since 160524 Moment libs.
     */
    public function enqueueMomentLibs()
    {
        if ($this->didEnqueue(__FUNCTION__)) {
            return; // Did this already.
        } // We only need to enqueue these libs once.
        $this->didEnqueue(__FUNCTION__, true); // Flag as done.

        wp_enqueue_script('moment', '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js', [], null, true);

        wp_localize_script(
            'moment', // See: <http://momentjs.com/docs/>
            'rgvfbtgzxqrdbpcdjvzpcrfrsbtgpdvpMomentData',
            [
                'format'     => _x('MMM Do, YYYY h:mm a', 'moment-libs', 'wp-sharks-core'), // Same as: `M jS, Y g:i a`
                'formatDate' => _x('MMM Do, YYYY', 'moment-libs', 'wp-sharks-core'), // Same as: `M jS, Y`
                'formatTime' => _x('h:mm a', 'moment-libs', 'wp-sharks-core'), // Same as: `g:i a`

                'locale' => _x('en', 'moment-libs', 'wp-sharks-core'), // Override to translate.
                'i18n'   => [ // Or, you can change the `en` language here.
                    'en'  => [], // See: <http://momentjs.com/docs/#/i18n/>
                    'utc' => _x('UTC', 'moment-libs', 'wp-sharks-core'),
                ],
            ]
        );
        add_filter('script_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'moment') {
                $tag = str_replace(' src=', ' integrity="sha256-Gn7MUQono8LUxTfRA0WZzJgTua52Udm1Ifrk5421zkA=" crossorigin="anonymous" src=', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Enqueue jQuery Pickadate libs.
     *
     * @since 160524 jQuery Pickadate libs.
     *
     * @param string $which One of `date-time`, `date`, `time`.
     */
    public function enqueueJQueryPickadateLibs(string $which = 'date-time')
    {
        if ($this->didEnqueue(__FUNCTION__)) {
            return; // Did this already.
        } // We only need to enqueue these libs once.
        $this->didEnqueue(__FUNCTION__, true); // Flag as done.

        wp_enqueue_style('jquery-pickadate', '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/3.5.6/compressed/themes/default.css', [], null);
        wp_enqueue_script('jquery-pickadate', '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/3.5.6/compressed/picker.js', ['jquery'], null, true);

        if ($which === 'date-time' || $which === 'date') {
            wp_enqueue_style('jquery-pickadate-date', '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/3.5.6/compressed/themes/default.date.css', ['jquery-pickadate'], null);
            wp_enqueue_script('jquery-pickadate-date', '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/3.5.6/compressed/picker.date.js', ['jquery-pickadate'], null, true);
        }
        if ($which === 'date-time' || $which === 'time') {
            wp_enqueue_style('jquery-pickadate-time', '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/3.5.6/compressed/themes/default.time.css', ['jquery-pickadate'], null);
            wp_enqueue_script('jquery-pickadate-time', '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/3.5.6/compressed/picker.time.js', ['jquery-pickadate'], null, true);
        }
        wp_localize_script(
            'jquery-pickadate', // See: <https://github.com/amsul/pickadate.js>
            'bvtnafpwxwhxtzqwqumtmwfywfmmgffdPickadateData',
            [
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
            ]
        );
        add_filter('style_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'jquery-pickadate') {
                $tag = str_replace(' rel=', ' integrity="sha256-HJnF0By+MMhHfGTHjMMD7LlFL0KAQEMyWB86VbeFn4k=" crossorigin="anonymous" rel=', $tag);
            } elseif ($handle === 'jquery-pickadate-date') {
                $tag = str_replace(' rel=', ' integrity="sha256-Ex8MCGbDP5+fEwTgLt8IbGaIDJu2uj88ZDJgZJrxA4Y=" crossorigin="anonymous" rel=', $tag);
            } elseif ($handle === 'jquery-pickadate-time') {
                $tag = str_replace(' rel=', ' integrity="sha256-0GwWH1zJVNiu4u+bL27FHEpI0wjV0hZ4nSSRM2HmpK8=" crossorigin="anonymous" rel=', $tag);
            }
            return $tag;
        }, 10, 2);
        add_filter('script_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'jquery-pickadate') {
                $tag = str_replace(' src=', ' integrity="sha256-A1y8n02GW5dvJFkEOX7UCbzJoko8kqgWUquWf9TWFS8=" crossorigin="anonymous" src=', $tag);
            } elseif ($handle === 'jquery-pickadate-date') {
                $tag = str_replace(' src=', ' integrity="sha256-rTh8vmcE+ZrUK3k9M6QCNZIBmAd1vumeuJkagq0EU3g=" crossorigin="anonymous" src=', $tag);
            } elseif ($handle === 'jquery-pickadate-time') {
                $tag = str_replace(' src=', ' integrity="sha256-vFMKre5X5oQN63N+oJU9cJzn22opMuJ+G9FWChlH5n8=" crossorigin="anonymous" src=', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Enqueue jQuery Chosen libs.
     *
     * @since 160524 jQuery Chosen libs.
     */
    public function enqueueJQueryChosenLibs()
    {
        if ($this->didEnqueue(__FUNCTION__)) {
            return; // Did this already.
        } // We only need to enqueue these libs once.
        $this->didEnqueue(__FUNCTION__, true); // Flag as done.

        wp_enqueue_style('jquery-chosen', '//cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.min.css', [], null);
        wp_enqueue_script('jquery-chosen', '//cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.min.js', ['jquery'], null, true);

        wp_localize_script(
            'jquery-chosen', // See: <https://harvesthq.github.io/chosen/>
            'jazssggqbtujeebgvnskynzyzwqttqqzJQueryChosenData',
            [
                'defaultOptions' => [
                    'width'                     => '100%',
                    'search_contains'           => true,
                    'no_results_text'           => _x('No results.', 'jquery-chosen-libs', 'wp-sharks-core'),
                    'placeholder_text_multiple' => _x('Search or click to select options...', 'jquery-chosen-libs', 'wp-sharks-core'),
                    'placeholder_text_single'   => _x('Search or click to select an option...', 'jquery-chosen-libs', 'wp-sharks-core'),
                ],
            ]
        );
        add_filter('style_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'jquery-chosen') {
                $tag = str_replace(' rel=', ' integrity="sha256-QD+eN1fgrT9dm2vaE+NAAznRdtWd1JqM0xP2wkgjTSQ=" crossorigin="anonymous" rel=', $tag);
            }
            return $tag;
        }, 10, 2);
        add_filter('script_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'jquery-chosen') {
                $tag = str_replace(' src=', ' integrity="sha256-sLYUdmo3eloR4ytzZ+7OJsswEB3fuvUGehbzGBOoy+8=" crossorigin="anonymous" src=', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Enqueue jQuery grid libs.
     *
     * @since 160524 jQuery grid libs.
     */
    public function enqueueJQueryJsGridLibs()
    {
        if ($this->didEnqueue(__FUNCTION__)) {
            return; // Did this already.
        } // We only need to enqueue these libs once.
        $this->didEnqueue(__FUNCTION__, true); // Flag as done.

        $this->enqueueMomentLibs(); // The `date-time-fields` depend on this lib.
        $this->enqueueJQueryPickadateLibs(); // The `date-time-fields` depend on this.

        wp_enqueue_style('jquery-jsgrid', '//cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css', [], null);
        wp_enqueue_style('jquery-jsgrid-theme', '//cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css', ['jquery-jsgrid'], null);

        wp_enqueue_script('jquery-jsgrid', '//cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js', ['jquery'], null, true);
        wp_enqueue_script('jquery-jsgrid-select-field', $this->c::appCoreUrl('/client-s/js/jquery-plugins/jsgrid/select-field.min.js'), ['jquery-jsgrid', 'underscore'], $this->cv, true);
        wp_enqueue_script('jquery-jsgrid-control-field', $this->c::appCoreUrl('/client-s/js/jquery-plugins/jsgrid/control-field.min.js'), ['jquery-jsgrid', 'underscore'], $this->cv, true);
        wp_enqueue_script('jquery-jsgrid-date-time-fields', $this->c::appCoreUrl('/client-s/js/jquery-plugins/jsgrid/date-time-fields.min.js'), ['jquery-jsgrid', 'jquery-pickadate', 'underscore'], $this->cv, true);

        wp_localize_script(
            'jquery-jsgrid', // See: <http://js-grid.com/docs/>
            'neyjfbxruwddgfeedwacfbggzbxkwfxhJQueryJsGridData',
            [
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
            ]
        );
        add_filter('style_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'jquery-jsgrid') {
                $tag = str_replace(' rel=', ' integrity="sha256-a/jNbtm7jpeKiXCShJ8YC+eNL9Abh7CBiYXHgaofUVs=" crossorigin="anonymous" rel=', $tag);
            } elseif ($handle === 'jquery-jsgrid-theme') {
                $tag = str_replace(' rel=', ' integrity="sha256-0rD7ZUV4NLK6VtGhEim14ZUZGC45Kcikjdcr4N03ddA=" crossorigin="anonymous" rel=', $tag);
            }
            return $tag;
        }, 10, 2);
        add_filter('script_loader_tag', function (string $tag, string $handle): string {
            if ($handle === 'jquery-jsgrid') {
                $tag = str_replace(' src=', ' integrity="sha256-lzjMTpg04xOdI+MJdjBst98bVI6qHToLyVodu3EywFU=" crossorigin="anonymous" src=', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Enqueue menu-page libs.
     *
     * @since 160709 Menu page libs.
     */
    public function enqueueMenuPageLibs()
    {
        if ($this->didEnqueue(__FUNCTION__)) {
            return; // Did this already.
        } // We only need to enqueue these libs once.
        $this->didEnqueue(__FUNCTION__, true); // Flag as done.

        $this->enqueueSharkiconLibs(); // Depends on this lib.

        wp_enqueue_style($this->App::CORE_CONTAINER_SLUG.'-menu-page', $this->c::appCoreUrl('/client-s/css/admin/menu-page/core.min.css'), ['sharkicons', 'wp-color-picker'], $this->cv);
        wp_enqueue_script($this->App::CORE_CONTAINER_SLUG.'-menu-page', $this->c::appCoreUrl('/client-s/js/admin/menu-page/core.min.js'), ['jquery', 'jquery-ui-tooltip', 'underscore', 'wp-color-picker'], $this->cv, true);

        wp_localize_script(
            $this->App::CORE_CONTAINER_SLUG.'-menu-page',
            'nuqvUt59Aqv9RhzvhjafETjNS5hAFScXMenuPageData',
            [
                'coreContainerSlug' => $this->App::CORE_CONTAINER_SLUG,
                'coreContainerVar'  => $this->App::CORE_CONTAINER_VAR,
                'coreContainerName' => $this->App::CORE_CONTAINER_NAME,

                'currentMenuPage'    => $this->s::currentMenuPage(),
                'currentMenuPageTab' => $this->s::currentMenuPageTab(),
            ]
        );
    }
}
