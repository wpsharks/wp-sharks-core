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
    $menuPageArea.find('input[type="color"], input[type="text"][data-toggle~="-color-picker"]').wpColorPicker();

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
     * e.g., `data-if="other_field_name"` (same as `=1`).
     * e.g., `data-if="other_field_name=0,1,2,3"`
     * e.g., `data-if="other_field_name!=0,1,2,3"`
     * e.g., `data-if="other_field_name = 0, 1, 2, 3"`
     * e.g., `data-if="other_field_name != 0, 1, 2, 3"`
     *
     * e.g., `data-if="other_field_name != 0, <empty>"` (empty in some way).
     * In other words, it's not `0` and it's not `<empty>` in some way.
     *  ~ A value of `0` is not considered to be `<empty>`.
     *
     * e.g., `data-if="other_field_name >= 2"` (tests a numeric value).
     * e.g., `data-if="other_field_name <= 0"` (tests a numeric value).
     * Non-numeric (or NaN) values always fail comparison, intentionally.
     *
     * e.g., `data-if="other_field_name:length >= 2"` (tests array or string length).
     * e.g., `data-if="other_field_name :length <= 0"` (tests array or string length).
     *
     * e.g., `data-if="other_field_name != 0, 1, 2 && another_field_name >= 2"` (multiple conditions).
     * e.g., `data-if="other_field_name != 0,<empty> && another_field_name:length >= 2"` (multiple conditions).
     *
     * You can test an unlimited number of conditions. However, you cannot mix `&&` and `||` together.
     * i.e., If your conditional statement contains `||`, the whole statement is treated as `ANY` logic.
     * Otherwise, it is treated as `AND` logic. That is the default logic applied internally.
     *
     * Comma-delimited values always use `ANY` logic, so you *can* mix `&&` that way.
     * e.g., `data-if="a = 1,2,3 && b = 4,5,6"` tests if `a` is `1`, `2`, or `3`, and `b` is `4`, `5`, or `6`.
     * e.g., `data-if="a != 1,2,3 && b = 4,5,6"` tests if `a` is not `1`, `2`, or `3`, and `b` is `4`, `5`, or `6`.
     *
     * For multiple fields (e.g., multiple files, select multiple, radios, checkboxes), length is the array length.
     * In that scenario the value will be converted to a string of all array values concatenated with `;`.
     * For radios & checkboxes, the length and value will only include those that are checked!
     *
     * For all other fields, length is the total string length of the value.
     * And of course, value is the string value. That goes for a single radio/checkbox too.
     * Again, the length and value for a radio/checkbox will only be filled if checked!
     *
     * A field that is nonexistent (i.e., not in the DOM) has the special value `<nonexistent>`.
     * Empty (either in length or in value; e.g., `[]`, `=== ''`, `undefined`), has the special value `<empty>`.
     *  ~ A value of `0` is not considered to be `<empty>`.
     */
    $menuPageArea.find('.-form-table tr[data-if]').each(function () {
      var disabledClass = '-disabled-via-if-check',
        nonexistentValue = '<nonexistent>',
        emptyValue = '<empty>';

      var $thisTr = $(this),
        $form = $thisTr.closest('form');

      var data = $.trim($thisTr.data('if'));
      if (!data) return; // Empty; e.g., `data-if=""`.

      var logic = /\s\|\|\s/i.test(data) ? 'ANY' : 'ALL',
        conditionals = data.split(/\s(?:&&|\|\|)\s/i);

      // Parse conditionals into condition objects.

      if (!conditionals.length)
        throw 'Missing conditionals.';

      var conditions = []; // Initialize.

      conditionals.forEach(function (c, index) {
        var parts = c.split(/(\:length\s*<=|\:length\s*>=|!==|===|<=|>=|!=|==|=)/),
          otherFieldName = $.trim(parts[0] || ''),
          operator = (parts[1] || '=').replace(/\s+/g, ''),
          values = _.map((parts[2] || '1').split(/,/), $.trim);

        if (!otherFieldName || !operator || !values.length)
          throw 'Missing field name, operator, or values.';

        var $otherField = $form.find('[name$="' + esqJqAttr('[' + otherFieldName + ']') + '"]'),
          // NOTE: The field may consist of more than one in some cases; e.g., radios.
          // NOTE: It is also entirely possible that a field is simply nonexistent.
          $otherTr = $otherField.closest('tr'); // Or empty `$` if not exists.

        var otherFieldTag = ($otherField.prop('tagName') || '').toLowerCase(),
          otherFieldType = ($otherField.attr('type') || '').toLowerCase();

        conditions[index] = {
          $otherTr: $otherTr,
          $otherField: $otherField,

          otherFieldTag: otherFieldTag,
          otherFieldType: otherFieldType,

          operator: operator,
          values: values,
        };
      });

      // Runs all tests & returns their results.

      var runTests = function () {
        var tests = []; // Initialize.

        conditions.forEach(function (c, index) {
          var otherValue, otherLength;

          if (!c.$otherField.length) {
            otherValue = nonexistentValue;

          } else if (c.otherFieldTag === 'input' && $.inArray(c.otherFieldType, ['checkbox', 'radio']) !== -1) {

            if (c.$otherField.length > 1) {
              otherValue = []; // More than one value.
              c.$otherField.filter(':checked').each(function (i, otherField) {
                otherValue.push($.trim(String($(otherField).val())));
              });
            } else otherValue = c.$otherField.filter(':checked').val();

          } else if (c.$otherField.length > 1) {

            otherValue = []; // More than one value.
            c.$otherField.each(function (i, otherField) {
              otherValue.push($.trim(String($(otherField).val())));
            });

          } else otherValue = c.$otherField.val();

          if (otherValue === nonexistentValue) {
            otherLength = 0, otherValue = nonexistentValue;

          } else if (otherValue === undefined) {
            otherLength = 0, otherValue = emptyValue;

          } else if (otherValue instanceof Array) {
            otherLength = otherValue.length;
            otherValue = $.trim(otherValue.join(';'));

            if (!otherLength || !otherValue.length) {
              otherValue = emptyValue;
            }
          } else { // Default behavior.
            otherValue = $.trim(String(otherValue));
            otherLength = otherValue.length;

            if (!otherLength || !otherValue.length) {
              otherValue = emptyValue;
            }
          }
          switch (c.operator) {
            case ':length<=': // Length <=.

              if (otherLength <= Number(c.values[0])) {
                tests[index] = true;
              } else tests[index] = false;

              break; // Break here.

            case ':length>=': // Length >=.

              if (otherLength >= Number(c.values[0])) {
                tests[index] = true;
              } else tests[index] = false;

              break; // Break here.

            case '<=': // Less than or equal to.

              if (!$.isNumeric(otherValue)) {
                tests[index] = false;
              } else if (!$.isNumeric(Number(otherValue))) {
                tests[index] = false;
              } else if (Number(otherValue) <= Number(c.values[0])) {
                tests[index] = true;
              } else tests[index] = false;

              break; // Break here.

            case '>=': // Greater than or equal to.

              if (!$.isNumeric(otherValue)) {
                tests[index] = false;
              } else if (!$.isNumeric(Number(otherValue))) {
                tests[index] = false;
              } else if (Number(otherValue) >= Number(c.values[0])) {
                tests[index] = true;
              } else tests[index] = false;

              break; // Break here.

            case '=': // Equal to any.
            case '==': // Alias.
            case '===': // Alias.

              if ($.inArray(otherValue, c.values) !== -1) {
                tests[index] = true;
              } else tests[index] = false;

              break; // Break here.

            case '!=': // Not equal to any.
            case '!==': // Alias.

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
        c.$otherField.on('input change', _.debounce(checkTests, 250, true));
      });

      // Check tests right away.

      checkTests();
    });
  });
})(jQuery);
