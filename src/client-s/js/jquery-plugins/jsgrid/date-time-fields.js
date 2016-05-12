(function ($) {
  var momentData = rgvfbtgzxqrdbpcdjvzpcrfrsbtgpdvpMomentData;
  var pickadateData = bvtnafpwxwhxtzqwqumtmwfywfmmgffdPickadateData;

  if (typeof momentData.i18n[momentData.locale] === 'object') {
    moment.updateLocale(momentData.locale, momentData.i18n[momentData.locale]);
  } // This updates a global locale if the key exists.

  var pickers = {}; // Pickers by node ID (across all instances).
  var killingStalePickers = false; // Global kill status.

  jsGridDateTimeFields = function (config) {
    jsGrid.Field.call(this, config);
  };
  jsGridDateTimeFields.prototype = new jsGrid.Field({
    subType: 'date-time',

    datePickerOptions: {},
    timePickerOptions: {},

    emptyDateTimeItemText: '—',
    emptyDateItemText: '—',
    emptyTimeItemText: '—',

    datePlaceholderText: 'date',
    timePlaceholderText: 'time',

    // Applies to `subType=date-time` only.
    noTimeEquals: 'startOfDay', // Or `endOfDay`.

    _datePickerOptions: null,
    _timePickerOptions: null,

    _$dateEditInput: null,
    _$dateInsertInput: null,

    _$timeEditInput: null,
    _$timeInsertInput: null,

    editValue: function () {
      return this._actionTimestamp('edit', this.subType);
    },
    editTemplate: function (value, item) {
      return this._actionTemplate('edit', this.subType, value);
    },

    insertValue: function () {
      return this._actionTimestamp('insert', this.subType);
    },
    insertTemplate: function () {
      return this._actionTemplate('insert', this.subType);
    },

    itemTemplate: function (value, item) {
      return this._timestampFormat(value, this.subType, true);
    },
    sorter: function (value1, value2) {
      return value1 - value2; // Timestamp comparison.
    },

    _pickerFunctionName: function (subType) {
      return 'picka' + subType; // e.g., `pickadate` or `pickatime`.
    },

    _pickerOptions: function (subType) {
      if (!this['_' + subType + 'PickerOptions']) {
        this['_' + subType + 'PickerOptions'] = $.extend({}, pickadateData['default' + this._ucf(subType) + 'Options'], this[subType + 'PickerOptions']);
        this['_' + subType + 'PickerOptions'].container = this._grid._container.parent();
        this['_' + subType + 'PickerOptions'].onClose = function () {
          $(document.activeElement).blur();
        };
      }
      return this['_' + subType + 'PickerOptions'];
    },

    _maybeKillStalePickers: function () {
      if (killingStalePickers) {
        return; // Already running.
      }
      killingStalePickers = true; // Killing.

      for (var _id in pickers) {
        if (!$.contains(this._grid._container[0], pickers[_id].$node[0])) {
          pickers[_id].stop(); // Kill (stop) the picker.
          delete pickers[_id]; // Remove from list.
        }
      } // delete _id; // Housekeeping.
      killingStalePickers = false; // Done kiling now.
    },

    _actionTimestamp: function (action, subType) {
      if (subType === 'date-time') {
        var date = $.trim(this['_$date' + this._ucf(action) + 'Input'].val());
        var time = $.trim(this['_$time' + this._ucf(action) + 'Input'].val());

        date = date === '0' ? '' : date; // Special case (not empty w/ `0`).
        time = time === '0' ? '' : time; // Special case (not empty w/ `0`).

        if (date && !time && this.noTimeEquals === 'startOfDay') {
          time = moment.utc().startOf('day').format(this._pickerOptions('time').momentFormat);
        } else if (date && !time && this.noTimeEquals === 'endOfDay') {
          time = moment.utc().endOf('day').format(this._pickerOptions('time').momentFormat);
        }
        if (date && time) { // A date and a time?
          return this._formatToTimestamp(date + ' ' + time, subType);
        } else if (date) { // Do we have at least the date?
          return this._formatToTimestamp(date, 'date');
        } else if (time) { // The date will be today.
          return this._formatToTimestamp(time, 'time');
        }
        return 0; // Empty timestamp value (default behavior).
      } else {
        return this._formatToTimestamp(this['_$' + subType + this._ucf(action) + 'Input'].val(), subType);
      }
    },

    _actionTemplate: function (action, subType, timestamp) {
      if (action === 'insert' && !this.inserting) {
        return null; // Not applicable.
      } else if (action === 'edit' && !this.editing) {
        return this.itemTemplate(timestamp);
      }
      if (subType === 'date-time') {
        this['_$date' + this._ucf(action) + 'Input'] = // Build input.
          $('<input placeholder="' + _.escape(this.datePlaceholderText) + '"' +
            ' value="' + _.escape(action === 'edit' && timestamp ? this._timestampFormat(timestamp, 'date') : '') + '" />');

        this['_$time' + this._ucf(action) + 'Input'] = // Build input.
          $('<input placeholder="' + _.escape(this.timePlaceholderText) + '"' +
            ' value="' + _.escape(action === 'edit' && timestamp ? this._timestampFormat(timestamp, 'time') : '') + '" />');

        var $table = $( // Both fields at the same time.
          '<table style="box-sizing:border-box; width:100%; border:0; padding:0; margin:0;">' +
          ' <tbody>' +
          '   <tr style="border:0; padding:0; margin:0;">' +
          '     <td class="-date" style="box-sizing:border-box; width:65%; border:0; padding:0; margin:0;"></td>' +
          '     <td class="-time" style="box-sizing:border-box; width:35%; border:0; padding:0; margin:0;"></td>' +
          '   </tr>' +
          ' </tbody>' +
          '</table>'
        ); // Now pop the date and time fields into place via `$.append()`.
        $table.find('.-date').append(this['_$date' + this._ucf(action) + 'Input']);
        $table.find('.-time').append(this['_$time' + this._ucf(action) + 'Input']);

        this._maybeKillStalePickers(); // Kill stale pickers before adding new ones.

        setTimeout(function () { // Requires DOM insertion.
          this['_$date' + this._ucf(action) + 'Input'][this._pickerFunctionName('date')](this._pickerOptions('date'));
          var p1 = this['_$date' + this._ucf(action) + 'Input'][this._pickerFunctionName('date')]('picker');

          this['_$time' + this._ucf(action) + 'Input'][this._pickerFunctionName('time')](this._pickerOptions('time'));
          var p2 = this['_$time' + this._ucf(action) + 'Input'][this._pickerFunctionName('time')]('picker');

          pickers[p1.$node[0].id] = p1; // Adds a new picker by it's `$node` ID.
          pickers[p2.$node[0].id] = p2; // Adds a new picker by it's `$node` ID.
        }.bind(this), 100); // Wait for appendage in jsGrid upstream.

        return $table; // Both fields together in a table.
        //
      } else { // Only a single field in this case.
        this['_$' + subType + this._ucf(action) + 'Input'] = // Build input.
          $('<input placeholder="' + _.escape(this[subType + 'PlaceholderText']) + '"' +
            ' value="' + _.escape(action === 'edit' && timestamp ? this._timestampFormat(timestamp, subType) : '') + '" />');

        this._maybeKillStalePickers(); // Kill stale pickers before adding new ones.

        setTimeout(function () { // Requires DOM insertion.
          this['_$' + subType + this._ucf(action) + 'Input'][this._pickerFunctionName(subType)](this._pickerOptions(subType));
          var p = this['_$' + subType + this._ucf(action) + 'Input'][this._pickerFunctionName(subType)]('picker');
          pickers[p.$node[0].id] = p; // Adds a new picker by it's `$node` ID.
        }.bind(this), 100); // Wait for appendage in jsGrid upstream.

        return this['_$' + subType + this._ucf(action) + 'Input'];
      }
    },

    _timestampFormat: function (timestamp, subType, forDisplay) {
      var formatted = ''; // Initialize.

      if (!(timestamp = parseInt(timestamp))) {
        if (forDisplay && subType === 'date-time') {
          formatted = '<em>' + _.escape(this.emptyDateTimeItemText) + '</em>';
        } else if (forDisplay) {
          formatted = '<em>' + _.escape(this['empty' + this._ucf(subType) + 'ItemText']) + '</em>';
        } else {
          formatted = ''; // Nothing to do here.
        }
      } else if (subType === 'date-time') { // Both the date & the time.
        formatted = moment.utc(timestamp, 'X', momentData.locale).format(this._pickerOptions('date').momentFormat + ' ' + this._pickerOptions('time').momentFormat) + (forDisplay ? ' ' + momentData.i18n.utc : '');
        formatted = forDisplay ? _.escape(formatted) : formatted;
      } else {
        formatted = moment.utc(timestamp, 'X', momentData.locale).format(this._pickerOptions(subType).momentFormat) + (forDisplay ? ' ' + momentData.i18n.utc : '');
        formatted = forDisplay ? _.escape(formatted) : formatted;
      }
      return formatted;
    },

    _formatToTimestamp: function (formatted, subType) {
      var timestamp = 0; // Initialize.

      if (!(formatted = $.trim(formatted)) || formatted === '0') {
        timestamp = 0; // Nothing to do here.
      } else if (subType === 'date-time') { // Both the date & the time.
        timestamp = parseInt(moment.utc(formatted, this._pickerOptions('date').momentFormat + ' ' + this._pickerOptions('time').momentFormat, momentData.locale).format('X'));
      } else {
        timestamp = parseInt(moment.utc(formatted, this._pickerOptions(subType).momentFormat, momentData.locale).format('X'));
      }
      return timestamp;
    },

    _currentInputTableRow: function () {
      var $reference = this._$dateEditInput || this._$dateInsertInput || this._$timeEditInput || this._$timeInsertInput || null;
      var $refClosestRow = $reference ? $reference.closest('.jsgrid-edit-row, .jsgrid-insert-row') : null;

      if ($refClosestRow && $refClosestRow.length === 1) {
        return $refClosestRow; // Closest row.
      } else {
        return null; // Null on failure.
      }
    },

    _ucf: function (string) {
      return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    }
  });

  jsGrid.fields.dateTime = jsGridDateTimeFields;
})(jQuery);
