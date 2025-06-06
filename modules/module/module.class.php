<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */
/**
 * @class  module
 * @author NAVER (developers@xpressengine.com)
 * @brief high class of the module module
 */
class Module extends ModuleObject
{
	/**
	 * @brief Implement if additional tasks are necessary when installing
	 */
	function moduleInstall()
	{
		// Insert new domain
		if(!getModel('module')->getDefaultDomainInfo())
		{
			$current_url = Rhymix\Framework\URL::getCurrentUrl();
			$current_port = intval(parse_url($current_url, PHP_URL_PORT)) ?: null;
			$domain = new stdClass();
			$domain->domain_srl = 0;
			$domain->domain = Rhymix\Framework\URL::getDomainFromURL($current_url);
			$domain->is_default_domain = 'Y';
			$domain->index_module_srl = 0;
			$domain->index_document_srl = 0;
			$domain->http_port = RX_SSL ? null : $current_port;
			$domain->https_port = RX_SSL ? $current_port : null;
			$domain->security = config('url.ssl') ?: 'none';
			$domain->description = '';
			$domain->settings = json_encode(array(
				'language' => 'default',
				'timezone' => 'default',
			));
			$output = executeQuery('module.insertDomain', $domain);
			if (!$output->toBool())
			{
				return $output;
			}
		}

		// Create a directory to use in the module module
		FileHandler::makeDir('./files/cache/module_info');
		FileHandler::makeDir('./files/cache/triggers');
		FileHandler::makeDir('./files/ruleset');
	}

	/**
	 * @brief a method to check if successfully installed
	 */
	function checkUpdate()
	{
		$oDB = DB::getInstance();

		// check ruleset directory
		if(!is_dir(RX_BASEDIR . 'files/ruleset')) return true;

		// Check domains
		if (!$oDB->isTableExists('domains') || !getModel('module')->getDefaultDomainInfo())
		{
			return true;
		}

		// Remove site_srl column from tables
		if ($oDB->getColumnInfo('lang', 'site_srl')->default_value === null) return true;
		if ($oDB->isIndexExists('modules', 'idx_site_mid')) return true;
		if ($oDB->getColumnInfo('modules', 'site_srl')->default_value === null) return true;
		if ($oDB->getColumnInfo('module_config', 'site_srl')->default_value === null) return true;
		if ($oDB->isTableExists('site_admin') && !file_exists($this->module_path . 'schemas/site_admin.xml'))
		{
			return true;
		}

		// Add domain_srl column
		if (!$oDB->isColumnExists('modules', 'domain_srl')) return true;
		if (!$oDB->isIndexExists('modules', 'idx_domain_srl')) return true;
		if (!$oDB->isIndexExists('modules', 'unique_mid')) return true;
		if ($oDB->isIndexExists('modules', 'idx_mid')) return true;

		// check fix mskin
		if(!$oDB->isColumnExists("modules", "is_mskin_fix")) return true;

		// check unique index on module_part_config
		if($oDB->isIndexExists('module_part_config', 'idx_module_part_config')) return true;
		if(!$oDB->isIndexExists('module_part_config', 'unique_module_part_config')) return true;

		// check module_part_config data type
		$column_info = $oDB->getColumnInfo('module_part_config', 'config');
		if($column_info->xetype !== 'bigtext')
		{
			return true;
		}
		$column_info = $oDB->getColumnInfo('module_part_config', 'module');
		if($column_info->size > 80)
		{
			return true;
		}

		// check module_config data type
		$column_info = $oDB->getColumnInfo('module_config', 'config');
		if($column_info->xetype !== 'bigtext')
		{
			return true;
		}
		$column_info = $oDB->getColumnInfo('module_config', 'module');
		if($column_info->size > 80)
		{
			return true;
		}

		// check unique index on module_part_config
		if(!$oDB->isIndexExists('module_part_config', 'unique_module_part_config')) return true;

		// check route columns in action_forward table
		if(!$oDB->isColumnExists('action_forward', 'route_regexp')) return true;
		if(!$oDB->isColumnExists('action_forward', 'route_config')) return true;
		if(!$oDB->isColumnExists('action_forward', 'global_route')) return true;

		// check additional indexes on lang table
		$column_info = $oDB->getColumnInfo('lang', 'name');
		if($column_info->size > 100)
		{
			return true;
		}
		if(!$oDB->isIndexExists('lang', 'idx_name')) return true;
		if(!$oDB->isIndexExists('lang', 'idx_lang_code')) return true;
		if(!$oDB->isIndexExists('lang', 'idx_lang')) return true;

		// check module_trigger table
		if($oDB->isIndexExists('module_trigger', 'idx_trigger'))
		{
			return true;
		}
		if(!$oDB->isIndexExists('module_trigger', 'idx_trigger_name'))
		{
			return true;
		}
		$column_info = $oDB->getColumnInfo('module_trigger', 'type');
		if($column_info->size < 120)
		{
			return true;
		}

		// check deprecated lang code
		$output = executeQuery('module.getLangCount', ['lang_code' => 'jp']);
		if ($output->data->count > 0)
		{
			return true;
		}

		// check scope column on module_admins table
		if (!$oDB->isColumnExists('module_admins', 'scopes'))
		{
			return true;
		}
	}

