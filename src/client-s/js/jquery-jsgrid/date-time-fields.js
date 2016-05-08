(function ($) {
  var momentData = rgvfbtgzxqrdbpcdjvzpcrfrsbtgpdvpMomentData;
  var pickadateData = bvtnafpwxwhxtzqwqumtmwfywfmmgffdPickadateData;

  if (typeof momentData.i18n[momentData.locale] === 'object') {
    moment.updateLocale(momentData.locale, momentData.i18n[momentData.locale]);
  } // This updates a global locale if the key exists.

  jsGridDateTimeFields = function (config) {
    jsGrid.Field.call(this, config);
  };
  jsGridDateTimeFields.prototype = new jsGrid.Field({
    subType: 'date-time',

    emptyDateItemText: '',
    emptyTimeItemText: '',
    datePlaceholderText: '',
    timePlaceholderText: '',

    datePickerOptions: {},
    timePickerOptions: {},

    _datePickerOptions: null,
    _timePickerOptions: null,

    _$dateEditInput: null,
    _$dateInsertInput: null,

    _$timeEditInput: null,
    _$timeInsertInput: null,

    sorter: function (timestamp1, timestamp2) {
      return timestamp1 - timestamp2;
    },
    itemTemplate: function (timestamp) {
      if (this.subType === 'date') {
        return this.timestampFormat(timestamp, 'date');
      } else {
        throw 'Invalid subType.';
      }
    },
    insertValue: function () {
      if (this.subType === 'date') {
        return this.formatToTimestamp(this._$dateInsertInput.val(), 'date');
      } else {
        throw 'Invalid subType.';
      }
    },
    editValue: function () {
      if (this.subType === 'date') {
        return this.formatToTimestamp(this._$dateEditInput.val(), 'date');
      } else {
        throw 'Invalid subType.';
      }
    },
    insertTemplate: function () {
      if (!this.inserting) {
        return null;
      }
      if (this.subType === 'date') {
        if (this._$dateInsertInput) {
          this._$dateInsertInput[this.pickerName('date')]('stop');
        }
        this._$dateInsertInput = $('<input placeholder="' + this.datePlaceholderText + '" />');

        setTimeout(function () { // Requires DOM insertion.
          this._$dateInsertInput[this.pickerName('date')](this.pickerOptions('date'));
        }.bind(this), 50);

        return this._$dateInsertInput;
      } else {
        throw 'Invalid subType.';
      }
    },
    editTemplate: function (timestamp) {
      if (this.subType === 'date') {
        if (this._$dateEditInput) {
          this._$dateEditInput[this.pickerName('date')]('stop');
        }
        this._$dateEditInput = $('<input placeholder="' + this.datePlaceholderText + '" value="' + this.timestampFormat(timestamp, 'date') + '" />');

        setTimeout(function () { // Requires DOM insertion.
          this._$dateEditInput[this.pickerName('date')](this.pickerOptions('date'));
        }.bind(this), 50);

        return this._$dateEditInput;
      } else {
        throw 'Invalid subType.';
      }
    },
    pickerName: function (subType) {
      if (subType === 'date') {
        return 'pickadate';
      } else if (subType === 'time') {
        return 'pickatime';
      } else {
        throw 'Invalid subType.';
      }
    },
    pickerOptions: function (subType) {
      if (subType === 'date') {
        if (!this._datePickerOptions) {
          this._datePickerOptions = $.extend({}, pickadateData.defaultDateOptions, this.datePickerOptions);
          this._datePickerOptions.container = this._grid._container.parent();
        }
        return this._datePickerOptions;
      } else if (subType === 'time') {
        if (!this._timePickerOptions) {
          this._timePickerOptions = $.extend({}, pickadateData.defaultTimeOptions, this.timePickerOptions);
          this._timePickerOptions.container = this._grid._container.parent();
        }
        return this._timePickerOptions;
      } else {
        throw 'Invalid subType.';
      }
    },
    timestampFormat: function (timestamp, subType) {
      if ((timestamp = parseInt(timestamp))) {
        return moment.utc(timestamp, 'X', momentData.locale).format(this.pickerOptions(subType).momentFormat);
      }
      return ''; // Default formatted string.
    },
    formatToTimestamp: function (formatted, subType) {
      if ((formatted = $.trim(formatted)) && formatted !== '0') {
        return parseInt(moment.utc(formatted, this.pickerOptions(subType).momentFormat, momentData.locale).format('X'));
      }
      return 0; // Default timestamp integer.
    }
  });
  jsGrid.fields.dateTime = jsGridDateTimeFields;
})(jQuery);
