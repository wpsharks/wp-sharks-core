<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore;

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
 * Menu page form.
 *
 * @since 160709 Menu page markup utils.
 */
class MenuPageForm extends Classes\SCore\Base\Core
{
    /**
     * ReST action identifier.
     *
     * @since 160709 Menu page markup utils.
     *
     * @type string ReST action identifier.
     */
    public $action;

    /**
     * Class constructor.
     *
     * @since 160709 Menu page markup utils.
     *
     * @param Classes\App $App    Instance.
     * @param string      $action ReST action identifier.
     */
    public function __construct(Classes\App $App, string $action)
    {
        parent::__construct($App);

        if (!($this->action = $action)) {
            throw $this->c::issue('Missing action.');
        }
    }

    /**
     * Open `<form>` tag for action.
     *
     * @since 160709 Menu page markup utils.
     *
     * @return string Raw HTML markup.
     */
    public function openTag()
    {
        return '<form'.
               ' method="post"'.
               ' action="'.esc_url($this->s::restActionUrl($this->action)).'"'.
               ' enctype="multipart/form-data"'.
               ' accept-charset="utf-8"'.
               ' autocomplete="off"'.
               '>';
    }

    /**
     * Open `<table>` tag.
     *
     * @since 160709 Menu page markup utils.
     *
     * @param string $heading     Optional heading.
     * @param string $description Optional description.
     *
     * @return string Raw HTML markup.
     */
    public function openTable(string $heading = '', string $description = '')
    {
        $markup = $heading ? '<h2>'.$heading.'</h2>' : '';
        $markup .= $description ? '<p>'.$description.'</p>' : '';

        $markup .= '<table class="-form-table form-table">';
        $markup .=     '<tbody>';

        return $markup;
    }

