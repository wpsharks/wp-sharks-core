(function ($) {
  $(document).ready(function () {
    /*
     * Prefix/data.
     */
    var prefix = 'nuqvUt59Aqv9RhzvhjafETjNS5hAFScX',
      x = window[prefix + 'MenuPageData'];

    /*
     * Element cache.
     */
    var $menuPage = $('.' + x.coreContainerSlug + '-menu-page');
    var $menuPageWrapper = $menuPage.find('.' + x.coreContainerSlug + '-menu-page-wrapper');

    /*
     * Block-level tooltips.
     */
    $menuPageWrapper.tooltip({
      show: false, // No animation.
      hide: false, // No animation.

      position: {
        my: 'center bottom',
        at: 'center top-10',
        using: function (position, feedback) {
          $(this).css(position).addClass(feedback.vertical + ' ' + feedback.horizontal);
        },
        collision: 'flip' // Flip if unable to see it.
      },
      content: function () {
        return $(this).prop('title');
      },
      items: '[data-toggle~="core.jquery-ui-tooltip"]',
      tooltipClass: x.coreContainerSlug + '-jquery-ui-tooltip'
    });
  });
})(jQuery);
