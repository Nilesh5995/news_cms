<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
require_once dirname(__FILE__) . '/helper.php';
$JInput = JFactory::getApplication()->input;
$articleId = $JInput->get('id', '', 'int');
$categoryName = ModArticleHelper::getCategoryName($articleId);
$count = 5;
$article = ModArticleHelper::ShowTitle($count);
require JModuleHelper::getLayoutPath('mod_article', $params->get('position', 'left'));
$input = JFactory::getApplication()->input;
