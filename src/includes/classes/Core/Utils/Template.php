<?php
/**
 * Template utils.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\Core\Utils;

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
 * Template utils.
 *
 * @since 160702 Template utils.
 */
class Template extends CoreClasses\Core\Utils\Template
{
    /**
     * Locates a template file.
     *
     * @since 160702 Template locater.
     *
     * @param string $file Relative to templates dir.
     * @param string $dir  From a specific directory?
     *
     * @return array `[dir, file, ext]`.
     */
    public function locate(string $file, string $dir = ''): array
    {
        $file           = $this->c::mbTrim($file, '/');
        $dir            = $this->c::mbRTrim($dir, '/');
        $theme_base_dir = $this->App->Config->©fs_paths['§templates_theme_base_dir'];

        if (!$dir && $file) { // Allow WP themes to override default templates.
            if (($template = locate_template($theme_base_dir.'/'.$file))) {
                if (preg_match('/\/\.|\.\/|\.\./u', $this->c::normalizeDirPath($theme_base_dir.'/'.$file))) {
                    throw $this->c::issue(sprintf('Insecure template path: `%1$s`.', $theme_base_dir.'/'.$file));
                }
                return ['dir' => $theme_base_dir, 'file' => $file, 'ext' => $this->c::fileExt($file)];
            }
        }
        return parent::locate($file, $dir);
    }
}
