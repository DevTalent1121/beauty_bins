<?php







/**







 * @version		$Id: helper.php 19594 2010-11-20 05:06:08Z ian $







 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.







 * @license		GNU General Public License version 2 or later; see LICENSE.txt







 */















// no direct access







defined('_JEXEC') or die;















/**







 * @package		Joomla.Site







 * @subpackage	mod_menu







 * @since		1.5







 */







class modMenuHelper2







{







	/**







	 * Get a list of the menu items.







	 *







	 * @param	JRegistry	$params	The module options.







	 *







	 * @return	array







	 * @since	1.5







	 */







	static function getList(&$params)







	{







		// Initialise variables.







		$list		= array();







		$db			= JFactory::getDbo();







		$user		= JFactory::getUser();







		$app		= JFactory::getApplication();







		$menu		= $app->getMenu();















		// If no active menu, use default







		$active = ($menu->getActive()) ? $menu->getActive() : $menu->getDefault();















		$path		= $active->tree;



		



		



		 







		$start		= (int) $params->get('startLevel');







		$end		= (int) $params->get('endLevel');







		$showAll	= $params->get('showAllChildren');







		$maxdepth	= $params->get('maxdepth');







		$items 		= $menu->getItems('menutype',$params->get('menutype'));



		



		











		$lastitem	= 0;















		if ($items) {



			$childsTobeRemoved = array();



			foreach($items as $i => $item)







			{



				if(!$item->params->get('menu_show',1) || in_array($item->parent_id,$childsTobeRemoved)){

					$childsTobeRemoved[] = $item->id;

					unset($items[$i]);

					continue;

				}



				 if (($start && $start > $item->level)







					|| ($end && $item->level > $end)







					|| (!$showAll && $item->level > 1 && !in_array($item->parent_id, $path))







					|| ($maxdepth && $item->level > $maxdepth)







				) {







					unset($items[$i]);







					continue;







				}



				



				else{



						if($item->level > 1){



							$path[] = $item->id;	



						}



				} 







				







				$item->deeper = false;







				$item->shallower = false;







				$item->level_diff = 0;















				if (isset($items[$lastitem])) {







					$items[$lastitem]->deeper		= ($item->level > $items[$lastitem]->level);







					$items[$lastitem]->shallower	= ($item->level < $items[$lastitem]->level);







					$items[$lastitem]->level_diff	= ($items[$lastitem]->level - $item->level);







					







					 







				}















				$lastitem			= $i;







				$item->active		= false;







				$item->flink = $item->link;















				/*switch ($item->type)
				{
					case 'separator':
						// No further action needed.
						continue;
					case 'url':
						if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false)) {
							// If this is an internal Joomla link, ensure the Itemid is set.
							$item->flink = $item->link.'&Itemid='.$item->id;
						}
						break;
					case 'alias':
						// If this is an alias use the item id stored in the parameters to make the link.
						$item->flink = 'index.php?Itemid='.$item->params->get('aliasoptions');
						break;
					default:
						$router = JFactory::getApplication()->getRouter();
						//var_dump($router);exit;
						if ($router->getMode() == JROUTER_MODE_SEF) {
							$item->flink = 'index.php?Itemid='.$item->id;
						}
						else {
							$item->flink .= '&Itemid='.$item->id;
						}
						break;
				}*/
				switch ($item->type)
				{
					case 'separator':
						break;

					case 'heading':
						// No further action needed.
						break;

					case 'url':
						if ((strpos($item->link, 'index.php?') === 0) && (strpos($item->link, 'Itemid=') === false))
						{
							// If this is an internal Joomla link, ensure the Itemid is set.
							$item->flink = $item->link . '&Itemid=' . $item->id;
						}
						break;

					case 'alias':
						$item->flink = 'index.php?Itemid=' . $item->params->get('aliasoptions');
						break;

					default:
						$item->flink = 'index.php?Itemid=' . $item->id;
						break;
				}















				if (strcasecmp(substr($item->flink, 0, 4), 'http') && (strpos($item->flink, 'index.php?') !== false)) {







					$item->flink = JRoute::_($item->flink, true, $item->params->get('secure'));







				}







				else {







					$item->flink = JRoute::_($item->flink);







				}







			}















			if (isset($items[$lastitem])) {







				$items[$lastitem]->deeper		= (($start?$start:1) > $items[$lastitem]->level);







				$items[$lastitem]->shallower	= (($start?$start:1) < $items[$lastitem]->level);







				$items[$lastitem]->level_diff	= ($items[$lastitem]->level - ($start?$start:1));







			}







		}







		



		



		/*echo "<pre>";



		print_r($items);



		exit;*/



		







		return $items;







	}







}







