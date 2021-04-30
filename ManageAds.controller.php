<?php

/**
 * @package SimpleAds
 *
 * @author [SiNaN]
 * @copyright 2008-2021 by: [SiNaN] (sinan@simplemachines.org)
 * @license BSD 3-clause
 *
 * @version 1.0.3
 */


/**
 * The admin ad controller
 */
class ManageAds_Controller extends Action_Controller
{
	protected $fields = array();
	protected $values = array();

	/**
	 * Entry point, determines what action will be called based on the subaction
	 */
	public function action_index()
	{
		global $context, $txt;

		require_once(SUBSDIR . '/AdsAdmin.subs.php');

		// Template, css, language, javascript
		loadTemplate('ManageAds');
		loadCSSFile('manageads.css');
		loadLanguage('ManageAds');
		loadJavascriptFile('manageads.js', array('defer' => true));

		// Define our known actions
		$subActions = array(
			'ads' => array($this, 'action_list_ads'),
			'addad' => array($this, 'action_edit_ad'),
			'editad' => array($this, 'action_edit_ad'),
			'positions' => array($this, 'action_list_positions'),
			'addposition' => array($this, 'action_edit_position'),
			'editposition' => array($this, 'action_edit_position'),
		);

		// Start up the controller
		$action = new Action();

		// Admin tabs
		$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' => $txt['sa_admin_title'],
			'help' => '',
			'description' => $txt['sa_admin_desc'],
			'tabs' => array(
				'ads' => array(),
				'addad' => array(
					'label' => $txt['sa_ads_add_title'],
					'disabled' => !empty($_REQUEST['ad']),
				),
				'editad' => array(
					'label' => $txt['sa_ads_edit_title'],
					'disabled' => empty($_REQUEST['ad']),
				),
				'positions' => array(),
				'addposition' => array(
					'label' => $txt['sa_positions_add_title'],
					'disabled' => !empty($_REQUEST['position']),
				),
				'editposition' => array(
					'label' => $txt['sa_positions_edit_title'],
					'disabled' => empty($_REQUEST['position']),
				),
			),
		);

		// Set the default to the list tab
		$subAction = $action->initialize($subActions, 'ads');

