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

/**
 * Nonce utils.
 *
 * @since 16xxxx WP notices.
 */
class Nonce extends Classes\SCore\Base\Core
{
    /**
     * Add an nonce to a URL.
     *
     * @since 16xxxx First documented version.
     *
     * @param string $url    Input URL.
     * @param string $action Action identifier.
     *
     * @return string URL w/ an nonce.
     */
    public function urlAdd(string $url, string $action = '-1'): string
    {
        return $this->c::addUrlQueryArgs(['_wpnonce' => wp_create_nonce($action)], $url);
    }

    /**
     * Remove an nonce from a URL.
     *
     * @since 16xxxx First documented version.
     *
     * @param string $url Input URL.
     *
     * @return string URL w/o an nonce.
     */
    public function urlRemove(string $url): string
    {
        return $this->c::removeUrlQueryArgs(['_wpnonce'], $url);
    }

    /**
     * Request contains a valid nonce?
     *
     * @since 16xxxx First documented version.
     *
     * @param string $action Action identifier.
     *
     * @return bool True if request contains a valid nonce.
     */
    public function isValid(string $action = '-1'): bool
    {
        return (bool) wp_verify_nonce($_REQUEST['_wpnonce'] ?? '', $action);
    }

    /**
     * Require a valid nonce.
     *
     * @since 16xxxx First documented version.
     *
     * @param string $action Action identifier.
     */
    public function requireValid(string $action = '-1')
    {
        if (!$this->isValid($action)) {
            $this->s::dieForbidden();
        }
    }
}