	/**
	 * @brief Execute update
	 */
	function moduleUpdate()
	{
		$oDB = DB::getInstance();

		// Migrate domains
		if (!getModel('module')->getDefaultDomainInfo())
		{
			$this->migrateDomains();
		}

		// Set default value as 0 for site_srl columns that are no longer used.
		if ($oDB->getColumnInfo('lang', 'site_srl')->default_value === null)
		{
			$oDB->modifyColumn('lang', 'site_srl', 'number', null, 0, true, 'lang_code');
		}
		if ($oDB->isIndexExists('modules', 'idx_site_mid'))
		{
			$oDB->dropIndex('modules', 'idx_site_mid');
		}
		if ($oDB->getColumnInfo('modules', 'site_srl')->default_value === null)
		{
			$oDB->modifyColumn('modules', 'site_srl', 'number', null, 0, true, 'menu_srl');
		}
		if ($oDB->getColumnInfo('module_config', 'site_srl')->default_value === null)
		{
			$oDB->modifyColumn('module_config', 'site_srl', 'number', null, 0, true, 'module');
		}

		// Remove the site_admin table.
		if ($oDB->isTableExists('site_admin') && !file_exists($this->module_path . 'schemas/site_admin.xml'))
		{
			$oDB->dropTable('site_admin');
		}

		// Add domain_srl column to relevant tables.
		if (!$oDB->isColumnExists('modules', 'domain_srl'))
		{
			$oDB->addColumn('modules', 'domain_srl', 'number', null, -1, true, 'site_srl');
		}
		if (!$oDB->isIndexExists('modules', 'idx_domain_srl'))
		{
			$oDB->addIndex('modules', 'idx_domain_srl', array('domain_srl'));
		}

		// Try adding a unique index on mid, but fall back to a regular index if not possible.
		if (!$oDB->isIndexExists('modules', 'unique_mid'))
		{
			$output = $oDB->addIndex('modules', 'unique_mid', array('mid'), true);
			if (!$output->toBool())
			{
				return $output;
			}
		}
		if ($oDB->isIndexExists('modules', 'unique_mid') && $oDB->isIndexExists('modules', 'idx_mid'))
		{
			$oDB->dropIndex('modules', 'idx_mid');
		}

		// check ruleset directory
		FileHandler::makeDir(RX_BASEDIR . 'files/ruleset');

		// check module_config data type
		$column_info = $oDB->getColumnInfo('module_config', 'config');
		if($column_info->xetype !== 'bigtext')
		{
			$oDB->modifyColumn('module_config', 'config', 'bigtext');
		}
		$column_info = $oDB->getColumnInfo('module_config', 'module');
		if($column_info->size > 80)
		{
			$oDB->modifyColumn('module_config', 'module', 'varchar', 80, '', true);
		}

		// check module_part_config data type
		$column_info = $oDB->getColumnInfo('module_part_config', 'config');
		if($column_info->xetype !== 'bigtext')
		{
			$oDB->modifyColumn('module_part_config', 'config', 'bigtext');
		}
		$column_info = $oDB->getColumnInfo('module_part_config', 'module');
		if($column_info->size > 80)
		{
			$oDB->modifyColumn('module_part_config', 'module', 'varchar', 80, '', true);
		}

		// check unique index on module_part_config
		if($oDB->isIndexExists('module_part_config', 'idx_module_part_config'))
		{
			$oDB->dropIndex('module_part_config', 'idx_module_part_config');
		}
		if(!$oDB->isIndexExists('module_part_config', 'unique_module_part_config'))
		{
			$output = $oDB->addIndex('module_part_config', 'unique_module_part_config', array('module', 'module_srl'), true);
			if (!$output->toBool())
			{
				$oDB->addIndex('module_part_config', 'unique_module_part_config', array('module', 'module_srl'), false);
			}
		}

		// check route columns in action_forward table
		if(!$oDB->isColumnExists('action_forward', 'route_regexp'))
		{
			$oDB->addColumn('action_forward', 'route_regexp', 'text');
		}
		if(!$oDB->isColumnExists('action_forward', 'route_config'))
		{
			$oDB->addColumn('action_forward', 'route_config', 'text');
		}
		if(!$oDB->isColumnExists('action_forward', 'global_route'))
		{
			$oDB->addColumn('action_forward', 'global_route', 'char', 1, 'N', true);
		}

		// check additional indexes on lang table
		$column_info = $oDB->getColumnInfo('lang', 'name');
		if($column_info->size > 100)
		{
			$oDB->modifyColumn('lang', 'name', 'varchar', 100, null, true);
		}
		if(!$oDB->isIndexExists('lang', 'idx_name'))
		{
			$oDB->addIndex('lang', 'idx_name', array('name'), false);
		}
		if(!$oDB->isIndexExists('lang', 'idx_lang_code'))
		{
			$oDB->addIndex('lang', 'idx_lang_code', array('lang_code'), false);
		}
		if(!$oDB->isIndexExists('lang', 'idx_lang'))
		{
			$oDB->addIndex('lang', 'idx_lang', array('name', 'lang_code'), false);
		}

		// check module_trigger table
		if($oDB->isIndexExists('module_trigger', 'idx_trigger'))
		{
			$oDB->dropIndex('module_trigger', 'idx_trigger');
		}
		$column_info = $oDB->getColumnInfo('module_trigger', 'type');
		if($column_info->size < 120)
		{
			$oDB->modifyColumn('module_trigger', 'trigger_name', 'varchar', 80, null, true, null, null, 'latin1');
			$oDB->modifyColumn('module_trigger', 'called_position', 'varchar', 20, null, true, null, null, 'latin1');
			$oDB->modifyColumn('module_trigger', 'module', 'varchar', 80, null, true, null, null, 'latin1');
			$oDB->modifyColumn('module_trigger', 'type', 'varchar', 120, null, true, null, null, 'latin1');
			$oDB->modifyColumn('module_trigger', 'called_method', 'varchar', 80, null, true, null, null, 'latin1');
		}
		if(!$oDB->isIndexExists('module_trigger', 'idx_trigger_name'))
		{
			$oDB->addIndex('module_trigger', 'idx_trigger_name', array('trigger_name', 'called_position'));
			$oDB->addIndex('module_trigger', 'idx_trigger_target', array('module', 'type', 'called_method'));
		}

		// check deprecated lang code
		$output = executeQuery('module.getLangCount', ['lang_code' => 'jp']);
		if ($output->data->count > 0)
		{
			$output = executeQuery('module.updateLangCode', ['lang_code' => 'jp', 'new_lang_code' => 'ja']);
			if (!$output->toBool())
			{
				return $output;
			}
		}

		// check scope column on module_admins table
		if (!$oDB->isColumnExists('module_admins', 'scopes'))
		{
			$oDB->addColumn('module_admins', 'scopes', 'text', null, null, false, 'member_srl');
		}
	}

