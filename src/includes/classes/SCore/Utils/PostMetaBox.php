<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

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
 * Post meta utils.
 *
 * @since 160723 Post meta utils.
 */
class PostMetaBox extends Classes\SCore\Base\Core
{
    /**
     * Adds a post meta box.
     *
     * @since 160723 Post meta utils.
     *
     * @param array $args Configuration args.
     */
    public function add(array $args)
    {
        # Establish args.

        $default_args = [
            'auto_prefix'        => true,
            'include_post_types' => [],
            'exclude_post_types' => [],
            'slug'               => '',
            'title'              => '',
            'class'              => '',
            'template_file'      => '',
            'template_dir'       => '',
            'callback'           => null,
            'screen'             => null,
            'context'            => 'advanced',
            'priority'           => 'default',
        ];
        $cfg = (object) array_merge($default_args, $args);

        $cfg->auto_prefix        = (bool) $cfg->auto_prefix;
        $cfg->include_post_types = (array) $cfg->include_post_types;
        $cfg->exclude_post_types = (array) $cfg->exclude_post_types;
        $cfg->slug               = (string) $cfg->slug;
        $cfg->title              = (string) $cfg->title;
        $cfg->class              = (string) $cfg->class;
        $cfg->template_file      = (string) $cfg->template_file;
        $cfg->template_dir       = (string) $cfg->template_dir;
        $cfg->context            = (string) $cfg->context;
        $cfg->priority           = (string) $cfg->priority;

        if ($cfg->slug && $cfg->auto_prefix) {
            $cfg->slug = $this->App->Config->©brand['©slug'].'-'.$cfg->slug;
        } elseif (!$cfg->slug) {
            $cfg->slug = $this->App->Config->©brand['©slug'];
        }
        if (!$cfg->title) {
            $cfg->title = $this->App->Config->©brand['©name'];
        }
        $cfg->class .= $cfg->class ? ' ' : '';
        $cfg->class .= $this->App::CORE_CONTAINER_SLUG.'-menu-page-area';
        $cfg->class .= ' '.$this->App::CORE_CONTAINER_SLUG.'-post-meta-box-wrapper';
        $cfg->class .= ' '.$this->App->Config->©brand['©slug'].'-post-meta-box-wrapper';
        $cfg->class .= $cfg->slug !== $this->App->Config->©brand['©slug'] ? ' '.$cfg->slug.'-post-meta-box-wrapper' : '';

        $cfg->nonce['action'] = $cfg->slug.'-save'; // Nonce action ID.
        $cfg->nonce['name']   = '_'.$this->c::slugToVar($cfg->slug).'_nonce';

        $cfg = $this->s::applyFilters('post_meta_box', $cfg, $args, $default_args);

        if (!$cfg->template_file) {
            throw $this->c::issue('Missing template file.');
        }
        $cfg->callback = $cfg->callback ?: function (\WP_Post $WP_Post) use ($cfg) {
            $post_id = (int) $WP_Post->ID;
            $vars    = compact('post_id', 'WP_Post', 'cfg');
            wp_nonce_field($cfg->nonce['action'], $cfg->nonce['name']);
            echo $this->c::getTemplate('s-core/admin/menu-pages/post-meta-box/template.php')->parse($vars);
        };
        # Build meta box sub-routines via closures.

        $on_load_maybe_do_setup = function () use ($cfg) {
            $current_post_type = $this->s::currentMenuPagePostType();

            if ($cfg->include_post_types && !in_array($current_post_type, $cfg->include_post_types, true)) {
                return; // Does not apply to this post type.
            } elseif ($cfg->exclude_post_types && in_array($current_post_type, $cfg->exclude_post_types, true)) {
                return; // Does not apply to this post type.
            }
            $on_add_meta_boxes = function () use ($cfg) {
                add_meta_box($cfg->slug, $cfg->title, $cfg->callback, $cfg->screen, $cfg->context, $cfg->priority);
            };
            $on_save_post = function ($post_id, \WP_Post $WP_Post) use ($cfg) {
                if (!($post_id = (int) $post_id)) {
                    return; // Not possible.
                }
                $_r = $this->c::unslash($_REQUEST);
                $_r = $this->c::mbTrim($_r);

                $nonce_action = $cfg->nonce['action'];
                $nonce_value  = $_r[$cfg->nonce['name']] ?? null;

                $data = $_r[$this->c::slugToVar($cfg->slug)] ?? [];
                $data = is_array($data) ? $data : []; // Force array.
                $data = $this->c::removeKey($data, '___ignore');

                if (!isset($nonce_value)) {
                    return; // Nonce is missing.
                } elseif (!wp_verify_nonce($nonce_value, $nonce_action)) {
                    return; // Nonce is unverifiable.
                } elseif (!current_user_can('edit_post', $post_id)) {
                    return; // Current user not allowed.
                } elseif (wp_is_post_autosave($post_id)) {
                    return; // Not applicable.
                } elseif (wp_is_post_revision($post_id)) {
                    return; // Not applicable.
                }
                foreach ($data as $_key => $_value) {
                    if ($_key && is_string($_key)) {
                        $this->s::updatePostMeta($post_id, $_key, $_value);
                    }
                }
            };
            add_action('add_meta_boxes', $on_add_meta_boxes);
            add_action('save_post', $on_save_post, 10, 2);
        };
        add_action('load-post.php', $on_load_maybe_do_setup);
        add_action('load-post-new.php', $on_load_maybe_do_setup);
    }

    /**
     * A post meta box form class instance.
     *
     * @since 160723 Post meta utils.
     *
     * @param string $slug Post meta box slug.
     * @param array  $args Any additional behavioral args.
     *
     * @return Classes\SCore\PostMetaBoxForm Class instance.
     */
    public function form(string $slug, array $args = []): Classes\SCore\PostMetaBoxForm
    {
        return $this->App->Di->get(Classes\SCore\PostMetaBoxForm::class, compact('slug', 'args'));
    }
}