    /**
     * Creates an input row.
     *
     * @since 160709 Menu page markup utils.
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
        ];
        if (!array_key_exists('value', $args)) {
            throw $this->c::issue('Missing `value` key.');
        }
        $cfg = array_merge($default_args, $args);
        $cfg = array_intersect_key($cfg, $default_args);
        $cfg = array_map('strval', $cfg);

        if (!$cfg['label'] || !$cfg['name']) {
            throw $this->c::issue('`label` and `name` required.');
        } elseif (in_array($cfg['type'], ['checkbox', 'radio', 'reset', 'button', 'image'], true)) {
            throw $this->c::issue(sprintf('Input `type="%1$s"` unsupported at this time.', $cfg['type']));
        }
        $cfg['var_name'] = $cfg['name']; // Internal copy.
        $cfg['name']     = $this->s::restActionFormElementName($cfg['var_name']);
        $cfg['id']       = $this->s::restActionFormElementId($this->action, $cfg['var_name']);
        $cfg['class']    = $this->c::mbTrim($cfg['class'].' '.$this->s::restActionFormElementClass($cfg['var_name']));

        $cfg['tip']  = $cfg['tip'] ? $this->s::menuPageTip($cfg['tip']) : '';
        $cfg['note'] = $cfg['note'] ? $this->s::menuPageNote($cfg['note']) : '';

        $markup = '<tr valign="top">';

        $markup .=     '<th scope="row">';
        $markup .=         $cfg['tip']; // Floats right.
        $markup .=         '<label for="'.esc_attr($cfg['id']).'">'.
                                $cfg['label'].
                           '</label>';
        $markup .=     '</th>';

        $markup .=     '<td>';
        $markup .=         '<input'.
                           ' type="'.esc_attr($cfg['type']).'"'.
                           ' name="'.esc_attr($cfg['name']).'" id="'.esc_attr($cfg['id']).'"'.
                           ' class="'.esc_attr($cfg['class']).'" style="'.esc_attr($cfg['style']).'"'.
                           ' placeholder="'.esc_attr($cfg['placeholder']).'" autocomplete="new-password"'.
                           ' value="'.($cfg['type'] === 'url' ? esc_url($cfg['value']) : esc_attr($cfg['value'])).'"'.
                           ($cfg['attrs'] ? ' '.$cfg['attrs'] : '').
                           ' />';
        $markup .=         $cfg['note'];
        $markup .=     '</td>';

        $markup .= '</tr>';

        return $markup;
    }

    /**
     * Creates a textarea row.
     *
     * @since 160709 Menu page markup utils.
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
        ];
        if (!array_key_exists('value', $args)) {
            throw $this->c::issue('Missing `value` key.');
        }
        $cfg = array_merge($default_args, $args);
        $cfg = array_intersect_key($cfg, $default_args);
        $cfg = array_map('strval', $cfg);

        if (!$cfg['label'] || !$cfg['name']) {
            throw $this->c::issue('`label` and `name` required.');
        }
        $cfg['var_name'] = $cfg['name']; // Internal copy.
        $cfg['name']     = $this->s::restActionFormElementName($cfg['var_name']);
        $cfg['id']       = $this->s::restActionFormElementId($this->action, $cfg['var_name']);
        $cfg['class']    = $this->c::mbTrim($cfg['class'].' '.$this->s::restActionFormElementClass($cfg['var_name']));

        $cfg['tip']  = $cfg['tip'] ? $this->s::menuPageTip($cfg['tip']) : '';
        $cfg['note'] = $cfg['note'] ? $this->s::menuPageNote($cfg['note']) : '';

        $markup = '<tr valign="top">';

        $markup .=     '<th scope="row">';
        $markup .=         $cfg['tip']; // Floats right.
        $markup .=         '<label for="'.esc_attr($cfg['id']).'">'.
                                $cfg['label'].
                           '</label>';
        $markup .=     '</th>';

        $markup .=     '<td>';
        $markup .=         '<textarea'.
                           ' name="'.esc_attr($cfg['name']).'" id="'.esc_attr($cfg['id']).'"'.
                           ' class="'.esc_attr($cfg['class']).'" style="'.esc_attr($cfg['style']).'"'.
                           ' placeholder="'.esc_attr($cfg['placeholder']).'" autocomplete="new-password"'.
                           ($cfg['attrs'] ? ' '.$cfg['attrs'] : '').
                           '>'.esc_textarea($cfg['value']).'</textarea>';
        $markup .=         $cfg['note'];
        $markup .=     '</td>';

        $markup .= '</tr>';

        return $markup;
    }

    /**
     * Creates a select row.
     *
     * @since 160709 Menu page markup utils.
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
        ];
        if (!array_key_exists('value', $args)) {
            throw $this->c::issue('Missing `value` key.');
        }
        $cfg = array_merge($default_args, $args);
        $cfg = array_intersect_key($cfg, $default_args);

        foreach ($cfg as $_key => &$_value) {
            if ($_key === 'value' && (bool) $cfg['multiple']) {
                $_value = array_map('strval', (array) $_value);
            } elseif ($_key === 'options' && is_array($_value)) {
                $_value = array_map('strval', $_value);
            } elseif ($_key === 'multiple') {
                $_value = (bool) $_value;
            } else { // Everything else.
                $_value = (string) $_value;
            }
        } // unset($_key, $_value); // Housekeeping.

        if (!$cfg['label'] || !$cfg['name']) {
            throw $this->c::issue('`label` and `name` required.');
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
        $cfg['id']       = $this->s::restActionFormElementId($this->action, $cfg['var_name']);
        $cfg['name']     = $this->s::restActionFormElementName($cfg['var_name'].($cfg['multiple'] ? '[]' : ''));
        $cfg['class']    = $this->c::mbTrim($cfg['class'].' '.$this->s::restActionFormElementClass($cfg['var_name']));

        $cfg['tip']  = $cfg['tip'] ? $this->s::menuPageTip($cfg['tip']) : '';
        $cfg['note'] = $cfg['note'] ? $this->s::menuPageNote($cfg['note']) : '';

        $markup = '<tr valign="top">';

        $markup .=     '<th scope="row">';
        $markup .=         $cfg['tip']; // Floats right.
        $markup .=         '<label for="'.esc_attr($cfg['id']).'">'.
                                $cfg['label'].
                           '</label>';
        $markup .=     '</th>';

        $markup .=     '<td>';
        $markup .=         '<select'.
                           ' name="'.esc_attr($cfg['name']).'" id="'.esc_attr($cfg['id']).'"'.
                           ' class="'.esc_attr($cfg['class']).'" style="'.esc_attr($cfg['style']).'"'.
                           ' placeholder="'.esc_attr($cfg['placeholder']).'" autocomplete="new-password"'.
                           ($cfg['multiple'] ? ' multiple' : '').
                           ($cfg['attrs'] ? ' '.$cfg['attrs'] : '').
                           '>'.$cfg['options'].'</select>';
        $markup .=         $cfg['note'];

        if ($cfg['multiple']) { // Flag used to detect a case where nothing is selected.
            // NOTE: Browsers will not submit an empty array. If nothing selected, nothing submitted.
            // With this flag, something will always be submitted, because we're hard-coding an array element.
            $markup .= '<input type="hidden" name="'.esc_attr(mb_substr($cfg['name'], 0, -2).'[___ignore]').'" />';
        }
        $markup .=     '</td>';

        $markup .= '</tr>';

        return $markup;
    }

    /**
     * Close `</table>` tag.
     *
     * @since 160709 Menu page markup utils.
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
     * @since 160709 Menu page markup utils.
     *
     * @param string $label Optional label.
     *
     * @return string Raw HTML markup.
     */
    public function submitButton(string $label = '')
    {
        $markup = '<p class="-submit submit">';
        $markup .=     '<button class="button button-primary" type="submit">';
        $markup .=         $label ?: __('Save Changes', 'wp-sharks-core');
        $markup .=     '</button>';
        $markup .= '</p>';

        return $markup;
    }

    /**
     * Close `</form>` tag.
     *
     * @since 160709 Menu page markup utils.
     *
     * @return string Raw HTML markup.
     */
    public function closeTag()
    {
        return '</form>';
    }
}