	/**
	 * @brief Migrate old sites and multidomain info to new 'domains' table
	 */
	function migrateDomains()
	{
		// Create the domains table.
		$oDB = DB::getInstance();
		if (!$oDB->isTableExists('domains'))
		{
			$oDB->createTable($this->module_path . 'schemas/domains.xml');
		}

		// Get current site configuration.
		$config = ModuleModel::getModuleConfig('module');

		// Initialize domains data.
		$domains = array();
		$default_domain = new stdClass;

		// Check XE sites.
		$output = executeQueryArray('module.getSites');
		if ($output->data)
		{
			foreach ($output->data as $site_info)
			{
				$site_domain = $site_info->domain;
				if (!preg_match('@^https?://@', $site_domain))
				{
					$site_domain = 'http://' . $site_domain;
				}

				$domain = new stdClass();
				$domain->domain_srl = $site_info->site_srl;
				$domain->domain = Rhymix\Framework\URL::getDomainFromURL(strtolower($site_domain));
				$domain->is_default_domain = $site_info->site_srl == 0 ? 'Y' : 'N';
				$domain->index_module_srl = $site_info->index_module_srl;
				$domain->index_document_srl = 0;
				$domain->http_port = config('url.http_port') ?: null;
				$domain->https_port = config('url.https_port') ?: null;
				$domain->security = config('url.ssl') ?: 'none';
				$domain->description = '';
				$domain->settings = json_encode(array(
					'title' => $config->siteTitle,
					'subtitle' => $config->siteSubtitle,
					'language' => 'default',
					'timezone' => 'default',
					'html_footer' => $config->htmlFooter,
				));
				$domain->regdate = $site_info->regdate;
				$domains[$domain->domain] = $domain;
				if ($domain->is_default_domain === 'Y')
				{
					$default_domain = $domain;
				}
			}
		}
		else
		{
			$output = executeQuery('module.getDefaultMidInfo', $args);
			$default_hostinfo = parse_url(Rhymix\Framework\URL::getCurrentURL());

			$domain = new stdClass();
			$domain->domain_srl = 0;
			$domain->domain = Rhymix\Framework\URL::decodeIdna(strtolower($default_hostinfo['host']));
			$domain->is_default_domain = 'Y';
			$domain->index_module_srl = $output->data ? $output->data->module_srl : 0;
			$domain->index_document_srl = 0;
			$domain->http_port = isset($default_hostinfo['port']) ? intval($default_hostinfo['port']) : null;
			$domain->https_port = null;
			$domain->security = config('url.ssl') ?: 'none';
			$domain->description = '';
			$domain->settings = json_encode(array(
				'title' => $config->siteTitle,
				'subtitle' => $config->siteSubtitle,
				'language' => 'default',
				'timezone' => 'default',
				'html_footer' => $config->htmlFooter,
			));
			$domains[$domain->domain] = $domain;
			$default_domain = $domain;
		}

		// Check multidomain module.
		if (getModel('multidomain'))
		{
			$output = executeQueryArray('multidomain.getMultidomainList', (object)array('order_type' => 'asc', 'list_count' => 100000000));
			if ($output->data)
			{
				foreach ($output->data as $site_info)
				{
					$site_domain = $site_info->domain;
					if (!preg_match('@^https?://@', $site_domain))
					{
						$site_domain = 'http://' . $site_domain;
					}

					$domain = new stdClass();
					$domain->domain_srl = $site_info->multidomain_srl;
					$domain->domain = Rhymix\Framework\URL::getDomainFromURL(strtolower($site_domain));
					$domain->is_default_domain = isset($domains[$domain->domain]) ? $domains[$domain->domain]->is_default_domain : 'N';
					$domain->index_module_srl = intval($site_info->module_srl);
					$domain->index_document_srl = intval($site_info->document_srl);
					$domain->http_port = config('url.http_port') ?: null;
					$domain->https_port = config('url.https_port') ?: null;
					$domain->security = config('url.ssl') ?: 'none';
					$domain->description = '';
					$domain->settings = json_encode(array(
						'title' => $config->siteTitle,
						'subtitle' => $config->siteSubtitle,
						'language' => 'default',
						'timezone' => 'default',
						'html_footer' => $config->htmlFooter,
					));
					$domain->regdate = $site_info->regdate;
					$domains[$domain->domain] = $domain;
					if ($domain->is_default_domain === 'Y')
					{
						$default_domain = $domain;
					}
				}
			}
		}

		// Insert into DB.
		foreach ($domains as $domain)
		{
			$output = executeQuery('module.insertDomain', $domain);
			if (!$output->toBool())
			{
				return $output;
			}
		}

		// Clear cache.
		Rhymix\Framework\Cache::clearGroup('site_and_module');
		ModuleModel::$_mid_map = ModuleModel::$_module_srl_map = [];

		// Return the default domain info.
		return $default_domain;
	}

	/**
	 * @brief Re-generate the cache file
	 */
	function recompileCache()
	{
		$oModuleModel = getModel('module');
		$module_list = ModuleModel::getModuleList();
		$module_names = array_map(function($module_info) {
			return $module_info->module;
		}, $module_list);

		// Delete triggers belonging to modules that don't exist
		$args = new stdClass;
		$args->module = $module_names ?: [];
		executeQuery('module.deleteTriggers', $args);
		Rhymix\Framework\Cache::delete('triggers');
	}
}
/* End of file module.class.php */
/* Location: ./modules/module/module.class.php */
