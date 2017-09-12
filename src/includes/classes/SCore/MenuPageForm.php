<?php
/**
 * Menu page form.
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
 * Menu page form.
 *
 * @since 160709 Menu page utils.
 */
class MenuPageForm extends Classes\SCore\Base\Core
{
    /**
     * ReST action identifier.
     *
     * @since 160709 Menu page utils.
     *
     * @type string ReST action identifier.
     */
    public $action;

    /**
     * Form configuration args.
     *
     * @since 160723 Menu page utils.
     *
     * @type \StdClass Form configuration args.
     */
    public $cfg; // Also for extenders.

    /**
     * Class constructor.
     *
     * @since 160709 Menu page utils.
     *
     * @param Classes\App $App    Instance.
     * @param string      $action ReST action identifier.
     * @param array       $args   Any additional behavioral args.
     */
    public function __construct(Classes\App $App, string $action, array $args = [])
    {
        parent::__construct($App);

        $this->action = $action;

        $default_args = [
            'Widget' => null,
            'slug'   => '',

            'auto_prefix' => true,
            'action_url'  => '',
            'method'      => 'post',
        ];
        $this->cfg = (object) array_merge($default_args, $args);

        if (!($this->cfg->Widget instanceof Classes\SCore\Base\Widget)) {
            $this->cfg->Widget = null; // Invalid; nullify.
        }
        $this->cfg->slug = (string) $this->cfg->slug;

        $this->cfg->auto_prefix = (bool) $this->cfg->auto_prefix;
        $this->cfg->action_url  = (string) $this->cfg->action_url;
        $this->cfg->method      = (string) $this->cfg->method;

        if ($this->cfg->slug && $this->cfg->auto_prefix) {
            $this->cfg->slug = $this->App->Config->©brand['©slug'].'-'.$this->cfg->slug;
        }
        if (!$this->action && !$this->cfg->Widget && !$this->cfg->slug) {
            throw $this->c::issue('Must have an `action`, `Widget`, or `slug`.');
        }
    }

    /**
     * Open `<form>` tag for action.
     *
     * @since 160709 Menu page utils.
     *
     * @return string Raw HTML markup.
     */
    public function openTag()
    {
        return '<form'.
               ' method="'.esc_attr($this->cfg->method).'"'.

               ($this->action
                    ? ' action="'.esc_url($this->s::restActionUrl($this->action)).'"'
                    : ' action="'.esc_url($this->cfg->action_url).'"'
               ).// NOTE: The `action` can also be empty.

               ' enctype="multipart/form-data"'.
               ' accept-charset="utf-8"'.
               ' autocomplete="off"'.
               '>';
    }

    /**
     * Open `<table>` tag.
     *
     * @since 160709 Menu page utils.
     *
     * @param string $heading     Optional heading.
     * @param string $description Optional description.
     * @param array  $args        Any behavioral args.
     *
     * @return string Raw HTML markup.
     */
    public function openTable(string $heading = '', string $description = '', array $args = [])
    {
        $default_args = [
            'class' => '', // e.g., `-display-block`.
        ];
        $args += $default_args;
        $args['class'] = (string) $args['class'];

        $markup = $heading ? '<h2>'.$heading.'</h2>' : '';

        if ($description && mb_stripos($description, '<') === 0) {
            $markup .= $description; // Append HTML block.
        } elseif ($description) {
            $markup .= '<p>'.$description.'</p>';
        }
        $markup .= '<table class="-form-table'.($args['class'] ? ' '.esc_attr($args['class']) : '').'">';
        $markup .= '<tbody>';

        return $markup;
    }

    /**
     * Creates an HR row.
     *
     * @since 170218.31677 Menu page utils.
     *
     * @param array $args Configuration args.
     *
     * @return string Raw HTML markup.
     */
    public function hrRow(array $args = [])
    {
        echo '<tr class="-hr"><td colspan="2"><hr /></td></tr>';
    }

