<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
/**
	* Helper for mod_articles_category
	*
	* @since  1.6
	*/
class ModArticleHelper
{
	/**
	 * Get a list of articles from a specific category
	 *
	 * @param   \Joomla\Registry\Registry  $count  object holding the models parameters
	 *
	 * @return  mixed
	 *
	 * @since  1.6
	 */

	public static function ShowTitle($count)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*')
		->from('#__content')
		->order('id DESC');
		$db->setQuery($query, 0, $count);
		$rows = $db->loadObjectList();
		$arr = [];

		foreach ( $rows as $row)

			{
				$lang = (explode("-", $row->language));

				$arr[] = array("$row->title" => $lang[0]);
		}

		return (array) $arr;
	}

	/**
	 * Method to show the subtype of the article
	 *
	 * The goal is to show the categories of the article
	 *
	 * @param   int  $articleId  the id of the article
	 *
	 * @return  object  The categories of the article
	 *
	 * @since   1.6
	 *
	 */
	public static function  getCategoryName($articleId)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('c.title');
		$query->from('#__categories AS c');
		$query->join("INNER", "#__content AS a ON c.id = a.catid");
		$query->where("a.id = '$articleId'");
		$db->setQuery($query);
		$row = $db->loadObject();

			return $row->title;
	}
}
