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
    <?= __('We\'re a globally-distributed team of enthusiastic open-source developers who build software for WordPress', 'wp-sharks-core'); ?> <i class="sharkicon sharkicon-wordpress"></i>
</h3>

<p>
    <?= sprintf(__('<a href="%2$s" target="_blank">%1$s</a> was founded by business partners <a href="%3$s" target="_blank">Jason Caldwell</a> and <a href="%4$s" target="_blank">Raam Dev</a>. Over the years we\'ve built everything from simple contact forms, to ecommerce platforms, cache engines, social-media tools, and more. <em style="display:inline-block; margin-left:.5em;">Always on top of WordPress.</em>', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME), esc_url($this->s::coreUrl('/')), esc_url($this->s::coreUrl('/vendor/jaswsinc')), esc_url($this->s::coreUrl('/vendor/raamdev'))); ?>
</p>

<p>
    <?= __('Today, our team includes talented individuals from Florida, Georgia, Alaska, the Phillipines, Mexico, India, and occassionally from aquatic habitats at various temperatures where there\'s plenty of other fish to feed on 🐟 — we are Sharks after all', 'wp-sharks-core'); ?> <i class="sharkicon sharkicon-smile-o"></i>
</p>

<p>
    <?= sprintf(__('A developer\'s life is filled with fun and we all love what we do. We find great pleasure in working with each other, with our customers, and with others in the WordPress community. While there is distance between us, we\'re a really tight group (good at online collaboration), and we work very hard on common goals; e.g., to produce great software and help others. We pride ourselves on our level of <a href="%1$s" target="_blank">customer support</a> and response time.', 'wp-sharks-core'), esc_url($this->s::coreUrl('/support'))); ?>
</p>

<p style="font-size:110%; font-style:italic;">
    <?= __('Your WordPress experience will never be the same.', 'wp-sharks-core'); ?>
</p>
