<?php
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
<p>
    <?= sprintf(__('%1$s™ License Key for: <strong>%2$s</strong>', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME), esc_html($this->App->Config->©brand['§product_name'])); ?>

    <small class="-note" style="display:inline-block; margin:0 0 0 2em;"><?= __('(enables automatic updates)', 'wp-sharks-core'); ?></small>
    <?= $this->s::menuPageTip(sprintf(__('Get your license key from the \'My Account → My Downloads\' page at %1$s™.', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME))); ?>
</p>
<form method="post" action="<?= esc_url($this->App->Parent->s::restActionUrl('§update-license-keys')) ?>" style="margin:0.5em 0 .75em 0;">
    <input type="text" name="<?= esc_attr($this->App->Parent->s::restActionFormElementName('[license_keys]['.$this->App->Config->©brand['©slug'].']')) ?>" placeholder="XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX" style="width:400px; max-width:75%;" />
    <button type="submit" class="button button-primary"><?= __('Save', 'wp-sharks-core') ?></button>
</form>
