<?php
/**
 * Widget form.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore;

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
 * Widget form.
 *
 * @since 160729 Post meta utils.
 */
class WidgetForm extends MenuPageForm
{
    /**
     * Class constructor.
     *
     * @since 160729 Post meta utils.
     *
     * @param Classes\App               $App    Instance.
     * @param Classes\SCore\Base\Widget $Widget A widget instance.
     * @param array                     $args   Any additional behavioral args.
     */
    public function __construct(Classes\App $App, Classes\SCore\Base\Widget $Widget, array $args = [])
    {
        $args['Widget'] = $Widget;

        parent::__construct($App, '', $args);
    }

    /**
     * Open form tag handler.
     *
     * @since 160729 Post meta utils.
     */
    public function openTag()
    {
        throw $this->c::issue('Widgets use parent form tag.');
    }

    /**
     * Close form tag handler.
     *
     * @since 160729 Post meta utils.
     */
    public function closeTag()
    {
        throw $this->c::issue('Widgets use parent form tag.');
    }

    /**
     * Create submit button.
     *
     * @since 160709 Menu page utils.
     *
     * @param string $label Optional label.
     *
     * @return string Raw HTML markup.
     */
    public function submitButton(string $label = '')
    {
        throw $this->c::issue('Widgets have their own submit button.');
    }
}
