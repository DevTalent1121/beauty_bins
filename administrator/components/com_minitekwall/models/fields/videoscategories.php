<?php
/**
* @title				Minitek Wall
* @copyright   	Copyright (C) 2011-2020 Minitek, All rights reserved.
* @license   		GNU General Public License version 3 or later.
* @author url   https://www.minitek.gr/
* @developers   Minitek.gr
*/

defined('_JEXEC') or die;

class JFormFieldVideosCategories extends JFormField
{
  function getInput()
  {
    $document = JFactory::getDocument();
    JHtml::_('behavior.framework');

    $db = JFactory::getDBO();
    $query = 'SELECT v.* FROM #__community_videos_category v WHERE published=1 ORDER BY parent, id';
    $db->setQuery($query);
    $vitems = $db->loadObjectList();
    $children = array();
    if ($vitems)
    {
      foreach ($vitems as $v)
      {
        $v->title = $v->name;
        $v->parent_id = $v->parent;

        $pt = $v->parent;
        $list = @$children[$pt] ? $children[$pt] : array();
        array_push($list, $v);
        $children[$pt] = $list;
      }
    }
    $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
    $vitems = array();

    foreach ($list as $item)
    {
      $item->treename = JString::str_ireplace('&#160;', '- ', $item->treename);
      $vitems[] = JHTML::_('select.option', $item->id, $item->treename);
    }

    $output = JHTML::_('select.genericlist', $vitems, $this->name, 'class="inputbox" multiple="true" size="10"', 'value', 'text', $this->value, $this->id);

    return $output;
  }
}