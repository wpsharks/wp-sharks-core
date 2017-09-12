<?php
/**
 * Template.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core;

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

if (!defined('WPINC')) {
    exit('Do NOT access this file directly.');
}
?>
<div class="<?= esc_attr($cfg->class); ?>">
    <h1 class="-display-hidden" data-wp-notices-here></h1>

    <div class="-container">

        <?php if ($cfg->meta_links) : $_meta_links = $cfg->meta_links; ?>
            <div class="-meta-links">

                <?php if (!empty($_meta_links['restore'])) : unset($_meta_links['restore']); ?>
                    <a class="-restore" href="<?= esc_url($this->s::restoreDefaultOptionsUrl()); ?>" onclick="if(!confirm('<?= __('Are you sure?', 'wp-sharks-core') ?>')) return false;">
                        <?= __('Restore Default Options', 'wp-sharks-core'); ?>
                    </a>
                <?php endif; ?>

                <?= implode(' ', $_meta_links); // Others.?>
            </div>
        <?php endif; ?>

        <?= $cfg->nav_tabs; ?>

        <div class="-content">
            <?= $this->get($cfg->template_file, [], $cfg->template_dir); ?>
        </div>

    </div>
</div>
