<?php
/**
 * Template.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core;

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

extract($this->vars); // Template variables.
?>
<h3>
    <a href="<?= esc_url($this->s::coreUrl()); ?>" target="_blank" class="-no-icon"><img src="<?= esc_url($this->c::appUrl('/client-s/images/logo-500.png')); ?>" style="float:right; margin:0 0 5em 5em; width:300px;" /></a>
    <?= __('We\'re a globally-distributed team of enthusiastic developers who build great open-source software for WordPress.', 'wp-sharks-core'); ?> <i class="sharkicon sharkicon-wordpress"></i>
</h3>

<p>
    <?= sprintf(__('<a href="%2$s" target="_blank">%1$s</a> was founded by business partners <a href="%3$s" target="_blank">Jason Caldwell</a> and <a href="%4$s" target="_blank">Raam Dev</a>. Over the years we\'ve built everything everything from advanced e-commerce platforms and caching engines, to simple contact forms and social-media tools—<em style="display:inline-block;">always with WordPress at the core.</em>', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME), esc_url($this->s::coreUrl('/')), esc_url($this->s::coreUrl('/vendor/jaswsinc')), esc_url($this->s::coreUrl('/vendor/raamdev'))); ?>
</p>

<p>
    <?= __('Today, our team is comprised of talented engineers and developers working remotely from all around the world: Florida, Georgia, Alaska, New Hampshire, the Phillipines, Mexico, India, and occasionally from aquatic habitats at various temperatures where there\'s plenty of other fish to feed on 🐟 — we are Sharks after all.', 'wp-sharks-core'); ?> <i class="sharkicon sharkicon-smile-o"></i>
</p>

<p>
    <?= sprintf(__('A developer\'s life is filled with fun and we all love what we do. We find great pleasure in working with each other, with our customers, and with the entire WordPress community. While there is distance between us, we\'re a really tight group (good at online collaboration), and we work very hard on common goals like producing great software and helping others. We pride ourselves on great <a href="%1$s" target="_blank">customer service</a>, fast response times, and a dedication to maintaining our software.', 'wp-sharks-core'), esc_url($this->s::coreUrl('/support'))); ?>
</p>

<p style="font-size:110%; font-style:italic;">
    <?= __('Your WordPress experience will never be the same.', 'wp-sharks-core'); ?>
</p>
