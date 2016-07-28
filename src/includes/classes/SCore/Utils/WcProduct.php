<?php
/**
 * WC product utils.
 *
 * @author @jaswsinc
 * @copyright WebSharks™
 */
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
 * WC product utils.
 *
 * @since 160727 WC product utils.
 */
class WcProduct extends Classes\SCore\Base\Core
{
    /**
     * Product ID by slug.
     *
     * @since 160727 WC product utils.
     *
     * @param string $slug     Product slug.
     * @param bool   $no_cache Bypass cache check?
     *
     * @return int Product ID.
     */
    public function idBySlug(string $slug, bool $no_cache = false): int
    {
        static $product_ids;

        if (!($slug = (string) $slug)) {
            return 0; // Not possible.
        }
        if (isset($product_ids[$slug])) {
            return $product_ids[$slug];
        }
        $WpDb = $this->s::wpDb(); // DB instance.

        $sql = /* Get the product ID for this slug. */ '
            SELECT `ID` FROM `'.esc_sql($WpDb->posts).'`
                WHERE
                    `post_type` IN(\'product\', \'product_variation\')
                    AND `post_name` = %s
            LIMIT 1';
        $sql = $WpDb->prepare($sql, $slug); // Prepare.

        if (($product_id = (int) $WpDb->get_var($sql))) {
            return $product_ids[$slug] = $product_id;
        }
        return $product_ids[$slug] = 0; // Not possible.
    }
}
