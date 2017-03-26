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
     * Tooltips.
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
     * e.g., data-if="other_field_name=0,1,2,3"
     * e.g., data-if="other_field_name!=0,1,2,3"
     *
     * e.g., data-if="other_field_name!=0,<disabled>" (disabled in some way).
     * In other words, it's not `0` and it's not `<disabled>` in some way.
     */
    $menuPageArea.find('.-form-table tr[data-if]').each(function () {
      var disabledClass = '-disabled-via-if-check',
        disabledValue = '<disabled>';

      var $thisTr = $(this),
        $form = $thisTr.closest('form');

      var data = $.trim($thisTr.data('if')),
        logic = /\s+(?:OR|\|\|)\s+/i.test(data) ? 'ANY' : 'ALL',
        conditionals = data.split(/\s+(?:AND|&&|OR|\|\|)\s+/i);

      // Parse conditionals into condition objects.

      if (!conditionals.length)
        throw 'Missing conditionals.';

      var conditions = []; // Initialize.

      conditionals.forEach(function (c, index) {
        var parts = c.split(/(!==|===|!=|=)/),
          otherFieldName = parts[0] || '',
          operator = parts[1] || '==',
          values = (parts[2] || '1').split(/(?:,\|)/);

        if (!otherFieldName || !operator || !values.length)
          throw 'Missing field name, operator, or values.';

        var $otherField = $form.find('[name$="' + esqJqAttr('[' + otherFieldName + ']') + '"]'),
          $otherTr = $otherField.closest('tr'); // Parent table row for the other field.

        conditions[index] = {
          $otherField: $otherField,
          $otherTr: $otherTr,
          operator: operator,
          values: values,
        };
      });

      // Runs all tests & returns their results.

      var runTests = function () {
        var tests = []; // Initialize results.

        conditions.forEach(function (c, index) {
          var otherValue; // Initialize.

          if (c.$otherField.length < 1) {
            otherValue = '<undefined>';

          } else { // Field exists.
            otherValue = $.trim(c.$otherField.val());

            if (c.$otherField.prop('disabled')) {
              otherValue = disabledValue;
            } else if (c.$otherTr.hasClass(disabledClass)) {
              otherValue = disabledValue;
            }
          }
          switch (c.operator) {
            case '==': // Equal to any.
            case '=': // Alias.

              if ($.inArray(otherValue, c.values) !== -1) {
                tests[index] = true;
              } else tests[index] = false;

              break; // Break here.

            case '!==': // Not equal to any.
            case '!=': // Alias.

              if ($.inArray(otherValue, c.values) === -1) {
                tests[index] = true;
              } else tests[index] = false;

              break; // Break here.
          }
        });
        return tests;
      };

      // Checks all tests and adjusts classes.

      var checkTests = function () {
        var tests = runTests(),
          i, enabled;

        if (logic === 'ANY') {
          for (i = 0, enabled = false; i < tests.length; i++) {
            if (tests[i]) enabled = true;
          }
        } else { // Defaults to `ALL` logic.
          for (i = 0, enabled = true; i < tests.length; i++) {
            if (!tests[i]) enabled = false;
          }
        }
        if (enabled) { // Enabled?
          $thisTr.removeClass(disabledClass);
        } else $thisTr.addClass(disabledClass);
      };

      // Setup change event handlers.

      conditions.forEach(function (c, index) {
        c.$otherField.on('input change', _.debounce(checkTests, 500));
      });

      // Check tests right away.

      checkTests();
    });
  });
})(jQuery);
