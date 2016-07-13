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

extract($this->current_vars); // Template variables.

$apps_by_type                     = $this->s::getAppsByType();
$Form                             = $this->s::menuPageForm('§update-license-keys');
$rest_action_data                 = $this->c::doingRestAction('§update-license-keys') ? (array) $this->s::restActionData(true) : [];
$rest_action_data['license_keys'] = (array) ($rest_action_data['license_keys'] ?? []);
?>
<?= $Form->openTag(); ?>

    <?php if (!empty($apps_by_type['theme']) || !empty($apps_by_type['plugin'])) : ?>

        <?php if (!empty($apps_by_type['theme'])) : ?>
            <?= $Form->openTable(
                sprintf(__('Themes by %1$s', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME)),
                __('Activating a license key enables automatic updates. <em>If you need to deactivate a license key, simply empty the field and click \'Save Changes\'.</em>', 'wp-sharks-core')
            ); ?>

            <?php foreach ($apps_by_type['theme'] as $_App) : ?>
                <?= $Form->inputRow([
                    'label' => esc_html($_App->Config->©brand['©name']),
                    'tip'   => sprintf(__('Get your license key from the \'My Account → My Downloads\' page at %1$s™.', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME)),

                    'name'        => '[license_keys]['.$_App->Config->©brand['©slug'].']',
                    'value'       => $rest_action_data['license_keys'][$_App->Config->©brand['©slug']] ?? $_App->Config->§options['§license_key'],
                    'placeholder' => __('XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX', 'wp-sharks-core'),
                ]); ?>
            <?php endforeach; ?>

            <?= $Form->closeTable(); ?>
        <?php endif; ?>

        <?php if (!empty($apps_by_type['plugin'])) : ?>
            <?= $Form->openTable(
                sprintf(__('Plugins by %1$s', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME)),
                __('Activating a license key enables automatic updates. <em>If you need to deactivate a license key, simply empty the field and click \'Save Changes\'.</em>', 'wp-sharks-core')
            ); ?>

            <?php foreach ($apps_by_type['plugin'] as $_App) : ?>
                <?= $Form->inputRow([
                    'label' => esc_html($_App->Config->©brand['©name']),
                    'tip'   => sprintf(__('Get your license key from the \'My Account → My Downloads\' page at %1$s™.', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME)),

                    'name'        => '[license_keys]['.$_App->Config->©brand['©slug'].']',
                    'value'       => $rest_action_data['license_keys'][$_App->Config->©brand['©slug']] ?? $_App->Config->§options['§license_key'],
                    'placeholder' => __('XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX-XXXX', 'wp-sharks-core'),
                ]); ?>
            <?php endforeach; ?>

            <?= $Form->closeTable(); ?>
        <?php endif; ?>

        <?= $Form->submitButton(); ?>

    <?php else : ?>
        <p><?= sprintf(__('No themes/plugins by %1$s are active at this time.', 'wp-sharks-core'), esc_html($this->App::CORE_CONTAINER_NAME)); ?></p>
    <?php endif; ?>

<?= $Form->closeTag(); ?>
