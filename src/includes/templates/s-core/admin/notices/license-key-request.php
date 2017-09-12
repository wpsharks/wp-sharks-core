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
<?php if (($this->App->Config->§specs['§is_pro'] || $this->App->Config->§specs['§is_elite']) && ($trial_days_remaining = $this->s::trialDaysRemaining()) >= 0) : ?>

    <?php if ($this->s::isTrialExpired()) : // Trial applicable.?>

        <h3 style="color:#c38d00; margin-bottom:.25em;">
            <i class="sharkicon sharkicon-enty-exclamation"></i> <?= __('<strong>IMPORTANT:</strong> Free Trial Expired', 'wp-sharks-core'); ?>
        </h3>

        <form method="post" action="<?= esc_url($this->App->s::brandUrl()); ?>" target="_blank" style="margin:0;">
            <p>
                <?= sprintf(__('License Key for <strong>%1$s</strong>', 'wp-sharks-core'), esc_html($this->App->Config->©brand['§product_name'])); ?>
                <button type="submit" class="button button-small" style="display:inline-block; margin:0 0 0 1em;"><i class="sharkicon sharkicon-enty-cart"></i> <?= __('Buy Now', 'wp-sharks-core'); ?></button><br />
                    <span class="-note" style="margin:0;"><?= __('To restore functionality provided by this software please activate a license key.', 'wp-sharks-core'); ?></span>
                <?= $this->s::menuPageTip(__('Click the \'Buy Now\' button to purchase a license key.', 'wp-sharks-core')); ?>
            </p>
        </form>

        <form method="post" action="<?= esc_url($this->App->Parent->s::restActionUrl('§update-license-keys')); ?>" style="margin:.5em 0 1em 0;">
            <input type="text" name="<?= esc_attr($this->App->Parent->s::restActionFormElementName('[license_keys]['.$this->App->Config->©brand['©slug'].']')); ?>" placeholder="XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX" style="width:400px; max-width:75%;" />
            <button type="submit" class="button button-primary"><?= __('Activate', 'wp-sharks-core'); ?></button>
        </form>

    <?php else : // Still within trial period; e.g., trial just began or notice is recurring.?>

        <form method="post" action="<?= esc_url($this->App->s::brandUrl()); ?>" target="_blank" style="margin:0;">
            <p>
                <?= sprintf(__('License Key for <strong>%1$s</strong>', 'wp-sharks-core'), esc_html($this->App->Config->©brand['§product_name'])); ?>
                <button type="submit" class="button button-small" style="display:inline-block; margin:0 0 0 1em;"><i class="sharkicon sharkicon-enty-cart"></i> <?= __('Buy Now', 'wp-sharks-core'); ?></button>
                <?= $this->s::menuPageTip(__('Click the \'Buy Now\' button to purchase a license key.', 'wp-sharks-core')); ?>
            </p>
        </form>

        <form method="post" action="<?= esc_url($this->App->Parent->s::restActionUrl('§update-license-keys')); ?>" style="margin:.5em 0 .5em 0;">
            <input type="text" name="<?= esc_attr($this->App->Parent->s::restActionFormElementName('[license_keys]['.$this->App->Config->©brand['©slug'].']')); ?>" placeholder="XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX" style="width:400px; max-width:75%;" />
            <button type="submit" class="button button-primary"><?= __('Activate', 'wp-sharks-core'); ?></button>
        </form>

        <form method="post" action="<?= esc_url($this->s::dismissNoticeUrl('§license-key-request', $this->s::defaultMenuPageUrl() ?: null)); ?>" style="margin:.5em 0 1em 0;">
            <span style="display:inline-block; margin:0 .5em 0 .15em; vertical-align:middle; font-variant:small-caps;"><?= __('or', 'wp-sharks-core'); ?></span>

            <?php if ($trial_days_remaining === $this->s::trialDays()) : ?>
                <button type="submit" class="button button-small" style="vertical-align:middle;">
                    <?= __('Begin Free Trial', 'wp-sharks-core'); ?>
                </button>
                <span class="-note" style="margin:0 0 0 .5em; vertical-align:middle;">
                    <?= sprintf(_n('(%1$s day of unrestricted access to all features)', '(%1$s days of unrestricted access to all features)', $trial_days_remaining, 'wp-sharks-core'), $trial_days_remaining); ?>
                </span>
            <?php else : ?>
                <button type="submit" class="button button-small" style="vertical-align:middle;">
                    <?= sprintf(_n('Continue Trial (%1$s day remaining)', 'Continue Trial (%1$s days remaining)', $trial_days_remaining, 'wp-sharks-core'), $trial_days_remaining); ?>
                </button>
                <span class="-note" style="margin:0 0 0 .5em; vertical-align:middle;">
                    <?= __('unrestricted access to all features.', 'wp-sharks-core'); ?>
                </span>
            <?php endif; ?>
        </form>

    <?php endif; ?>

<?php else : // Trial not applicable; e.g., the core, free plugin, MU plugin, etc.?>

    <p>
        <?= sprintf(__('%1$s™ License Key for <strong>%2$s</strong>', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME), esc_html($this->App->Config->©brand['§product_name'])); ?> <small class="-note" style="margin:0 0 0 2em;"><?= __('(enables automatic updates)', 'wp-sharks-core'); ?></small>
        <?= $this->s::menuPageTip(sprintf(__('Get your license key from the \'My Account → My Downloads\' page at %1$s™.', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME))); ?>
    </p>

    <form method="post" action="<?= esc_url($this->App->Parent->s::restActionUrl('§update-license-keys')); ?>" style="margin:.5em 0 1em 0;">
        <input type="text" name="<?= esc_attr($this->App->Parent->s::restActionFormElementName('[license_keys]['.$this->App->Config->©brand['©slug'].']')); ?>" placeholder="XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX" style="width:400px; max-width:75%;" />
        <button type="submit" class="button button-primary"><?= __('Activate', 'wp-sharks-core'); ?></button>
    </form>

<?php endif; ?>
