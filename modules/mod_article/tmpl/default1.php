<?php
   /**
      * @package Joomla.Site
      * @subpackage mod_firstmodule
      * @copyright Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
      * @license GNU General Public License version 2 or later; see LICENSE.txt
   */
defined('_JEXEC') or die;
//die("good");
//echo $row->title;
?>

<h3 class='title'><?php echo $categoryName;
//print_r($article); ?>
</h3>
<?php if (!empty($article)) : ?>
	<ul class="latestusers<?php echo $moduleclass_sfx; ?> mod-list" >
	<?php foreach ($article  as $key => $value) : ?>
		<?php foreach ($value  as $key1 => $value1) : ?>
			<?php if ($value1!='*') : ?>
				<li>
					
					<img src="https://localhost/news_cms/media/mod_languages/images/<?php echo $value1;  ?>.gif">   <?php echo $key1;  ?>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>