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

extract($¤vars); // Template variables.
?>
<p><?= sprintf(__('<strong>%1$s License Key</strong> <small>(to enable automatic updates)</small>', 'wp-sharks-core'), esc_html($this->App->Config->©brand['©name']), esc_html($this->App::VERSION)) ?></p>
<form method="post" action="<?= esc_url($this->s::saveOptionsUrl()) ?>" style="margin:0.5em 0;">
    <input type="text" name="<?= esc_attr($this->s::restActionFormElementName('§license_key')) ?>" placeholder="<?= esc_attr(__('enter license key here...', 'wp-sharks-core')) ?>" style="width:400px; max-width:75%;" />
    <button type="submit" class="button button-primary"><?= __('Save', 'wp-sharks-core') ?></button>
</form>