		// Right then, off you go
		$context[$context['admin_menu_name']]['current_subsection'] = $subAction;
		$action->dispatch($subAction);
	}

	/**
	 * Show the list of ads in the system
	 * Provide buttons to edit/delete/status
	 */
	public function action_list_ads()
	{
		global $context, $scripturl, $txt;

		// Removing one of the ads
		if (!empty($_REQUEST['delete']))
		{
			checkSession('get');
			delete_ad((int) $_REQUEST['delete']);
			redirectexit('action=admin;area=ads;sa=ads');
		}

		// Changing the status active<->disabled
		if (!empty($_REQUEST['status']))
		{
			checkSession('request');

			list ($ad_id, $status) = set_status_ad((int) $_REQUEST['status']);

			// Doing this the ajax way?
			if (isset($_REQUEST['xml']))
			{
				$context['item_id'] = $ad_id;
				$context['status'] = $status ? 'disabled' : 'active';

				// Clear out any template layers, add the xml response
				$template_layers = Template_Layers::getInstance();
				$template_layers->removeAll();
				$context['sub_template'] = 'sa_change_status';

				obExit();
			}
			else
			{
				redirectexit('action=admin;area=ads;sa=ads');
			}
		}

		// Generate the list add control for create list to use
		$list_options = array(
			'id' => 'list_ads',
			'title' => $txt['sa_ads_list_title'],
			'items_per_page' => 30,
			'base_href' => $scripturl . '?action=admin;area=ads;sa=ads',
			'default_sort_col' => 'name',
			'get_items' => array(
				'function' => array($this, 'list_get_ads_data'),
			),
			'get_count' => array(
				'function' => array($this, 'list_get_ads_count'),
			),
			'no_items_label' => $txt['sa_no_ads'],
			'columns' => array(
				'name' => array(
					'header' => array(
						'value' => $txt['sa_ads_name'],
					),
					'data' => array(
						'db' => 'name',
					),
					'sort' => array(
						'default' => 'name',
						'reverse' => 'name DESC',
					),
				),
				'clicks' => array(
					'header' => array(
						'value' => $txt['sa_ads_clicks'],
						'class' => 'centertext'
					),
					'data' => array(
						'db' => 'clicks',
						'class' => 'centertext'
					),
					'sort' => array(
						'default' => 'clicks',
						'reverse' => 'clicks DESC',
					),
				),
				'impressions' => array(
					'header' => array(
						'value' => $txt['sa_ads_impressions'],
						'class' => 'centertext'
					),
					'data' => array(
						'db' => 'impressions',
						'class' => 'centertext'
					),
					'sort' => array(
						'default' => 'impressions',
						'reverse' => 'impressions DESC',
					),
				),
				'expires' => array(
					'header' => array(
						'value' => $txt['sa_ads_expires'],
						'class' => 'centertext'
					),
					'data' => array(
						'db' => 'expires',
						'class' => 'centertext'
					),
				),
				'status' => array(
					'header' => array(
						'value' => $txt['sa_ads_status'],
						'class' => 'centertext'
					),
					'data' => array(
						'db' => 'status',
						'class' => 'centertext'
					),
					'sort' => array(
						'default' => 'expired',
						'reverse' => 'expired DESC',
					),
				),
				'actions' => array(
					'header' => array(
						'value' => $txt['sa_generic_actions'],
						'class' => 'centertext'
					),
					'data' => array(
						'function' => create_function('$row', '
							global $context, $scripturl;

							return \'<a href="\' . $scripturl . \'?action=admin;area=ads;sa=ads;status=\' . $row[\'id_ad\'] . \';\' . $context[\'session_var\'] . \'=\' . $context[\'session_id\'] . \'" onclick="sa_change_status(\' . $row[\'id_ad\'] . \', \\\'ad\\\', \\\'\' . $context[\'session_var\'] . \'\\\', \\\'\' . $context[\'session_id\'] . \'\\\'); return false;">\' . sa_embed_image($row[\'status_image\'], \'status_image_\' . $row[\'id_ad\']) . \'
							</a> <a href="\' . $scripturl . \'?action=admin;area=ads;sa=editad;ad=\' . $row[\'id_ad\'] . \'">\' . sa_embed_image(\'edit\') . \'</a> <a href="\' . $scripturl . \'?action=admin;area=ads;sa=ads;delete=\' . $row[\'id_ad\'] . \';\' . $context[\'session_var\'] . \'=\' . $context[\'session_id\'] . \'">\' . sa_embed_image(\'delete\') . \'</a>\';
						'),
						'class' => 'centertext nowrap'
					),
				),
			),
		);

		// Create the list
		require_once(SUBSDIR . '/GenericList.class.php');
		createList($list_options);

		$context['page_title'] = $txt['sa_ads_list_title'];
		$context['sub_template'] = 'list_ads';
	}

	/**
	 * Creates the add / edit add fields for use in the template
	 */
	public function action_edit_ad()
	{
		global $context, $txt;

		$ad_id = !empty($_REQUEST['ad']) ? (int) $_REQUEST['ad'] : 0;
		$context['is_new'] = empty($ad_id);

		// Saving the ad?
		if (!empty($_POST['submit']))
		{
			checkSession();

			// Our template field names
			$this->fields = array(
				'name' => 'text',
				'body' => 'text',
				'positions' => 'array_int',
				'default_display' => 'text',
				'custom_display' => 'text',
				'allowed_groups' => 'text',
				'denied_groups' => 'text',
				'duration' => 'int',
				'max_clicks' => 'int',
				'max_impressions' => 'int',
				'status' => 'int',
			);

			// Clean the values as defined by fields
			$this->_sanitizeValues();

			// Where we intend / can to show these
			$default_display = $this->_setDefaultDisplay();
			if (!empty($default_display))
			{
				$this->values['default_display'] = implode(',', $default_display);
			}

			// The groups that will see the ad
			$this->_setAccess();

			// Expiration date for the ad
			$this->_setExpiration();

			// Any reason not to continue?
			$this->_checkErrors();

			// First time for this ad, set the creation date/time
			if ($context['is_new'])
			{
				$this->fields['created'] = 'int';
				$this->values['created'] = time();
				add_ad_data($this->fields, $this->values);
			}
			// Editing an add, lets get additional data like clicks, impressions, etc
			else
			{
				$current_data = get_ad_data($ad_id);
				$updated = array('duration', 'max_clicks', 'max_impressions');
				foreach ($updated as $field)
				{
					$current_data[$field] = $this->values[$field];
				}

				if ($current_data['expired'] && !is_ad_expired($current_data))
				{
					$this->fields['expired'] = 'int';
					$this->values['expired'] = 0;
				}

				$update_fields = array();
				foreach ($this->fields as $name => $type)
				{
					$update_fields[] = $name . ' = {' . $type . ':' . $name . '}';
				}

				$this->values['ad_id'] = $ad_id;
				update_ad_data($update_fields, $this->values);
			}

			redirectexit('action=admin;area=ads;sa=ads');
		}

		// Adding a new or editing an existing ad
		if ($context['is_new'])
		{
			$context['ad'] = array(
				'id_ad' => 0,
				'name' => '',
				'body' => '',
				'positions' => array(),
				'default_display' => array(),
				'custom_display' => '',
				'allowed_groups' => array(),
				'denied_groups' => array(),
				'max_clicks' => 0,
				'max_impressions' => 0,
				'expiration' => array(0, 1),
				'status' => 1,
			);
		}
		else
		{
			$context['ad'] = get_ad_data($ad_id);
		}

		$context['positions'] = get_ads_positions();
		$context['membergroups'] = get_ads_membergroups();
		$context['actions'] = get_ads_actions();
		$context['boards'] = get_ads_boards();
		$context['body_template'] = get_ads_body_templates();
		$context['page_title'] = $context['is_new'] ? $txt['sa_ads_add_title'] : $txt['sa_ads_edit_title'];
		$context['sub_template'] = 'ads_edit';
	}

	/**
	 * No ad name or ad body then bail now
	 */
	private function _checkErrors()
	{
		if (Util::htmltrim($this->values['name']) === '')
		{
			fatal_lang_error('sa_error_empty_name', false);
		}

		if (Util::htmltrim($this->values['body']) === '')
		{
			fatal_lang_error('sa_error_empty_body', false);
		}
	}

	/**
	 * Set the ads expiration date if one has been supplied
	 */
	private function _setExpiration()
	{
		if (!empty($_POST['expiration']) && $_POST['expiration'] > 0 && !empty($_POST['expiration_type']))
		{
			if ($_POST['expiration_type'] == 3 && $_POST['expiration'] > 50)
			{
				$_POST['expiration'] = 50;
			}

			$this->values['duration'] = ((int) $_POST['expiration']) * 86400 * ($_POST['expiration_type'] == 3 ? 365 : ($_POST['expiration_type'] == 2 ? 30 : 1));
		}
	}

	/**
	 * Set the ads access groups so we only show it as defined
	 */
	private function _setAccess()
	{
		// The groups that will see the ad
		$allowed_groups = array();
		$denied_groups = array();

		if (!empty($_POST['membergroups']) && is_array($_POST['membergroups']))
		{
			foreach ($_POST['membergroups'] as $id => $value)
			{
				if ($value == 1)
				{
					$allowed_groups[] = (int) $id;
				}
				elseif ($value == -1)
				{
					$denied_groups[] = (int) $id;
				}
			}
		}

		if (!empty($allowed_groups))
		{
			$this->values['allowed_groups'] = implode(',', $allowed_groups);
		}

		if (!empty($denied_groups))
		{
			$this->values['denied_groups'] = implode(',', $denied_groups);
		}
	}

	/**
	 * Set the actions and boards where an add can be placed.
	 *
	 * @return array
	 */
	private function _setDefaultDisplay()
	{
		$default_display = array();

		// All the actions which inspire an ad
		if (!empty($_POST['actions']) && is_array($_POST['actions']))
		{
			foreach ($_POST['actions'] as $action)
			{
				$default_display[] = Util::htmlspecialchars($action, ENT_QUOTES);
			}
		}

		// All the boards where an ad can appear
		if (!empty($_POST['boards']) && is_array($_POST['boards']))
		{
			foreach ($_POST['boards'] as $board)
			{
				$default_display[] = (int) $board;
			}
		}

		return $default_display;
	}

	/**
	 * Ensures that each post value is set to a specified field type
	 * Sets cleaned values in the $this->values var
	 */
	private function _sanitizeValues()
	{
		// Sanitize each field as defined
		foreach ($this->fields as $name => $type)
		{
			switch ($type)
			{
				case 'text':
					$this->values[$name] = !empty($_POST[$name]) ? Util::htmlspecialchars($_POST[$name], ENT_QUOTES) : '';
					break;
				case 'int':
					$this->values[$name] = !empty($_POST[$name]) ? (int) $_POST[$name] : 0;
					break;
				case 'array_int':
					if (!empty($_POST[$name]) && is_array($_POST[$name]))
					{
						$temp = array();
						foreach ($_POST[$name] as $item)
						{
							$temp[] = (int) $item;
						}

						$this->values[$name] = implode(',', $temp);
					}
					else
					{
						$this->values[$name] = '';
					}

					$this->fields[$name] = 'text';
					break;
				default:
					break;
			}
		}

	}

	/**
	 * Shows a listing of available ad positions
	 * - allows to remove or disable them
	 */
	public function action_list_positions()
	{
		global $context, $scripturl, $txt;

		// Remove a position
		if (!empty($_REQUEST['delete']))
		{
			checkSession('get');
			delete_position_data((int) $_REQUEST['delete']);
			redirectexit('action=admin;area=ads;sa=positions');
		}

		// Get the status for this position
		if (!empty($_REQUEST['status']))
		{
			checkSession('request');

			list ($position_id, $status) = update_postion_data((int) $_REQUEST['status']);

			if (isset($_REQUEST['xml']))
			{
				$context['item_id'] = $position_id;
				$context['status'] = $status ? 'disabled' : 'active';

				// Clear out any template layers, add the xml response
				$template_layers = Template_Layers::getInstance();
				$template_layers->removeAll();
				$context['sub_template'] = 'sa_change_status';

				obExit();
			}
			else
			{
				redirectexit('action=admin;area=ads;sa=positions');
			}
		}

		$list_options = array(
			'id' => 'list_positions',
			'title' => $txt['sa_positions_list_title'],
			'items_per_page' => 30,
			'base_href' => $scripturl . '?action=admin;area=ads;sa=positions',
			'default_sort_col' => 'name',
			'get_items' => array(
				'function' => array($this, 'list_get_positions_data'),
			),
			'get_count' => array(
				'function' => array($this, 'list_get_positions_count'),
			),
			'no_items_label' => $txt['sa_no_positions'],
			'columns' => array(
				'name' => array(
					'header' => array(
						'value' => $txt['sa_positions_name'],
					),
					'data' => array(
						'db' => 'name',
					),
					'sort' => array(
						'default' => 'name',
						'reverse' => 'name DESC',
					),
				),
				'namespace' => array(
					'header' => array(
						'value' => $txt['sa_positions_namespace'],
					),
					'data' => array(
						'db' => 'namespace',
					),
					'sort' => array(
						'default' => 'namespace',
						'reverse' => 'namespace DESC',
					),
				),
				'type' => array(
					'header' => array(
						'value' => $txt['sa_positions_type'],
					),
					'data' => array(
						'db' => 'type',
					),
					'sort' => array(
						'default' => 'type',
						'reverse' => 'type DESC',
					),
				),
				'actions' => array(
					'header' => array(
						'value' => $txt['sa_generic_actions'],
						'class' => 'centertext'
					),
					'data' => array(
						'function' => create_function('$row', '
							global $context, $scripturl;

							return \'<a href="\' . $scripturl . \'?action=admin;area=ads;sa=positions;status=\' . $row[\'id_position\'] . \';\' . $context[\'session_var\'] . \'=\' . $context[\'session_id\'] . \'" onclick="sa_change_status(\' . $row[\'id_position\'] . \', \\\'position\\\', \\\'\' . $context[\'session_var\'] . \'\\\', \\\'\' . $context[\'session_id\'] . \'\\\'); return false;">\' . sa_embed_image($row[\'status_image\'], \'status_image_\' . $row[\'id_position\']) . \'</a> <a href="\' . $scripturl . \'?action=admin;area=ads;sa=editposition;position=\' . $row[\'id_position\'] . \'">\' . sa_embed_image(\'edit\') . \'</a> <a href="\' . $scripturl . \'?action=admin;area=ads;sa=positions;delete=\' . $row[\'id_position\'] . \';\' . $context[\'session_var\'] . \'=\' . $context[\'session_id\'] . \'">\' . sa_embed_image(\'delete\') . \'</a>\';
						'),
						'class' => 'centertext nowrap',
					),
				),
			),
		);

		require_once(SUBSDIR . '/GenericList.class.php');
		createList($list_options);

		$context['page_title'] = $txt['sa_positions_list_title'];
		$context['sub_template'] = 'list_positions';
	}

	/**
	 * Edit/Add a position to the system
	 */
	public function action_edit_position()
	{
		global $context, $txt;

		$position_id = !empty($_REQUEST['position']) ? (int) $_REQUEST['position'] : 0;
		$context['is_new'] = empty($position_id);

		// Saving
		if (!empty($_POST['submit']))
		{
			checkSession();

			$this->fields = array(
				'name' => 'text',
				'namespace' => 'text',
				'type' => 'int',
				'status' => 'int',
			);

			// Sanitize the fields as defined
			$this->_sanitizeValues();

			// Any reason not to continue?
			$this->_positionError();

			if ($context['is_new'])
			{
				add_position_data($this->fields, $this->values);

				$context['namespace'] = $this->values['namespace'];
				$context['page_title'] = $txt['sa_positions_notice_title'];
				$context['sub_template'] = 'positions_notice';

				return;
			}
			else
			{
				$update_fields = array();
				foreach ($this->fields as $name => $type)
				{
					$update_fields[] = $name . ' = {' . $type . ':' . $name . '}';
				}

				$this->values['position_id'] = $position_id;

				update_positions_data($update_fields, $this->values);

				redirectexit('action=admin;area=ads;sa=positions');
			}
		}

		if ($context['is_new'])
		{
			$context['position'] = array(
				'id_position' => 0,
				'name' => '',
				'namespace' => '',
				'type' => 0,
				'status' => 1,
			);
		}
		else
		{
			$context['position'] = get_position_data($position_id);
		}

		$context['page_title'] = $context['is_new'] ? $txt['sa_positions_add_title'] : $txt['sa_positions_edit_title'];
		$context['sub_template'] = 'positions_edit';
	}

	/**
	 * There are a few reasons we don't continue on bad data
	 */
	private function _positionError()
	{
		if (Util::htmltrim($this->values['name']) === '')
		{
			fatal_lang_error('sa_error_empty_name', false);
		}

		if (Util::htmltrim($this->values['namespace']) === '')
		{
			fatal_lang_error('sa_error_empty_namespace', false);
		}
		elseif (preg_replace('~[A-Za-z0-9_]~', '', $this->values['namespace']) !== '')
		{
			fatal_lang_error('sa_error_invalid_namespace', false);
		}
	}

	/**
	 * Get the data for the ads
	 *
	 * - Callback for createlist, forward to get_ads_data
	 *
	 * @param int $start
	 * @param int $chunk_size
	 * @param string $sort
	 *
	 * - Callback for createList(), forwards to get_ads_data
	 */
	public function list_get_ads_data($start, $chunk_size, $sort = '')
	{
		return get_ads_data($start, $chunk_size, $sort);
	}

	/**
	 * Get the count of ads in the system
	 *
	 * - Callback for createList(), forwards to get_ads_count
	 */
	public function list_get_ads_count()
	{
		return get_ads_count();
	}

	/**
	 * Get the data for the positions
	 *
	 * @param int $start
	 * @param int $chunk_size
	 * @param string $sort
	 *
	 * - Callback for createList(), forwards to get_positions_data
	 */
	public function list_get_positions_data($start, $chunk_size, $sort = '')
	{
		return get_positions_data($start, $chunk_size, $sort);
	}

	/**
	 * Get the count of positions in the system
	 *
	 * - Callback for createList(), forwards to get_positions_count
	 */
	public function list_get_positions_count()
	{
		return get_positions_count();
	}
}