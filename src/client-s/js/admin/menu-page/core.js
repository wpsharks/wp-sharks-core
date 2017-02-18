(function ($) {
  $(document).ready(function () {
    /*
     * Prefix/data.
     */
    var prefix = 'nuqvUt59Aqv9RhzvhjafETjNS5hAFScX',
      x = window[prefix + 'MenuPageData'];

    /*
     * Utility.
     */
    var esqJqAttr = function (str) {
      return str.replace(/(:|\.|\[|\]|,|=|@)/g, '\\$1');
    };

    /*
     * Element cache.
     */
    var $menuPage = $('.' + x.coreContainerSlug + '-menu-page');
    var $menuPageArea = $('.' + x.coreContainerSlug + '-menu-page-area');
    var $menuPageWrapper = $menuPage.find('.' + x.coreContainerSlug + '-menu-page-wrapper');

    /*
     * Color pickers.
     */
    $menuPageArea.find('input[type="color"]').wpColorPicker();

    /*
     * Block-level tooltips.
     */
    $('body').tooltip({
      show: false, // No animation.
      hide: false, // No animation.

      position: {
        my: 'center bottom', // Floating tooltip.
        at: 'center top-10', // e.g., a `.-tip` icon.

        using: function (position, feedback) {
          $(this).css(position) // Position tip overlay.
            .addClass(feedback.horizontal + ' ' + feedback.vertical);
        },
        collision: 'flipfit' // Flip & fit (best available).
      },
      content: function () {
        return $(this).prop('title');
      },
      items: '.' + x.coreContainerSlug + '-menu-page-area [data-toggle~="-jquery-ui-tooltip"]',
      tooltipClass: x.coreContainerSlug + '-jquery-ui-tooltip'
    });

    /*
     * Menu page `if` dependencies.
     * e.g., data-if="option_name" as `=1`.
     * e.g., data-if="option_name=0|1|2|3"
     * e.g., data-if="option_name!=0|1|2|3"
     */
    $menuPageWrapper.find('.-form-table tr[data-if]').each(function () {
      var $this = $(this),
        $form = $this.closest('form');

      var parts = $this.data('if').split(/(!=|=)/),
        option = parts[0] || '',
        operator = parts[1] || '=',
        values = (parts[2] || '1').split(/\|/);

      if (!option || !operator || !values.length)
        return; // Nothing to do in this case.

      var onOptionChange = function (e) {
        var value = $.trim($(this).val());

        switch (operator) {
        case '=': // Enable if in array.
          if ($.inArray(value, values) !== -1) {
            $this.removeClass('-disabled-via-if-check');
          } else $this.addClass('-disabled-via-if-check');
          break;

        case '!=': // Enable if not in array.
          if ($.inArray(value, values) === -1) {
            $this.removeClass('-disabled-via-if-check');
          } else $this.addClass('-disabled-via-if-check');
          break;
        }
      }; // Trigger an initial change on setup.
      $form.find('[name$="' + esqJqAttr('[' + option + ']') + '"]')
        .on('change', onOptionChange).trigger('change');
    });
  });
})(jQuery);
