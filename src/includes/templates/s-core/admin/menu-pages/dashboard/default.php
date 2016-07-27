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

$apps_by_type = $this->s::getAppsByNetworkWide($this->Wp->is_network_admin);

if ($this->Wp->is_multisite && $this->Wp->is_network_admin) {
    $active_label       = __('Active Network-Only', 'wp-sharks-core');
    $active_description = __('These can only be activated network-wide, and therefore only require a single license key, which covers all child sites in the network.', 'wp-sharks-core').' ';
} elseif ($this->Wp->is_multisite) {
    $active_label       = __('Active Child-Site', 'wp-sharks-core');
    $active_description = __('These require a license key to be entered for each child site in the network; i.e., if you activate them on a child site.', 'wp-sharks-core').' ';
} else {
    $active_label       = __('Active', 'wp-sharks-core');
    $active_description = ''; // Nothing more to add below.
}
$Form                             = $this->s::menuPageForm('§update-license-keys');
$rest_action_data                 = (array) $this->s::restActionData('§update-license-keys', true);
$rest_action_data['license_keys'] = (array) ($rest_action_data['license_keys'] ?? []);
?>
<?= $Form->openTag(); ?>

    <?php
    /*
     * Only if there is an active theme to display.
     * NOTE: Only one theme can be active at any given time.
     */
    ?>
    <?php if (!empty($apps_by_type['theme'])) : ?>

        <?= $Form->openTable(
            sprintf(__('%1$s Themes by %2$s', 'wp-sharks-core'), esc_html($active_label), esc_html($this->App::CORE_CONTAINER_NAME)),
            sprintf(__('%1$sActivating a license key enables automatic updates. <em>If you need to deactivate a license key, simply empty the field and click \'Save Changes\'.</em>', 'wp-sharks-core'), esc_html($active_description))
        ); ?>

        <?php foreach ($apps_by_type['theme'] as $_App) : ?>
            <?= $Form->inputRow([
                'label' => esc_html($_App->Config->©brand['§product_name']),
                'tip'   => sprintf(__('Get your license key from the \'My Account → My Downloads\' page at %1$s™.', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME)),

                'name'        => '[license_keys]['.$_App->Config->©brand['©slug'].']',
                'value'       => (string) ($rest_action_data['license_keys'][$_App->Config->©brand['©slug']] ?? $_App->s::getOption('§license_key')),
                'placeholder' => __('XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX', 'wp-sharks-core'),
            ]); ?>
        <?php endforeach; ?>

        <?= $Form->closeTable(); ?>

    <?php endif; ?>

    <?php
    /*
     * The list of plugins is always shown, even if there are none active.
     * This helps as a visual indication of how the system works; i.e., a key is expected for each site.
     */
    ?>
    <?php if (!empty($apps_by_type['plugin'])) : ?>

        <?= $Form->openTable(
            sprintf(__('%1$s Plugins by %2$s', 'wp-sharks-core'), esc_html($active_label), esc_html($this->App::CORE_CONTAINER_NAME)),
            sprintf(__('%1$sActivating a license key enables automatic updates. <em>If you need to deactivate a license key, simply empty the field and click \'Save Changes\'.</em>', 'wp-sharks-core'), esc_html($active_description))
        ); ?>

        <?php foreach ($apps_by_type['plugin'] as $_App) : ?>
            <?= $Form->inputRow([
                'label' => esc_html($_App->Config->©brand['§product_name']),
                'tip'   => sprintf(__('Get your license key from the \'My Account → My Downloads\' page at %1$s™.', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME)),

                'name'        => '[license_keys]['.$_App->Config->©brand['©slug'].']',
                'value'       => (string) ($rest_action_data['license_keys'][$_App->Config->©brand['©slug']] ?? $_App->s::getOption('§license_key')),
                'placeholder' => __('XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX', 'wp-sharks-core'),
            ]); ?>
        <?php endforeach; ?>

        <?= $Form->closeTable(); ?>

    <?php else : // If there no active plugins at this time. ?>

        <?= $Form->openTable(
            sprintf(__('%1$s Plugins by %2$s', 'wp-sharks-core'), esc_html($active_label), esc_html($this->App::CORE_CONTAINER_NAME)),
            sprintf(__('%1$sActivating a license key enables automatic updates.', 'wp-sharks-core'), esc_html($active_description))
        ); ?>
        <tr>
            <td>
                <?= sprintf(__('No %1$s plugins at this time.', 'wp-sharks-core'), esc_html(mb_strtolower($active_label))); ?>
            </td>
        </tr>
        <?= $Form->closeTable(); ?>

    <?php endif; ?>

    <?php
    /*
     * Submit button, but only if there is something to save.
     */
    ?>
    <?php if (!empty($apps_by_type['theme']) || !empty($apps_by_type['plugin'])) : ?>

        <?= $Form->submitButton(); ?>

    <?php endif; ?>

<?= $Form->closeTag(); ?>
