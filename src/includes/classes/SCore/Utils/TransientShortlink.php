<?php
/**
 * Transient shortlink utils.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
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
class TransientShortlink extends Classes\SCore\Base\Core implements CoreInterfaces\SecondConstants
{
    /**
     * Shortlink var.
     *
     * @since 160704 Shortlink var.
     *
     * @var string Shortlink var.
     */
    protected $var;

    /**
     * Shortlink slug.
     *
     * @since 160704 Shortlink slug.
     *
     * @var string Shortlink slug.
     */
    protected $slug;

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

        $this->var  = $this->App->Config->©brand['©short_var'].'_ts';
        $this->slug = $this->App->Config->©brand['©short_slug'].'-ts';
    }

    /**
     * Transient shortlink.
     *
     * @since 160704 Transient utils.
     *
     * @param string $long_url      URL to shorten.
     * @param string $descriptor    Optional descriptor.
     * @param int    $expires_after Expires after (in seconds).
     *
     * @return string Transient shortlink.
     */
    public function __invoke(string $long_url, string $descriptor = '', int $expires_after = null): string
    {
        if (!$long_url) {
            return ''; // Not possible.
        }
        $WP_Rewrite = $GLOBALS['wp_rewrite'];

        $expires_after  = $expires_after ?? $this::SECONDS_IN_DAY;
        $transient_key  = sha1('transient-shortlink-'.$long_url);
        $transient_hash = $this->s::setTransient($transient_key, $long_url, $expires_after, true);

        if ($WP_Rewrite->using_mod_rewrite_permalinks()) {
            $descriptor       = $this->c::mbTrim($descriptor, '/'); // Only works with fancy permalinks.
            return $shortlink = home_url('/'.$this->slug.'/'.$transient_hash.($descriptor ? '/'.urlencode($descriptor) : ''));
        } else {
            return $shortlink = $this->c::addUrlQueryArgs([$this->var => $transient_hash], home_url('/'));
        }
    }

    /**
     * Transient redirects.
     *
     * @since 160704 Transient utils.
     */
    public function onWpLoaded()
    {
        if ($this->c::isCli()) {
            return; // Not applicable.
        }
        $WP_Rewrite = $GLOBALS['wp_rewrite'];

        if (!empty($_REQUEST[$this->var])) {
            $transient_hash = (string) $_REQUEST[$this->var];
        } elseif (mb_stripos($_SERVER['REQUEST_URI'] ?? '', '/'.$this->slug.'/') !== false && $WP_Rewrite->using_mod_rewrite_permalinks()) {
            $base_path = $this->c::mbRTrim((string) $this->c::parseUrl(home_url('/'), PHP_URL_PATH), '/');
            $path      = $base_path ? mb_substr($this->c::currentPath(), mb_strlen($base_path)) : $this->c::currentPath();

            if ($path && preg_match('/^\/'.$this->c::escRegex($this->slug).'\/(?<transient_hash>[^\/]+)(?:\/|$)/ui', $path, $_m)) {
                $transient_hash = $_m['transient_hash'];
            } else {
                return; // Not appplicable; i.e., unable to find transient hash in path.
            }
        } else {
            return; // Not applicable.
        }
        if (!($long_url = $this->s::getTransient('', $transient_hash))) {
            $this->s::dieInvalid(__('Sorry, link expired.', 'wp-sharks-core'));
        }
        wp_redirect($long_url, 301).exit(); // Stop on redirection.
    }
}
