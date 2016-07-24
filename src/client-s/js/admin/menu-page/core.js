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
    var $menuPageArea = $('.' + x.coreContainerSlug + '-menu-page-area');
    var $menuPageWrapper = $menuPage.find('.' + x.coreContainerSlug + '-menu-page-wrapper');

    /*
     * Block-level tooltips.
     */
    $menuPageArea.tooltip({
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
      items: '[data-toggle~="-jquery-ui-tooltip"]',
      tooltipClass: x.coreContainerSlug + '-jquery-ui-tooltip'
    });
  });
})(jQuery);
