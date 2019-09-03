<?php
/**
 * @version    SVN: <svn_id>
 * @package    JMailAlerts
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

jimport('joomla.installer.installer');
jimport('joomla.filesystem.file');

class Com_JmailalertsInstallerScript
{
	/** @var array The list of extra modules and plugins to install */
	private $installation_queue = array(
		/*plugins => { (folder) => { (element) => (published) }}*/
		'plugins' => array(
			'system' => array(
				'tjassetsloader'=>1,
				'tjupdates' => 1
			),
			'user'   => array(
				'plug_usr_mailalert' => 1,
			)
		),
	);

	/** @var array Obsolete files and folders to remove*/
	private $removeFilesAndFolders = array(
		'files' => array(
			/*Since v2.6.0*/
			'administrator/components/com_jmailalerts/log.txt'
		),
		'folders' => array(
		)
	);

	public function install($parent)
	{
		// $parent is the class calling this method
		$this->comInstall($parent);
	}

	public function comInstall($parent)
	{
		$this->runSQL($parent, 'frequencies.mysql.utf8.sql');
	}

	public function runSQL($parent, $sqlfile)
	{
		$db = JFactory::getDBO();

		// Obviously you may have to change the path and name if your installation SQL file ;)
		if (method_exists($parent, 'extension_root'))
		{
			$sqlfile = $parent->getPath('extension_root') . '/administrator/sql/' . $sqlfile;
		}
		else
		{
			$sqlfile = $parent->getParent()->getPath('extension_root') . '/sql/' . $sqlfile;
		}

		// Don't modify below this line
		$buffer = file_get_contents($sqlfile);

		if ($buffer !== false)
		{
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);

			if (count($queries) != 0)
			{
				foreach ($queries as $query)
				{
					$query = trim($query);

					if ($query != '' && $query{0} != '#')
					{
						$db->setQuery($query);

						if (!$db->execute())
						{
							JError::raiseWarning(1, JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)));

							return false;
						}
					}
				}
			}
		}
	}

	public function preflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
	}

	public function postflight($type, $parent)
	{
		// Remove obsolete files and folders
		$removeFilesAndFolders = $this->removeFilesAndFolders;
		$this->_removeObsoleteFilesAndFolders($removeFilesAndFolders);

		// $parent is the class calling this method
		// Install subextensions
		$status = $this->_installSubextensions($parent);

		// Install Techjoomla Straper
		$straperStatus = $this->_installStraper($parent);

		// Show the post-installation page
		$this->_renderPostInstallation($status, $straperStatus, $parent);
	}

	private function _installStraper($parent)
	{
		$src = $parent->getParent()->getPath('source');

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		jimport('joomla.utilities.date');
		$source = $src . '/tj_strapper';
		$target = JPATH_ROOT . '/media/techjoomla_strapper';

		$haveToInstallStraper = false;

		if (!JFolder::exists($target))
		{
			$haveToInstallStraper = true;
		}
		else
		{
			$straperVersion = array();

			if (JFile::exists($target . '/version.txt'))
			{
				$rawData                     = JFile::read($target . '/version.txt');
				$info                        = explode("\n", $rawData);
				$straperVersion['installed'] = array(
					'version' => trim($info[0]),
					'date'    => new JDate(trim($info[1])),
				);
			}
			else
			{
				$straperVersion['installed'] = array(
					'version' => '0.0',
					'date'    => new JDate('2011-01-01'),
				);
			}

			$rawData                   = JFile::read($source . '/version.txt');
			$info                      = explode("\n", $rawData);
			$straperVersion['package'] = array(
				'version' => trim($info[0]),
				'date'    => new JDate(trim($info[1])),
			);

			$haveToInstallStraper = $straperVersion['package']['date']->toUNIX() > $straperVersion['installed']['date']->toUNIX();
		}

		$installedStraper = false;

		if ($haveToInstallStraper)
		{
			$versionSource    = 'package';
			$installer        = new JInstaller;
			$installedStraper = $installer->install($source);
		}
		else
		{
			$versionSource = 'installed';
		}

		if (!isset($straperVersion))
		{
			$straperVersion = array();

			if (JFile::exists($target . '/version.txt'))
			{
				$rawData                     = JFile::read($target . '/version.txt');
				$info                        = explode("\n", $rawData);
				$straperVersion['installed'] = array(
					'version' => trim($info[0]),
					'date'    => new JDate(trim($info[1])),
				);
			}
			else
			{
				$straperVersion['installed'] = array(
					'version' => '0.0',
					'date'    => new JDate('2011-01-01'),
				);
			}

			$rawData                   = JFile::read($source . '/version.txt');
			$info                      = explode("\n", $rawData);
			$straperVersion['package'] = array(
				'version' => trim($info[0]),
				'date'    => new JDate(trim($info[1])),
			);
			$versionSource = 'installed';
		}

		if (!($straperVersion[$versionSource]['date'] instanceof JDate))
		{
			$straperVersion[$versionSource]['date'] = new JDate;
		}

		return array(
			'required'  => $haveToInstallStraper,
			'installed' => $installedStraper,
			'version'   => $straperVersion[$versionSource]['version'],
			'date'      => $straperVersion[$versionSource]['date']->format('Y-m-d'),
		);
	}

	private function _installSubextensions($parent)
	{
		$src             = $parent->getParent()->getPath('source');
		$db              = JFactory::getDbo();
		$status          = new JObject;
		$status->plugins = array();

		// Plugins installation
		if (count($this->installation_queue['plugins']))
		{
			foreach ($this->installation_queue['plugins'] as $folder => $plugins)
			{
				if (count($plugins))
				{
					foreach ($plugins as $plugin => $published)
					{
						$path = "$src/plugins/$folder/$plugin";

						if (!is_dir($path))
						{
							$path = "$src/plugins/$folder/plg_$plugin";
						}

						if (!is_dir($path))
						{
							$path = "$src/plugins/$plugin";
						}

						if (!is_dir($path))
						{
							$path = "$src/plugins/plg_$plugin";
						}

						if (!is_dir($path))
						{
							continue;
						}

						// Was the plugin already installed?
						$query = $db->getQuery(true)
									->select('COUNT(*)')
									->from($db->qn('#__extensions'))
									->where($db->qn('element') . ' = ' . $db->q($plugin))
									->where($db->qn('folder') . ' = ' . $db->q($folder));
						$db->setQuery($query);
						$count = $db->loadResult();

						$installer = new JInstaller;
						$result    = $installer->install($path);

						$status->plugins[] = array('name' => $plugin, 'group' => $folder, 'result' => $result);

						if ($published && !$count)
						{
							$query = $db->getQuery(true)
										->update($db->qn('#__extensions'))
										->set($db->qn('enabled') . ' = ' . $db->q('1'))
										->where($db->qn('element') . ' = ' . $db->q($plugin))
										->where($db->qn('folder') . ' = ' . $db->q($folder));
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
			}

			return $status;
		}
	}

	/**
	 * Removes obsolete files and folders
	 *
	 * @param array $removeFilesAndFolders
	 */
	private function _removeObsoleteFilesAndFolders($removeFilesAndFolders)
	{
		// Remove files

		jimport('joomla.filesystem.file');

		if (!empty($removeFilesAndFolders['files']))
		{
			foreach ($removeFilesAndFolders['files'] as $file)
			{
				$f = JPATH_ROOT . '/' . $file;

				if (!JFile::exists($f))
				{
					continue;
				}
				else
				{
					JFile::delete($f);
				}
			}
		}

		// Remove folders
		jimport('joomla.filesystem.file');

		if (!empty($removeFilesAndFolders['folders']))
		{
			foreach ($removeFilesAndFolders['folders'] as $folder)
			{
				$f = JPATH_ROOT . '/' . $folder;

				if (!JFolder::exists($f))
				{
					continue;
				}
				else
				{
					JFolder::delete($f);
				}
			}
		}
	}

	private function _renderPostInstallation($status, $straperStatus, $parent)
	{
		$rows = 1;
		?>

		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th class="title" colspan="2">Extension</th>
					<th width="30%">Status</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row1">
					<td class="key" colspan="2">JMailAlerts component</td>
					<td><strong style="color: green">Installed</strong></td>
				</tr>
				<tr class="row0">
					<td class="key" colspan="2">
						<strong>TechJoomla Strapper <?php echo $straperStatus['version']?></strong> [<?php echo $straperStatus['date']?>]
					</td>
					<td><strong>
						<span style="color: <?php echo $straperStatus['required'] ? ($straperStatus['installed'] ? 'green' : 'red'):'#660'?>; font-weight: bold;">
							<?php echo $straperStatus['required'] ? ($straperStatus['installed'] ? 'Installed' : 'Not Installed'):'Already up-to-date';?>
						</span>
					</strong></td>
				</tr>

				<?php
				if (isset($status->modules))
				{
					if (count($status->modules)): ?>
						<tr>
							<th>Module</th>
							<th>Client</th>
							<th></th>
						</tr>
						<?php foreach ($status->modules as $module): ?>
							<tr class="row<?php echo ($rows++ % 2);?>">
								<td class="key"><?php echo $module['name'];?></td>
								<td class="key"><?php echo ucfirst($module['client']);?></td>
								<td><strong style="color: <?php echo ($module['result']) ? "green" : "red"?>"><?php echo ($module['result']) ? 'Installed' : 'Not installed';?></strong></td>
							</tr>
						<?php endforeach;?>
					<?php endif;
				}

				if (isset($status->plugins))
				{
					if (count($status->plugins)): ?>
						<tr>
							<th>Plugin</th>
							<th>Group</th>
							<th></th>
						</tr>
						<?php foreach ($status->plugins as $plugin): ?>
							<tr class="row<?php echo ($rows++ % 2);?>">
								<td class="key"><?php echo ucfirst($plugin['name']);?></td>
								<td class="key"><?php echo ucfirst($plugin['group']);?></td>
								<td><strong style="color: <?php echo ($plugin['result']) ? "green" : "red"?>"><?php echo ($plugin['result']) ? 'Installed' : 'Not installed';?></strong></td>
							</tr>
						<?php endforeach;?>
					<?php endif;
				}
				?>
			</tbody>
		</table>
		<?php
	}
}
