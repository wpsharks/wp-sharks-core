<?php
/**
 * WC order item utils.
 *
 * @author @jaswrks
 * @copyright WebSharks™
 */
declare(strict_types=1);
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
 * WC order item utils.
 *
 * @since 160608 WC order item utils.
 */
class WcOrderItem extends Classes\SCore\Base\Core
{
    /**
     * Get order containing item.
     *
     * @since 160608 Order item utilities.
     *
     * @param string|int $item_id Order item ID.
     *
     * @return \WC_Abstract_Order|null Order on success.
     */
    public function orderByItemId($item_id)
    {
        if (!($item_id = (int) $item_id)) {
            return null; // Not possible.
        }
        $WpDb  = $this->s::wpDb(); // DB instance.
        $table = $WpDb->prefix.'woocommerce_order_items';

        $sql = /* Get the order ID for this item. */ '
            SELECT `order_id` FROM `'.esc_sql($table).'`
             WHERE `order_item_id` = %s LIMIT 1';
        $sql = $WpDb->prepare($sql, $item_id); // Prepare.

        if (!($order_id = (int) $WpDb->get_var($sql))) {
            return null; // Not possible; can't get order ID.
        } elseif (!($post_type = get_post_type($order_id))) {
            debug(0, $this->c::issue(vars(), 'Unable to acquire order post type.'));
            return null; // Not possible; can't get post type.
        }
        switch ($post_type) { // Based on post type.
            case 'shop_subscription':
                $subscription_id = $order_id; // It's a subscription ID.
                if (($WC_Subscription = wcs_get_subscription($subscription_id))) {
                    return $WC_Subscription;
                }
                return null; // Not possible.

            case 'shop_order':
            default: // Any other order type.
                if (($WC_Order = wc_get_order($order_id))) {
                    return $WC_Order;
                }
                return null; // Not possible.
        }
    }

    /**
     * Get order item by ID.
     *
     * @since 160608 Order item utilities.
     *
     * @param string|int              $item_id  Order item ID.
     * @param \WC_Abstract_Order|null $WC_Order The order if already known.
     *
     * @return \WC_Order_Item|array An order item, else empty array.
     */
    public function orderItemById($item_id, \WC_Abstract_Order $WC_Order = null)
    {
        if (!($item_id = (int) $item_id)) {
            return []; // Not possible.
        } elseif (!($WC_Order = $WC_Order ?: $this->orderByItemId($item_id))) {
            return []; // Not possible.
        }
        foreach ($WC_Order->get_items() as $_item_id => $_WC_Order_Item) {
            if ($_item_id === $item_id) {
                // WC 3.0+ uses `\WC_Order_Item`.
                // <https://docs.woocommerce.com/wc-apidocs/class-WC_Abstract_Order.html>
                // <https://docs.woocommerce.com/wc-apidocs/class-WC_Order_Item.html>
                return $_WC_Order_Item; // `\WC_Order_Item|array`.
            }
        } // unset($_item_id, $_WC_Order_Item); // Housekeeping.

        return []; // Failure.
    }

    /**
     * Get product ID from item.
     *
     * @since 160608 Order item utilities.
     * @since 17xxxx Accept `\WC_Order_Item`.
     *
     * @param \WC_Order_Item|array $WC_Order_Item Order item.
     *
     * @return int Product ID from item.
     */
    public function productIdFromItem($WC_Order_Item): int
    {
        // WC 3.0+ uses `\WC_Order_Item_Product` extending `\WC_Order_Item`.
        // <https://docs.woocommerce.com/wc-apidocs/class-WC_Order_Item_Product.html>
        // <https://docs.woocommerce.com/wc-apidocs/class-WC_Order_Item.html>

        if ($WC_Order_Item instanceof \WC_Order_Item_Product) {
            if (($variation_id = $WC_Order_Item->get_variation_id())) {
                return (int) $variation_id;
            }
            return (int) $WC_Order_Item->get_product_id();
            //
        } elseif ($WC_Order_Item instanceof \WC_Order_Item) {
            return 0; // Not a product item.
            //
        } else { // Back compat.
            $item = (array) $WC_Order_Item;

            if (!empty($item['variation_id'])) {
                return (int) $item['variation_id'];
            }
            return (int) ($item['product_id'] ?? 0);
        }
    }

    /**
     * Get product by order item ID.
     *
     * @since 160608 Order item utilities.
     *
     * @param string|int              $item_id  Order item ID.
     * @param \WC_Abstract_Order|null $WC_Order The order if already known.
     *
     * @return \WC_Product|null A product object instance, else `null`.
     */
    public function productByOrderItemId($item_id, \WC_Abstract_Order $WC_Order = null)
    {
        // WC 3.0+ uses `\WC_Order_Item_Product` extending `\WC_Order_Item`.
        // <https://docs.woocommerce.com/wc-apidocs/class-WC_Order_Item_Product.html>
        // <https://docs.woocommerce.com/wc-apidocs/class-WC_Order_Item.html>

        if (!($item_id = (int) $item_id)) {
            return null; // Not possible.
        } elseif (!($WC_Order = $WC_Order ?: $this->orderByItemId($item_id))) {
            return null; // Not possible.
        }
        foreach ($WC_Order->get_items() as $_item_id => $_WC_Order_Item) {
            if ($_item_id === $item_id) {
                if ($_WC_Order_Item instanceof \WC_Order_Item_Product) {
                    $WC_Product = $_WC_Order_Item->get_product();
                    return $WC_Product instanceof \WC_Product && $WC_Product->exists() ? $WC_Product : null;
                    //
                } elseif ($_WC_Order_Item instanceof \WC_Order_Item) {
                    return null; // Not a product item.
                    //
                } else { // Back compat.
                    $_item      = (array) $_WC_Order_Item;
                    $WC_Product = $WC_Order->get_product_from_item($_item);
                    return $WC_Product instanceof \WC_Product && $WC_Product->exists() ? $WC_Product : null;
                }
            }
        } // unset($_item_id, $_WC_Order_Item, $_item); // Housekeeping.

        return null; // Failure.
    }
}
