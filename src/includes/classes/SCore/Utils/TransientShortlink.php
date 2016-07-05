<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Transient shortlink utils.
 *
 * @since 160704 Transient shortlink utils.
 */
class TransientShortLink extends Classes\SCore\Base\Core implements CoreInterfaces\SecondConstants
{
    /**
     * Transient shortlink var.
     *
     * @since 160704 Transient shortlink utils.
     *
     * @type string Transient shortlink var.
     */
    protected $var;

    /**
     * Class constructor.
     *
     * @since 160704 Transient shortlink utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->var = $this->App->Config->©brand['©short_var'].'_ts';
    }

    /**
     * Transient shortlink.
     *
     * @since 160704 Transient utils.
     *
     * @param string $long_url      URL to shorten.
     * @param int    $expires_after Expires after (in seconds).
     *
     * @return string Transient shortlink.
     */
    public function __invoke(string $long_url, int $expires_after = null): string
    {
        $expires_after    = $expires_after ?? $this::SECONDS_IN_DAY;
        $transient_key    = sha1('transient-shortlink-'.$long_url);
        $transient_hash   = $this->s::setTransient($transient_key, $long_url, $expires_after, true);
        return $shortlink = $this->c::addUrlQueryArgs([$this->var => $transient_hash], home_url('/'));
    }

    /**
     * Transient redirects.
     *
     * @since 160704 Transient utils.
     */
    public function onWpLoaded()
    {
        if (!empty($_REQUEST[$this->var])) {
            $transient_hash = (string) $_REQUEST[$this->var];
        } else {
            return; // Not applicable.
        }
        if (!($long_url = $this->s::getTransient('', $transient_hash))) {
            dieInvalid(__('Sorry, link expired.', 'wp-sharks-core'));
        }
        wp_redirect($long_url, 301);
        exit; // Stop here.
    }
}