    /**
     * Creates an input row.
     *
     * @since 160709 Menu page utils.
     *
     * @param array $args Configuration args.
     *
     * @return string Raw HTML markup.
     */
    public function inputRow(array $args = [])
    {
        $default_args = [
            // Required.
            'label' => '',

            // Suggested.
            'tip' => '',

            // Required.
            'name'  => '',
            'value' => '',

            // Optional.
            'type' => 'text',

            // Optional.
            'placeholder' => '',
            'note'        => '',

            // Optional.
            'class' => '',
            'style' => '',
            'attrs' => '',

            // Optional.
            'if' => '',
        ];
        if (!array_key_exists('value', $args)) {
            throw $this->c::issue('Missing `value` key.');
        }
        $cfg = array_merge($default_args, $args);
        $cfg = array_intersect_key($cfg, $default_args);
        $cfg = array_map('strval', $cfg);

        if (!$cfg['name']) {
            throw $this->c::issue('Arg `name` is required.');
        } elseif (in_array($cfg['type'], ['checkbox', 'radio', 'reset', 'button', 'image'], true)) {
            throw $this->c::issue(sprintf('Input `type="%1$s"` is unsupported at this time.', $cfg['type']));
        }
        $cfg['var_name'] = $cfg['name']; // Internal copy.
        $cfg['id']       = $this->elementId($cfg['var_name']);
        $cfg['name']     = $this->elementName($cfg['var_name']);
        $cfg['class']    = $this->c::mbTrim($cfg['class'].' '.$this->elementClass($cfg['var_name']));

        $cfg['tip']  = $cfg['tip'] ? $this->s::menuPageTip($cfg['tip']) : '';
        $cfg['note'] = $cfg['note'] ? $this->s::menuPageNote($cfg['note']) : '';

        if ($cfg['type'] === 'hidden') {
            $markup = '<tr class="-hidden">';
            $markup .= '<td colspan="2">';
            $markup .= '<input'. // Hidden input value.
                            ' type="'.esc_attr($cfg['type']).'"'.
                            ' name="'.esc_attr($cfg['name']).'" id="'.esc_attr($cfg['id']).'"'.
                            ' class="'.esc_attr($cfg['class']).'" style="'.esc_attr($cfg['style']).'"'.
                            ' placeholder="'.esc_attr($cfg['placeholder']).'" autocomplete="new-password"'.
                            ' value="'.($cfg['type'] === 'url' ? esc_url($cfg['value']) : esc_attr($cfg['value'])).'"'.
                            ($cfg['attrs'] ? ' '.$cfg['attrs'] : '').
                            ' />';
            $markup .= '</td>';
            $markup .= '</tr>';
        } else { // Any other type of `<input>` tag.
            $markup = '<tr'.($cfg['if'] ? ' data-if="'.esc_attr($cfg['if']).'"' : '').'>';

            if ($cfg['label']) {
                $markup .= '<th scope="row">';
                $markup .= '<div>'; // For positioning.
                $markup .= $cfg['tip'];
                $markup .= '<label for="'.esc_attr($cfg['id']).'" title="'.esc_attr($cfg['label']).'">'.
                                $cfg['label'].
                           '</label>';
                $markup .= '</div>';
                $markup .= '</th>';
            }
            $markup .= '<td'.(!$cfg['label'] ? ' colspan="2"' : '').'>';
            $markup .= '<div>'; // For positioning.
            $markup .= '<input'.
                            ' type="'.esc_attr($cfg['type']).'"'.
                            ' name="'.esc_attr($cfg['name']).'" id="'.esc_attr($cfg['id']).'"'.
                            ' class="'.esc_attr($cfg['class']).'" style="'.esc_attr($cfg['style']).'"'.
                            ' placeholder="'.esc_attr($cfg['placeholder']).'" autocomplete="new-password"'.
                            ' value="'.($cfg['type'] === 'url' ? esc_url($cfg['value']) : esc_attr($cfg['value'])).'"'.
                            ($cfg['attrs'] ? ' '.$cfg['attrs'] : '').
                            ' />';
            $markup .= $cfg['note'];
            $markup .= '</div>';
            $markup .= '</td>';

            $markup .= '</tr>';
        }
        return $markup;
    }

    /**
     * Creates a textarea row.
     *
     * @since 160709 Menu page utils.
     *
     * @param array $args Configuration args.
     *
     * @return string Raw HTML markup.
     */
    public function textareaRow(array $args = [])
    {
        $default_args = [
            // Required.
            'label' => '',

            // Suggested.
            'tip' => '',

            // Required.
            'name'  => '',
            'value' => '',

            // Optional.
            'placeholder' => '',
            'note'        => '',

            // Optional.
            'class' => '',
            'style' => '',
            'attrs' => '',

            // Optional.
            'if' => '',
        ];
        if (!array_key_exists('value', $args)) {
            throw $this->c::issue('Missing `value` key.');
        }
        $cfg = array_merge($default_args, $args);
        $cfg = array_intersect_key($cfg, $default_args);
        $cfg = array_map('strval', $cfg);

        if (!$cfg['name']) {
            throw $this->c::issue('Arg `name` is required.');
        }
        $cfg['var_name'] = $cfg['name']; // Internal copy.
        $cfg['id']       = $this->elementId($cfg['var_name']);
        $cfg['name']     = $this->elementName($cfg['var_name']);
        $cfg['class']    = $this->c::mbTrim($cfg['class'].' '.$this->elementClass($cfg['var_name']));

        $cfg['tip']  = $cfg['tip'] ? $this->s::menuPageTip($cfg['tip']) : '';
        $cfg['note'] = $cfg['note'] ? $this->s::menuPageNote($cfg['note']) : '';

        $markup = '<tr'.($cfg['if'] ? ' data-if="'.esc_attr($cfg['if']).'"' : '').'>';

        if ($cfg['label']) {
            $markup .= '<th scope="row">';
            $markup .= '<div>'; // For positioning.
            $markup .= $cfg['tip'];
            $markup .= '<label for="'.esc_attr($cfg['id']).'" title="'.esc_attr($cfg['label']).'">'.
                            $cfg['label'].
                       '</label>';
            $markup .= '</div>';
            $markup .= '</th>';
        }
        $markup .= '<td'.(!$cfg['label'] ? ' colspan="2"' : '').'>';
        $markup .= '<div>'; // For positioning.
        $markup .= '<textarea'.
                        ' name="'.esc_attr($cfg['name']).'" id="'.esc_attr($cfg['id']).'"'.
                        ' class="'.esc_attr($cfg['class']).'" style="'.esc_attr($cfg['style']).'"'.
                        ' placeholder="'.esc_attr($cfg['placeholder']).'" autocomplete="new-password"'.
                        ($cfg['attrs'] ? ' '.$cfg['attrs'] : '').
                        '>'.esc_textarea($cfg['value']).'</textarea>';
        $markup .= $cfg['note'];
        $markup .= '</div>';
        $markup .= '</td>';

        $markup .= '</tr>';

        return $markup;
    }

