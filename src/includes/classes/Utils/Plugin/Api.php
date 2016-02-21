<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\Utils\Plugin;

use WebSharks\WpSharks\Core\Functions as wc;
use WebSharks\WpSharks\Core\Classes as WCoreClasses;
use WebSharks\WpSharks\Core\Classes\Utils as WCoreUtils;
use WebSharks\WpSharks\Core\Interfaces as WCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as WCoreTraits;
#
use WebSharks\Core\WpSharksCore\Functions\__;
use WebSharks\Core\WpSharksCore\Functions as c;
use WebSharks\Core\WpSharksCore\Classes\Exception;
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Utils as CoreUtils;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;

/**
 * Plugin API utils.
 *
 * @since 16xxxx Plugin API.
 */
class Api extends WCoreClasses\PluginBase
{
    /**
     * File path (absolute).
     *
     * @since 16xxxx Plugin API.
     *
     * @return string Plugin file.
     */
    public function getFile(): string
    {
        return $this->Plugin->file;
    }

    /**
     * File directory (absolute).
     *
     * @since 16xxxx Plugin API.
     *
     * @return string Plugin directory.
     */
    public function getDir(): string
    {
        return $this->Plugin->dir;
    }

    /**
     * Core directory (absolute).
     *
     * @since 16xxxx Plugin API.
     *
     * @return string Core directory.
     */
    public function getCoreDir(): string
    {
        return $this->Plugin->core_dir;
    }

    /**
     * Namespace.
     *
     * @since 16xxxx Plugin API.
     *
     * @return string Plugin namespace.
     */
    public function getNamespace(): string
    {
        return $this->Plugin->namespace;
    }

    /**
     * Is pro version?
     *
     * @since 16xxxx Plugin API.
     *
     * @return bool Is pro version?
     */
    public function isPro(): bool
    {
        return $this->Plugin->Config->brand['is_pro'];
    }

    /**
     * Version.
     *
     * @since 16xxxx Plugin API.
     *
     * @return string Plugin version.
     */
    public function getVersion(): string
    {
        return $this->Plugin::VERSION;
    }

    /**
     * Config values.
     *
     * @since 16xxxx Plugin API.
     *
     * @return array Plugin config.
     */
    public function getConfig(): array
    {
        return (array) $this->Plugin->Config;
    }

    /**
     * Config options.
     *
     * @since 16xxxx Plugin API.
     *
     * @return array Plugin config options.
     */
    public function getOptions(): array
    {
        $this->Plugin->Config->options;
    }

    /**
     * Update options.
     *
     * @since 16xxxx Plugin API.
     *
     * @param array $options New options.
     *
     * @note `null` options force a default value.
     */
    public function updateOptions(array $options)
    {
        $this->Plugin->Config->updateOptions($options);
    }
}
