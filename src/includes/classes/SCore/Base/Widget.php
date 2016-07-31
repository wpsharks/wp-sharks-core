<?php
/**
 * Widget base.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Base;

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
 * Widget base.
 *
 * @since 160731.37352 Initial release.
 */
abstract class Widget extends \WP_Widget
{
    /**
     * WP common.
     *
     * @since 160524
     *
     * @var Wp
     */
    protected $Wp;

    /**
     * Application.
     *
     * @since 160731.37352
     *
     * @var Classes\App
     */
    protected $App;

    /**
     * Default options.
     *
     * @since 160731.37352
     *
     * @var array
     */
    protected $default_options;

    /**
     * Class constructor.
     *
     * @since 160731.37352 Initial release.
     *
     * @param Classes\App $App             App.
     * @param array       $args            Configuration.
     * @param array       $default_options Default options.
     */
    public function __construct(Classes\App $App, array $args, array $default_options = [])
    {
        $default_args = [
            'slug'        => '',
            'name'        => '',
            'description' => '',
            'class'       => '',
        ];
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);
        $args = array_map('strval', $args);

        if (!$args['slug'] || !$args['name'] || !$args['description']) {
            throw $this->App->c::issue('Slug, name, and description required.');
        }
        $this->App             = $App;
        $this->Wp              = $this->App->Wp;
        $this->default_options = array_merge(['title' => ''], $default_options);

        $id_base = $this->App->Config->©brand['©slug'].'-'.$args['slug'];

        parent::__construct($id_base, $args['name'], [
            'classname'   => $id_base.($args['class'] ? ' '.$args['class'] : ''),
            'description' => $args['description'],
        ]);
    }

    /**
     * Outputs the options form on admin.
     *
     * @since 160731.37352 Initial release.
     *
     * @param Classes\SCore\WidgetForm $Form    Instance.
     * @param array                    $options Options.
     *
     * @return string Form content markup.
     */
    protected function formContent(Classes\SCore\WidgetForm $Form, array $options): string
    {
        return ''; // For extenders.
    }

    /**
     * Widget content markup.
     *
     * @since 160731.37352 Initial release.
     *
     * @param array $options Options.
     *
     * @return string Widget content markup.
     */
    protected function widgetContent(array $options): string
    {
        return ''; // For extenders.
    }

    /**
     * Outputs the options form.
     *
     * @since 160731.37352 Initial release.
     *
     * @param array $options Options.
     */
    public function form($options)
    {
        $options = $this->merge([], (array) $options);
        $Form    = $this->App->s::widgetForm($this, []);

        $class = ''; // Initialize.
        $class .= $this->App::CORE_CONTAINER_SLUG.'-menu-page-area';
        $class .= ' '.$this->App::CORE_CONTAINER_SLUG.'-widget-wrapper';
        $class .= ' '.$this->App->Config->©brand['©slug'].'-widget-wrapper';
        $class .= $this->id_base !== $this->App->Config->©brand['©slug'] ? ' '.$this->id_base.'-widget-wrapper' : '';

        echo '<div class="'.esc_attr($class).'">';
        echo    $Form->openTable();

        echo      $Form->inputRow([
            'label' => __('Title:', 'wp-sharks-core'),
            'name'  => 'title',
            'value' => $options['title'],
        ]);
        echo      $this->formContent($Form, $options);

        echo    $Form->closeTable();
        echo '</div>';
    }

    /**
     * Output widget markup.
     *
     * @since 160731.37352 Initial release.
     *
     * @param array $args    Args.
     * @param array $options Options.
     */
    public function widget($args, $options)
    {
        $default_args = [
            'before_widget' => '', 'after_widget' => '',
            'before_title'  => '', 'after_title' => '',
        ];
        $args = (array) $args;
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);
        $args = array_map('strval', $args);

        $options          = $this->merge([], (array) $options);
        $options['title'] = apply_filters('widget_title', $options['title'], $options, $this->id_base);

        echo $args['before_widget'];

        if ($options['title']) { // Only if there is a title.
            echo $args['before_title'].$options['title'].$args['after_title'];
        }
        echo $this->widgetContent($options);
        echo $args['after_widget'];
    }

    /**
     * Update widget options on save.
     *
     * @since 160731.37352 Initial release.
     *
     * @param array $new     New options.
     * @param array $options Options.
     *
     * @return array All options after update.
     */
    public function update($new, $options)
    {
        $new = (array) $new;
        $new = $this->App->c::unslash($new);
        $new = $this->App->c::mbTrim($new);

        return $this->merge((array) $options, $new);
    }

    /**
     * Merge options.
     *
     * @since 160731.37352 Initial release.
     *
     * @param array $base  Base array.
     * @param array $merge Array to merge.
     *
     * @return array The resuling array after merging.
     *
     * @internal `null` options force a default value.
     */
    protected function merge(array $base, array $merge): array
    {
        $options = array_merge($this->default_options, $base, $merge);
        $options = array_intersect_key($options, $this->default_options);

        foreach ($this->default_options as $_key => $_default_option_value) {
            if (!isset($options[$_key])) {
                $options[$_key] = $_default_option_value;
            } else {
                settype($options[$_key], gettype($_default_option_value));
            }
        } // unset($_key, $_default_option_value);

        return $options;
    }
}
