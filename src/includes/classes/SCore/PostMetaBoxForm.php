<?php
/**
 * Post meta box form.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\SCore;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes\Core\Error;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Post meta box form.
 *
 * @since 160723 Post meta utils.
 */
class PostMetaBoxForm extends MenuPageForm
{
    /**
     * Class constructor.
     *
     * @since 160723 Post meta utils.
     *
     * @param Classes\App $App  Instance.
     * @param string      $slug Post meta box slug.
     * @param array       $args Any additional behavioral args.
     */
    public function __construct(Classes\App $App, string $slug, array $args = [])
    {
        if (!$slug) { // Empty slug?
            throw $this->c::issue('Missing slug.');
        }
        $args['slug'] = $slug; // Force matching slug.

        parent::__construct($App, '', $args);
    }

    /**
     * Open form tag handler.
     *
     * @since 160723 Post meta utils.
     */
    public function openTag()
    {
        throw $this->c::issue('Post meta boxes use parent form tag.');
    }

    /**
     * Close form tag handler.
     *
     * @since 160723 Post meta utils.
     */
    public function closeTag()
    {
        throw $this->c::issue('Post meta boxes use parent form tag.');
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
        throw $this->c::issue('Post meta boxes use parent submit button.');
    }
}
