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
     * e.g., data-if="other_field_name" (same as `=1`).
     * e.g., data-if="other_field_name=0|1|2|3"
     * e.g., data-if="other_field_name!=0|1|2|3"
     *
     * e.g., data-if="other_field_name!=0|<disabled>" (disabled in some way).
     * In other words, it's not `0` and it's not `<disabled>` in some way.
     */
    $menuPageArea.find('.-form-table tr[data-if]').each(function () {
      var disabledClass = '-disabled-via-if-check',
        disabledValue = '<disabled>';

      var $thisTr = $(this),
        $form = $thisTr.closest('form');

      var ifParts = $thisTr.data('if').split(/(!==|===|!=|=)/),
        ifOtherFieldName = ifParts[0] || '',
        ifOperator = ifParts[1] || '=',
        ifValues = (ifParts[2] || '1').split(/\|/);

      if (!ifOtherFieldName || !ifOperator || !ifValues.length)
        return; // Nothing to do in this case.

      var $otherField = $form.find('[name$="' + esqJqAttr('[' + ifOtherFieldName + ']') + '"]'),
        $otherTr = $otherField.closest('tr'); // Parent table row for the other field.

      $otherField.on('change', function (e) {
        var otherValue = $.trim($otherField.val());

        if ($otherField.prop('disabled')) {
          otherValue = disabledValue;
        } else if ($otherTr.hasClass(disabledClass)) {
          otherValue = disabledValue;
        }
        switch (ifOperator) {

        case '==': // Equal to any.
        case '=':

          if ($.inArray(otherValue, ifValues) !== -1) {
            $thisTr.removeClass(disabledClass);
          } else $thisTr.addClass(disabledClass);

          break; // Break here.

        case '!==': // Not equal to any.
        case '!=':

          if ($.inArray(otherValue, ifValues) === -1) {
            $thisTr.removeClass(disabledClass);
          } else $thisTr.addClass(disabledClass);

          break; // Break here.
        }
      }).trigger('change');
    });
  });
})(jQuery);
