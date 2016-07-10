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
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);
        $args = array_map('strval', $args);

        if (!$args['label'] || !$args['name']) {
            throw $this->c::issue('`label` and `name` required.');
        } elseif (in_array($args['type'], ['checkbox', 'radio', 'reset', 'button', 'image'], true)) {
            throw $this->c::issue(sprintf('Input `type="%1$s"` unsupported at this time.', $args['type']));
        }
        $args['var'] = $args['name']; // Internal copy.

        $args['name']  = $this->s::restActionFormElementName($args['var']);
        $args['id']    = $this->s::restActionFormElementId($this->action, $args['var']);
        $args['class'] = $this->c::mbTrim($args['class'].' '.$this->s::restActionFormElementClass($args['var']));

        $args['tip']  = $args['tip'] ? $this->s::menuPageTip($args['tip']) : '';
        $args['note'] = $args['note'] ? $this->s::menuPageNote($args['note']) : '';

        $markup = '<tr valign="top">';

        $markup .=     '<th scope="row">';
        $markup .=         '<label for="'.esc_attr($args['id']).'">'.
                                $args['label'].
                            '</label>';
        $markup .=         $args['tip'];
        $markup .=     '</th>';

        $markup .=     '<td>';
        $markup .=         '<input'.
                           ' type="'.esc_attr($args['type']).'"'.
                           ' name="'.esc_attr($args['name']).'" id="'.esc_attr($args['id']).'"'.
                           ' class="'.esc_attr($args['class']).'" style="'.esc_attr($args['style']).'"'.
                           ' placeholder="'.esc_attr($args['placeholder']).'" autocomplete="new-password"'.
                           ' value="'.($args['type'] === 'url' ? esc_url($args['value']) : esc_attr($args['value'])).'"'.
                           ($args['attrs'] ? ' '.$args['attrs'] : '').
                           ' />';
        $markup .=         $args['note'];
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
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);
        $args = array_map('strval', $args);

        if (!$args['label'] || !$args['name']) {
            throw $this->c::issue('`label` and `name` required.');
        }
        $args['var'] = $args['name']; // Internal copy.

        $args['name']  = $this->s::restActionFormElementName($args['var']);
        $args['id']    = $this->s::restActionFormElementId($this->action, $args['var']);
        $args['class'] = $this->c::mbTrim($args['class'].' '.$this->s::restActionFormElementClass($args['var']));

        $args['tip']  = $args['tip'] ? $this->s::menuPageTip($args['tip']) : '';
        $args['note'] = $args['note'] ? $this->s::menuPageNote($args['note']) : '';

        $markup = '<tr valign="top">';

        $markup .=     '<th scope="row">';
        $markup .=         '<label for="'.esc_attr($args['id']).'">'.
                                $args['label'].
                            '</label>';
        $markup .=         $args['tip'];
        $markup .=     '</th>';

        $markup .=     '<td>';
        $markup .=         '<textarea'.
                           ' name="'.esc_attr($args['name']).'" id="'.esc_attr($args['id']).'"'.
                           ' class="'.esc_attr($args['class']).'" style="'.esc_attr($args['style']).'"'.
                           ' placeholder="'.esc_attr($args['placeholder']).'" autocomplete="new-password"'.
                           ($args['attrs'] ? ' '.$args['attrs'] : '').
                           '>'.esc_textarea($args['value']).'</textarea>';
        $markup .=         $args['note'];
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
        $args = array_merge($default_args, $args);
        $args = array_intersect_key($args, $default_args);

        if (is_array($args['options'])) {
            $_options        = $args['options'];
            $args['options'] = ''; // Initialize.
            $_in_optgroup    = false; // Initialize.

            foreach ($_options as $_value => $_label) {
                $_value = (string) $_value;
                $_label = (string) $_label;

                if (mb_strpos($_value, '_group:') === 0) {
                    if ($_in_optgroup) {
                        $args['options'] .= '</optgroup>';
                    }
                    $args['options'] .= '<optgroup label="'.esc_attr($_label).'">';
                    $_in_optgroup = true; // Flag as true.
                } else {
                    $_selected = $_value === (string) $args['value'] ? ' selected' : '';
                    $args['options'] .= '<option value="'.esc_attr($_value).'"'.$_selected.'>'.esc_html($_label).'</option>';
                }
            }
            $args['options'] .= $_in_optgroup ? '</optgroup>' : ''; // Close last group.
            // unset($_value, $_label, $_selected, $_in_optgroup, $_options); // Housekeeping.
        }
        $args = array_map('strval', $args); // Now force all args to strings.

        if (!$args['label'] || !$args['name'] || !$args['options']) {
            throw $this->c::issue('`label`, `name`, and `options` required.');
        }
        $args['var'] = $args['name']; // Internal copy.

        $args['name']  = $this->s::restActionFormElementName($args['var']);
        $args['id']    = $this->s::restActionFormElementId($this->action, $args['var']);
        $args['class'] = $this->c::mbTrim($args['class'].' '.$this->s::restActionFormElementClass($args['var']));

        $args['tip']  = $args['tip'] ? $this->s::menuPageTip($args['tip']) : '';
        $args['note'] = $args['note'] ? $this->s::menuPageNote($args['note']) : '';

        $markup = '<tr valign="top">';

        $markup .=     '<th scope="row">';
        $markup .=         '<label for="'.esc_attr($args['id']).'">'.
                                $args['label'].
                            '</label>';
        $markup .=         $args['tip'];
        $markup .=     '</th>';

        $markup .=     '<td>';
        $markup .=         '<select'.
                           ' name="'.esc_attr($args['name']).'" id="'.esc_attr($args['id']).'"'.
                           ' class="'.esc_attr($args['class']).'" style="'.esc_attr($args['style']).'"'.
                           ' placeholder="'.esc_attr($args['placeholder']).'" autocomplete="new-password"'.
                           ($args['attrs'] ? ' '.$args['attrs'] : '').
                           '>'.$args['options'].'</select>';
        $markup .=         $args['note'];
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
        $markup .=         $label ?: __('Save Changes');
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
