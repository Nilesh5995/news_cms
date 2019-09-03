<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<h3 class='title'><?php echo $categoryName;?></h3>
<?php
if (!empty($article))
{
		?>
		<ul class="latestusers<?php echo $moduleclass_sfx; ?> mod-list" >
			<?php
			foreach ($article as $key => $value)
			{
				?>
				<?php
				foreach ($value  as $key1 => $value1)
				{
				?>
					<?php
					if ($value1 != '*')
					{
						?>
						<li>
							<?php echo $key1;  ?> <img src="https://localhost/news_cms/media/mod_languages/images/
							<?php echo $value1;  ?>.gif">


						</li>
					<?php
					}
				}
			}
				?>
		</ul>
	<?php
}




