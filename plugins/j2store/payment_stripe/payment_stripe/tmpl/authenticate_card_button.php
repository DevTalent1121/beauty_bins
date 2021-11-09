<?php
/**
 * --------------------------------------------------------------------------------
 * Payment Plugin - Stripe
 * --------------------------------------------------------------------------------
 * @package     Joomla 2.5 -  3.x
 * @subpackage  J2 Store
 * @author      Alagesan, J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2014-19 J2Store . All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://j2store.org
 * --------------------------------------------------------------------------------
 *
 * */

//no direct access
defined('_JEXEC') or die('Restricted access');

$authenticate_url = JURI::root().'index.php?option=com_j2store&view=app&task=view&appTask=authenticateSubscriptionPayment&id='.$vars->app_id.'&order_id='.$vars->order->order_id;
?>
<a class="btn btn-primary subscription_order_auth_btn" target="_blank" href="<?php echo $authenticate_url; ?>">
    <?php echo JText::_('J2STORE_STRIPE_AUTHENTICATE_PAYMENT_BTN_TEXT'); ?>
</a>
