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
     * @return array Template `dir`, `file`, and `ext`.
     */
    public function locate(string $file, string $dir = ''): array
    {
        $dir  = $this->c::mbRTrim($dir, '/');
        $file = $this->c::mbTrim($file, '/');

        if (!$dir && $file) { // Allow WP themes to override default templates.
            if (($template = locate_template($this->App->Config->©fs_paths['§templates_theme_base_dir'].'/'.$file))) {
                $dir = $this->c::mbRTrim(mb_substr($template, 0, -mb_strlen($file)), '/');
                return ['dir' => $dir, 'file' => $file, 'ext' => $this->c::fileExt($file)];
            }
        }
        return parent::locate($file, $dir);
    }
}
