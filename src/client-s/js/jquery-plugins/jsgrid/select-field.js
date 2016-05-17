(function ($) {
  jsGrid.fields.select.prototype.allowOther = false;
  jsGrid.fields.select.prototype.otherPlaceholderText = '';

  jsGrid.fields.select.prototype.editTemplate = function (value) {
    if (!this.editing) {
      return this.itemTemplate(value);
    }
    var $control = this._createSelect(value);
    // ↑ Allows the use of `other` for editing.
    this.editControl = $control;

    if (value !== undefined) {
      $control.val(String(value));
    }
    return $control; // `<select>` or `<input>`.
  };

  jsGrid.fields.select.prototype._valueItem = function (value) {
    var valueItem; // Initialize.

    if (this.valueField) {
      valueItem = $.grep(this.items, function (item, index) {
        return item[this.valueField] === value;
      }.bind(this))[0] || undefined;
    } else {
      valueItem = this.items[value];
    }
    return valueItem;
  };

  jsGrid.fields.select.prototype.itemTemplate = function (value) {
    var valueItem = this._valueItem(value),
      itemValue = ''; // Initialize.

    if (value !== undefined && valueItem === undefined && !this.readOnly && this.allowOther) {
      itemValue = String(value !== undefined ? value : '');
      return _.escape(itemValue);
    } else {
      itemValue = valueItem && this.textField ? valueItem[this.textField] : valueItem;
      itemValue = String(itemValue === undefined ? '' : itemValue);
      return _.escape(itemValue);
    }
  };

  jsGrid.fields.select.prototype._createSelect = function (value) {
    var $select = $('<select>'),
      $control = $select,
      $optGroup = null,
      $option = null,
      valueItem;

    $select.prop('disabled', this.readOnly);

    $.each(this.items, function (index, item) {
      var value = String(this.valueField ? item[this.valueField] : index),
        text = String(this.textField ? item[this.textField] : item);

      if (text === '---') {
        $optGroup = $('<optgroup>')
          .attr('label', value.replace(/^[\s\-]+|[\s\-]+$/g, ''))
          .appendTo($select);
      } else {
        $option = $('<option>')
          .attr('value', value).text(text)
          .prop('selected', this.selectedIndex === index)
          .appendTo($optGroup ? $optGroup : $select);
      }
    }.bind(this));

    if (!this.readOnly && this.allowOther) {
      if (value !== undefined) {
        valueItem = this._valueItem(value);
      }
      if (value !== undefined && valueItem === undefined) {
        $control = this._createInput(); // Editing w/ `other` input.
        // Note that the value is not set here; it's set by the caller.

        setTimeout(function () { // Wait for DOM entry by caller.
          if ($.contains(document, $control[0])) { // Iff it's in the DOM. It should be.
            $control.after(this._createInputOff($control));
            $control.parent().css('position', 'relative');
          }
        }.bind(this), 500); // Just a very short delay.
      } else {
        $select.on('change', function (e) {
          if ($select.val() === 'other') {
            $control = this._convertToInput($select);
          }
        }.bind(this));
      }
    }
    return $control;
  };

  jsGrid.fields.select.prototype._createInput = function () {
    var $input = $('<input>').attr('type', 'text');
    $input.attr('placeholder', this.otherPlaceholderText);
    return $input; // Simple text input control.
  };

  jsGrid.fields.select.prototype._createInputOff = function ($input) {
    var $inputOff = $('<a href="#">&#x2261;</a>');

    $inputOff.css({
      'top': 0,
      'right': 0,
      'width': '13px',
      'height': '13px',
      'line-height': '13px',
      'border-radius': '50%',
      'position': 'absolute',

      'color': '#000',
      'font-size': '10px',
      'text-rendering': 'geometricPrecision',
      'font-family': 'tahoma,arial,verdana,monospace',
      'text-decoration': 'none',
      'background': '#dcdcdc'
    }).on('click', function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      this._convertToSelect($input);
      $(e.target).remove();
    }.bind(this));

    return $inputOff;
  };

  jsGrid.fields.select.prototype._convertToInput = function ($select) {
    this.selectedIndex = -1;

    var $input = this._createInput();
    var $inputOff = this._createInputOff($input);

    $select.replaceWith($input);

    if (this.insertControl) {
      this.insertControl = $input;
    }
    if (this.editControl) {
      this.editControl = $input;
    }
    if (this.filterControl) {
      this.filterControl = $input;
    }
    $input.focus();
    $input.after($inputOff);
    $input.parent().css('position', 'relative');

    return $input;
  };

  jsGrid.fields.select.prototype._convertToSelect = function ($input) {
    this.selectedIndex = -1;

    var $select = this._createSelect();

    $input.replaceWith($select);

    if (this.insertControl) {
      this.insertControl = $select;
    }
    if (this.editControl) {
      this.editControl = $select;
    }
    if (this.filterControl) {
      this.filterControl = $select;
    }
    return $select;
  };
})(jQuery);