    /**
     * Creates a select row.
     *
     * @since 160709 Menu page utils.
     *
     * @param array $args Configuration args.
     *
     * @return string Raw HTML markup.
     */
    public function selectRow(array $args = [])
    {
        $default_args = [
            // Required.
            'label' => '',

            // Suggested.
            'tip' => '',

            // Required.
            'name'    => '',
            'value'   => '',
            'options' => [], // Or string.
            // A string is taken as `<option>` tags.

            // Optional.
            'multiple' => false,

            // Optional.
            'placeholder' => '',
            'note'        => '',

            // Optional.
            'class' => '',
            'style' => '',
            'attrs' => '',

            // Optional.
            'if' => '',
        ];
        if (!array_key_exists('value', $args)) {
            throw $this->c::issue('Missing `value` key.');
        }
        $cfg = array_merge($default_args, $args);
        $cfg = array_intersect_key($cfg, $default_args);

        foreach ($cfg as $_key => &$_value) {
            if ($_key === 'value' && (bool) $cfg['multiple']) {
                $_value = array_map('strval', (array) $_value);
            } elseif ($_key === 'value' && is_bool($_value)) {
                $_value = (string) (int) $_value;
            } elseif ($_key === 'options' && is_array($_value)) {
                $_value = array_map('strval', $_value);
            } elseif ($_key === 'multiple') {
                $_value = (bool) $_value;
            } else { // Everything else.
                $_value = (string) $_value;
            }
        } // Unset `$_value` by reference.
        unset($_key, $_value); // Housekeeping.

        if (!$cfg['name']) {
            throw $this->c::issue('Arg `name` is required.');
        }
        if (is_array($cfg['options'])) {
            $_options       = $cfg['options'];
            $cfg['options'] = ''; // Initialize.
            $_in_optgroup   = false; // Initialize.

            foreach ($_options as $_value => $_label) {
                $_value = (string) $_value;
                $_label = (string) $_label;

                if (mb_strpos($_value, '_group:') === 0) {
                    if ($_in_optgroup) {
                        $cfg['options'] .= '</optgroup>';
                    }
                    $cfg['options'] .= '<optgroup label="'.esc_attr($_label).'">';
                    $_in_optgroup = true; // Flag as true.
                } else {
                    if (is_array($cfg['value'])) {
                        $_selected = in_array($_value, $cfg['value'], true) ? ' selected' : '';
                    } else {
                        $_selected = $_value === $cfg['value'] ? ' selected' : '';
                    }
                    $cfg['options'] .= '<option value="'.esc_attr($_value).'"'.$_selected.'>'.esc_html($_label).'</option>';
                }
            }
            $cfg['options'] .= $_in_optgroup ? '</optgroup>' : ''; // Close last group.
            // unset($_value, $_label, $_selected, $_in_optgroup, $_options); // Housekeeping.
        }
        $cfg['var_name'] = $cfg['name']; // Internal copy.
        $cfg['id']       = $this->elementId($cfg['var_name']);
        $cfg['name']     = $this->elementName($cfg['var_name'].($cfg['multiple'] ? '[]' : ''));
        $cfg['class']    = $this->c::mbTrim($cfg['class'].' '.$this->elementClass($cfg['var_name']));

        if ($cfg['multiple'] && (!$cfg['tip'] || mb_strpos($cfg['tip'], 'Ctrl key') === false)) {
            $cfg['tip'] = __('Use Ctrl key (or ⌘) to select multiple items from the list.', 'wp-sharks-core').
                            ($cfg['tip'] ? '<hr />' : '').$cfg['tip'];
        }
        $cfg['tip']  = $cfg['tip'] ? $this->s::menuPageTip($cfg['tip']) : '';
        $cfg['note'] = $cfg['note'] ? $this->s::menuPageNote($cfg['note']) : '';

        $markup = '<tr'.($cfg['if'] ? ' data-if="'.esc_attr($cfg['if']).'"' : '').'>';

        if ($cfg['label']) {
            $markup .= '<th scope="row">';
            $markup .= '<div>'; // For positioning.
            $markup .= $cfg['tip'];
            $markup .= '<label for="'.esc_attr($cfg['id']).'" title="'.esc_attr($cfg['label']).'">'.
                            $cfg['label'].
                       '</label>';
            $markup .= '</div>';
            $markup .= '</th>';
        }
        $markup .= '<td'.(!$cfg['label'] ? ' colspan="2"' : '').'>';
        $markup .= '<div>'; // For positioning.
        $markup .= '<select'.
                        ' name="'.esc_attr($cfg['name']).'" id="'.esc_attr($cfg['id']).'"'.
                        ' class="'.esc_attr($cfg['class']).'" style="'.esc_attr($cfg['style']).'"'.
                        ' placeholder="'.esc_attr($cfg['placeholder']).'" autocomplete="new-password"'.
                        ($cfg['multiple'] ? ' multiple' : '').
                        ($cfg['attrs'] ? ' '.$cfg['attrs'] : '').
                        '>'.$cfg['options'].'</select>';
        $markup .= $cfg['note'];

        if ($cfg['multiple']) { // Flag used to detect a case where nothing is selected.
            // NOTE: Browsers will not submit an empty array. If nothing selected, nothing submitted.
            // With this flag, something will always be submitted, because we're hard-coding an array element.
            $markup .= '<input type="hidden" name="'.esc_attr(mb_substr($cfg['name'], 0, -2).'[___ignore]').'" />';
        }
        $markup .= '</div>';
        $markup .= '</td>';

        $markup .= '</tr>';

        return $markup;
    }

