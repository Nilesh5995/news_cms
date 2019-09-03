<?php
defined('_JEXEC') or die;
require_once dirname(__FILE__) . '/helper.php';
$article=ModArticleHelper::ShowTitle($params);
require JModuleHelper::getLayoutPath('mod_article');
?>