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
     *
     * e.g., data-if="option_name" (same as `=1`).
     * e.g., data-if="option_name=0|1|2|3"
     * e.g., data-if="option_name!=0|1|2|3"
     *
     * e.g., data-if="option_name!=0|<disabled>" (disabled in some way).
     * In other words, it's not `0` and it's not `<disabled>` in some way.
     */
    $menuPageWrapper.find('.-form-table tr[data-if]').each(function () {
      var $this = $(this),
        $form = $this.closest('form');

      var disabledClass = '-disabled-via-if-check',
        disabledValue = '<disabled>';

      var parts = $this.data('if').split(/(!==|===|!=|=)/),
        option = parts[0] || '',
        operator = parts[1] || '=',
        values = (parts[2] || '1').split(/\|/);

      if (!option || !operator || !values.length)
        return; // Nothing to do in this case.

      var onOptionChange = function (e) {
        var $this = $(this);
        var value = $.trim($this.val());

        if ($this.prop('disabled')) {
          value = disabledValue;
        } else if ($this.hasClass(disabledClass)) {
          value = disabledValue;
        }
        switch (operator) {

        case '==': // Equal to any.
        case '=':

          if ($.inArray(value, values) !== -1) {
            $this.removeClass(disabledClass);
          } else $this.addClass(disabledClass);

          break; // Break here.

        case '!==': // Not equal to any.
        case '!=':

          if ($.inArray(value, values) === -1) {
            $this.removeClass(disabledClass);
          } else $this.addClass(disabledClass);

          break; // Break here.
        }
      }; // Attach event & trigger an initial change on setup.

      $form.find('[name$="' + esqJqAttr('[' + option + ']') + '"]')
        .on('change', onOptionChange).trigger('change');
    });
  });
})(jQuery);