    /**
     * Close `</table>` tag.
     *
     * @since 160709 Menu page utils.
     *
     * @return string Raw HTML markup.
     */
    public function closeTable()
    {
        return '</tbody></table>';
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
        $markup = '<p class="-submit submit">';
        $markup .= '<button class="button button-primary" type="submit">';
        $markup .= $label ?: __('Save Changes', 'wp-sharks-core');
        $markup .= '</button>';
        $markup .= '</p>';

        return $markup;
    }

    /**
     * Close `</form>` tag.
     *
     * @since 160709 Menu page utils.
     *
     * @return string Raw HTML markup.
     */
    public function closeTag()
    {
        return '</form>';
    }

    /**
     * Form element ID.
     *
     * @since 160723 Menu page utils.
     *
     * @param string $var_name Var name.
     *
     * @return string Form element ID.
     */
    protected function elementId(string $var_name = ''): string
    {
        if ($this->action) {
            return $this->s::restActionFormElementId($this->action, $var_name);
        } elseif ($this->cfg->Widget) {
            return $this->cfg->Widget->get_field_id($var_name);
        }
        return $this->cfg->slug.($var_name ? '-'.$this->c::nameToSlug($var_name) : '');
    }

    /**
     * Form element class.
     *
     * @since 160723 Menu page utils.
     *
     * @param string $var_name Var name.
     *
     * @return string Form element class.
     */
    protected function elementClass(string $var_name = ''): string
    {
        if ($this->action) {
            return $this->s::restActionFormElementClass($var_name);
        } elseif ($this->cfg->Widget) {
            return $this->cfg->Widget->get_field_id($var_name);
        }
        return $this->cfg->slug.($var_name ? '-'.$this->c::nameToSlug($var_name) : '');
    }

    /**
     * Form element name.
     *
     * @since 160723 Menu page utils.
     *
     * @param string $var_name Var name.
     *
     * @return string Form element name.
     */
    protected function elementName(string $var_name = ''): string
    {
        if ($this->action) {
            return $this->s::restActionFormElementName($var_name);
        } elseif ($this->cfg->Widget) {
            return $this->cfg->Widget->get_field_name($var_name);
        }
        $parts = $var_name // This is optional.
            ? preg_split('/(\[[^[\]]*\])/u', $var_name, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY)
            : []; // An empty value could occur either way.

        if (!$parts) {
            return $this->c::slugToVar($this->cfg->slug);
        } elseif (mb_strpos($parts[0], '[') === 0) {
            return $this->c::slugToVar($this->cfg->slug).implode($parts);
        }
        return $this->c::slugToVar($this->cfg->slug).'['.$parts[0].']'.implode(array_slice($parts, 1));
    }
}
