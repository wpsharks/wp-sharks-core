<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Scripts/styles.
 *
 * @since 16xxxx Scripts/styles.
 */
class StylesScripts extends Classes\SCore\Base\Core
{
    /**
     * Enqueue Moment libs.
     *
     * @since 16xxxx Moment libs.
     */
    public function enqueueMomentLibs()
    {
        wp_enqueue_script('moment', '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment-with-locales.min.js', [], null, true);

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
    }

    /**
     * Enqueue jQuery Pickadate libs.
     *
     * @since 16xxxx jQuery Pickadate libs.
     *
     * @param string $which One of `date-time`, `date`, `time`.
     */
    public function enqueueJQueryPickadateLibs(string $which = 'date-time')
    {
        wp_enqueue_style('jquery-pickadate', '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/3.5.6/compressed/themes/default.css', [], null, 'all');
        wp_enqueue_script('jquery-pickadate', '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/3.5.6/compressed/picker.js', ['jquery'], null, true);

        if ($which === 'date-time' || $which === 'date') {
            wp_enqueue_style('jquery-pickadate-date', '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/3.5.6/compressed/themes/default.date.css', ['jquery-pickadate'], null, 'all');
            wp_enqueue_script('jquery-pickadate-date', '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/3.5.6/compressed/picker.date.js', ['jquery-pickadate'], null, true);
        }
        if ($which === 'date-time' || $which === 'time') {
            wp_enqueue_style('jquery-pickadate-time', '//cdnjs.cloudflare.com/ajax/libs/pickadate.js/3.5.6/compressed/themes/default.time.css', ['jquery-pickadate'], null, 'all');
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
    }

    /**
     * Enqueue jQuery Chosen libs.
     *
     * @since 16xxxx jQuery Chosen libs.
     */
    public function enqueueJQueryChosenLibs()
    {
        wp_enqueue_style('jquery-chosen', '//cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.min.css', [], null, 'all');
        wp_enqueue_script('jquery-chosen', '//cdnjs.cloudflare.com/ajax/libs/chosen/1.5.1/chosen.jquery.min.js', ['jquery'], null, true);

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
    }

    /**
     * Enqueue jQuery grid libs.
     *
     * @since 16xxxx jQuery grid libs.
     */
    public function enqueueJQueryJsGridLibs()
    {
        $this->enqueueMomentLibs(); // The `date-time-fields` depend on this lib.
        $this->enqueueJQueryPickadateLibs(); // The `date-time-fields` depend on this.

        wp_enqueue_style('jquery-jsgrid', '//cdnjs.cloudflare.com/ajax/libs/jsgrid/1.4.1/jsgrid.min.css', [], null, 'all');
        wp_enqueue_style('jquery-jsgrid-theme', '//cdnjs.cloudflare.com/ajax/libs/jsgrid/1.4.1/jsgrid-theme.min.css', ['jquery-jsgrid'], null, 'all');

        wp_enqueue_script('jquery-jsgrid', '//cdnjs.cloudflare.com/ajax/libs/jsgrid/1.4.1/jsgrid.min.js', ['jquery'], null, true);
        wp_enqueue_script('jquery-jsgrid-date-time-fields', $this->c::appCoreUrl('/client-s/js/jquery-plugins/jsgrid/date-time-fields.min.js'), ['jquery-jsgrid', 'jquery-pickadate', 'underscore'], null, true);

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
    }
}
