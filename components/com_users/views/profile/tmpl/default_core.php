<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>
<fieldset id="users-profile-core">
	<legend>
		<?php echo JText::_('COM_USERS_PROFILE_CORE_LEGEND'); ?>
	</legend>
	<dl class="dl-horizontal">
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?>
		</dt>
		<dd>
			<?php echo $this->escape($this->data->name); ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_USERNAME_LABEL'); ?>
		</dt>
		<dd>
			<?php echo $this->escape($this->data->username); ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_PHONE_LABEL'); ?>
		</dt>
		<dd>
			<?php echo $this->escape($this->data->phone); ?>
		</dd>
		<dt>
			<?php echo JText::_('COM_USERS_PROFILE_REGISTERED_DATE_LABEL'); ?>
		</dt>
		<dd>
			<?php echo JHtml::_('date', $this->data->registerDate, JText::_('DATE_FORMAT_LC1')); ?>
		</dd>
		<dt>
			<?php 
			// echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); 
			echo JText::_('COM_USERS_PROFILE_NEXT_RENEWAL_DATE_LABEL');
			?>
		</dt>
		<?php
		// var_dump($this->db);
		$id = $this->data->id;
		$db = JFactory::getDBO();
		$query1 = $db->getQuery(true);
        $query1->select('*');
        $query1->from('#__j2store_subscriptions');
        $query1->where('user_id = '.$id);
        $db->setQuery($query1);
        $result = $db->loadObject();
		if(isset($result->next_payment_on)){
			echo "<dd>";
			echo JHtml::_('date', $result->next_payment_on, JText::_('DATE_FORMAT_LC1'));
			echo "</dd>";
		}
		else{
			echo "<dd>You don't have any renewals</dd>";			
		}
 
		// if ($this->data->lastvisitDate != $this->db->getNullDate()) : ?>
			<!-- <dd> -->
				<?php // echo JHtml::_('date', $this->data->lastvisitDate, JText::_('DATE_FORMAT_LC1')); ?>
			<!-- </dd> -->
		<?php// else : ?>
			<!-- <dd> -->
				<?php // echo JText::_('COM_USERS_PROFILE_NEVER_VISITED'); ?>
			<!-- </dd> -->
		<?php // endif; ?>
		<dt>
			<?php 
			// echo JText::_('COM_USERS_PROFILE_LAST_VISITED_DATE_LABEL'); 
			echo JText::_('COM_USERS_PROFILE_ADDRESS_LABEL');
			$query_address = $db->getQuery(true);
			$query_address->select('*');
			$query_address->from('#__j2store_addresses');
			$query_address->where('user_id = '.$id);
			$db->setQuery($query_address);
			$result_address = $db->loadObject();
			$address_text = "";
			if(isset($result_address->address_1)){
				$address_text .= $result_address->address_1;
			}
			if(isset($result_address->city)){
				$address_text .= " , ".$result_address->city;
			}
			
			echo "<dd>".$address_text."</dd>";			
	
			?>
		</dt>
	</dl>
</fieldset>
