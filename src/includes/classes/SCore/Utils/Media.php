<?php
/**
 * Media utils.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
//
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
//
use WebSharks\Core\WpSharksCore\Classes\Core\Error;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
//
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Media utils.
 *
 * @since 17xxxx Media utils.
 */
class Media extends Classes\SCore\Base\Core
{
    /**
     * Download/save attachment.
     *
     * @since 17xxxx Initial release.
     *
     * @param string $url     File to download (URL).
     * @param int    $post_id Post ID to attach it to.
     *
     * @return int|Error Attachment ID, else error.
     */
    public function addAttachmentFromUrl(string $url, int $post_id)
    {
        require_once ABSPATH.'wp-admin/includes/file.php';
        require_once ABSPATH.'wp-admin/includes/media.php';
        require_once ABSPATH.'wp-admin/includes/image.php';

        if (!$url || !$post_id) { // Must have.
            return $this->c::error('bad_request', __('Bad request.', 'wp-sharks-core'));
        }
        $mime = wp_check_filetype(basename($url));

        if (empty($mime['type']) || empty($mime['ext'])) {
            return $this->c::error('unknown_mime_type', __('Unknown MIME type.', 'wp-sharks-core'));
            //
        } elseif (is_wp_error($tmp_name = download_url($url))) {
            return $this->s::wpErrorConvert($tmp_name);
        }
        if (!preg_match('/\.'.$this->c::escRegex($mime['ext']).'$/ui', $tmp_name)) {
            rename($tmp_name, $tmp_name.'.'.$mime['ext']);
        }
        $file = [
            'tmp_name' => $tmp_name,
            'type'     => $mime['type'],
            'name'     => $this->c::uniqueId('', false).'.'.$mime['ext'],
        ];
        if (is_wp_error($attachment_id = media_handle_sideload($file, $post_id))) {
            @unlink($tmp_name); // Ditch temporary file.
            return $this->s::wpErrorConvert($attachment_id);
        }
        return $attachment_id;
    }
}
