<?php

/**
 * @package SimpleAds
 *
 * @author [SiNaN]
 * @copyright 2008-2014 by: [SiNaN] (sinan@simplemachines.org)
 * @license BSD 3-clause
 *
 * @version 1.0.3
 */

/**
 * Insert an ad
 *
 * @param array $fields
 * @param array $values
 */
function add_ad_data($fields, $values)
{
	$db = database();

	$db->insert('', '
		{db_prefix}sa_ads',
		$fields,
		$values,
		array('id_ad')
	);
}

/**
 * Update an existing ad with new data
 *
 * @param array $update_fields
 * @param array $values
 */
function update_ad_data($update_fields, $values)
{
	$db = database();

	$db->query('', '
		UPDATE {db_prefix}sa_ads
		SET ' . implode(', ', $update_fields) . '
		WHERE id_ad = {int:ad_id}',
		$values
	);
}

/**
 * Fetch the data for a given ad
 *
 * @param int $id_ad
 */
function get_ad_data($id_ad)
{
	$db = database();

	$request = $db->query('', '
		SELECT
			id_ad, name, body, positions, default_display, custom_display,
			allowed_groups, denied_groups, created, duration, clicks, max_clicks,
			impressions, max_impressions, status, expired
		FROM {db_prefix}sa_ads
		WHERE id_ad = {int:id_ad}
		LIMIT {int:limit}',
		array(
			'id_ad' => $id_ad,
			'limit' => 1,
		)
	);
	$ad = array();
	while ($row = $db->fetch_assoc($request))
	{
		$csv_fields = array('positions', 'default_display', 'allowed_groups', 'denied_groups');
		foreach ($csv_fields as $field)
		{
			$row[$field] = !empty($row[$field]) ? explode(',', $row[$field]) : array();
		}

		$row['created'] = standardTime($row['created']);
		$row['expiration'] = get_ad_expiration($row['duration']);

		$ad = $row;
	}
	$db->free_result($request);

	return $ad;
}

/**
 * Callback function for create list to list all ads in the system
 *
 * @param int $start
 * @param int $items_per_page
 * @param string $sort
 */
function get_ads_data($start, $items_per_page, $sort)
{
	global $txt;

	$db = database();

	$request = $db->query('', '
		SELECT
			id_ad, name, clicks, impressions, created, duration, status, expired
		FROM {db_prefix}sa_ads
		ORDER BY {raw:sort}
		LIMIT {int:start}, {int:per_page}',
		array(
			'sort' => $sort,
			'start' => $start,
			'per_page' => $items_per_page,
		)
	);
	$ads = array();
	while ($row = $db->fetch_assoc($request))
	{
		if (empty($row['duration']))
		{
			$row['expires'] = $txt['sa_generic_never'];
		}
		else
		{
			$row['expires'] = standardTime($row['created'] + $row['duration'], '%d/%m/%y');
		}

		$row['status_image'] = $row['status'] ? 'active' : 'disabled';
		$row['status'] = $row['expired'] ? $txt['sa_generic_expired'] : ($row['status'] ? $txt['sa_generic_active'] : $txt['sa_generic_disabled']);

		$ads[] = $row;
	}
	$db->free_result($request);

	return $ads;
}

/**
 * Determines how many ads are in the system
 * callback function used by createlist
 *
 * @return int
 */
function get_ads_count()
{
	$db = database();

	$request = $db->query('', '
		SELECT COUNT(*)
		FROM {db_prefix}sa_ads',
		array()
	);
	list ($total_ads) = $db->fetch_row($request);
	$db->free_result($request);

	return $total_ads;
}

/**
 * Returns an ads expiration time
 *
 * @param int $duration
 * @return array
 */
function get_ad_expiration($duration)
{
	$types = array(
		3 => 86400 * 365,
		2 => 86400 * 30,
		1 => 86400,
	);

	$expiration = array(0, 1);
	foreach ($types as $type => $time)
	{
		if ($duration >= $time)
		{
			$expiration = array($duration / $time, $type);
			break;
		}
	}

	return $expiration;
}

/**
 * Remove an ad from the system
 *
 * @param int $id
 */
function delete_ad($id)
{
	$db = database();

	$db->query('', '
		DELETE FROM {db_prefix}sa_ads
		WHERE id_ad = {int:id_ad}',
		array(
			'id_ad' => $id,
		)
	);
}

/**
 * Update the enabled status of an ad
 *
 * @param int $id
 */
function set_status_ad($id)
{
	$db = database();

	$request = $db->query('', '
		SELECT
			id_ad, status
		FROM {db_prefix}sa_ads
		WHERE id_ad = {int:id_ad}
		LIMIT 1',
		array(
			'id_ad' => $id,
		)
	);
	list ($ad_id, $status) = $db->fetch_row($request);
	$db->free_result($request);
	if (!empty($ad_id))
	{
		$db->query('', '
			UPDATE {db_prefix}sa_ads
			SET status = {int:status}
			WHERE id_ad = {int:id_ad}',
			array(
				'id_ad' => $ad_id,
				'status' => $status ? 0 : 1,
			)
		);
	}

	return array($ad_id, $status);
}

/**
 * Remove a position field from the system
 *
 * @param int $id
 */
function delete_position_data($id)
{
	$db = database();

	$db->query('', '
		DELETE FROM {db_prefix}sa_positions
		WHERE id_position = {int:id_position}',
		array(
			'id_position' => $id,
		)
	);
}

/**
 * Updates the data for a given position
 *
 * @param int $status
 */
function update_postion_data($status)
{
	$db = database();

	$request = $db->query('', '
		SELECT
			id_position, status
		FROM {db_prefix}sa_positions
		WHERE id_position = {int:id_position}
		LIMIT 1',
		array(
			'id_position' => $status,
		)
	);
	list ($position_id, $status) = $db->fetch_row($request);
	$db->free_result($request);

	// Has to exist to update it
	if (!empty($position_id))
	{
		$db->query('', '
			UPDATE {db_prefix}sa_positions
			SET status = {int:status}
			WHERE id_position = {int:id_position}',
			array(
				'id_position' => $position_id,
				'status' => $status ? 0 : 1,
			)
		);

		cache_put_data('sa_positions', null, 240);
	}

	return array($position_id, $status);
}

/**
 * Return the settings for a position
 *
 * @param int $id_position
 */
function get_position_data($id_position)
{
	$db = database();

	$request = $db->query('', '
		SELECT
			id_position, name, namespace, type, status
		FROM {db_prefix}sa_positions
		WHERE id_position = {int:id_position}
		LIMIT {int:limit}',
		array(
			'id_position' => $id_position,
			'limit' => 1,
		)
	);
	$position = array();
	while ($row = $db->fetch_assoc($request))
	{
		$position = $row;
	}
	$db->free_result($request);

	return $position;
}

/**
 * Fetch all position data in the system
 * Callback for createlist to show position listing
 *
 * @param int $start
 * @param int $items_per_page
 * @param string $sort
 */
function get_positions_data($start, $items_per_page, $sort)
{
	global $txt;

	$db = database();

	$request = $db->query('', '
		SELECT
			id_position, name, namespace, type, status
		FROM {db_prefix}sa_positions
		ORDER BY {raw:sort}
		LIMIT {int:start}, {int:per_page}',
		array(
			'sort' => $sort,
			'start' => $start,
			'per_page' => $items_per_page,
		)
	);
	$positions = array();
	while ($row = $db->fetch_assoc($request))
	{
		$row['type'] = $row['type'] ? $txt['sa_generic_rotating'] : $txt['sa_generic_plain'];
		$row['status_image'] = $row['status'] ? 'active' : 'disabled';

		$positions[] = $row;
	}
	$db->free_result($request);

	return $positions;
}

/**
 * Returns the number of positions defined in the system
 * Callback function for createlist
 */
function get_positions_count()
{
	$db = database();

	$request = $db->query('', '
		SELECT COUNT(*)
		FROM {db_prefix}sa_positions',
		array()
	);
	list ($total_positions) = $db->fetch_row($request);
	$db->free_result($request);

	return $total_positions;
}

/**
 * Update a positions data
 *
 * @param string[] $update_fields
 * @param array $values
 */
function update_positions_data($update_fields, $values)
{
	$db = database();

	$db->query('', '
		UPDATE {db_prefix}sa_positions
		SET ' . implode(', ', $update_fields) . '
		WHERE id_position = {int:position_id}',
		$values
	);

	cache_put_data('sa_positions', null, 240);
}

/**
 * Add a new position / namespace to the system
 *
 * @param string[] $fields
 * @param array $values
 * @throws \Elk_Exception
 */
function add_position_data($fields, $values)
{
	$db = database();

	$request = $db->query('', '
		SELECT COUNT(*)
		FROM {db_prefix}sa_positions
		WHERE namespace = {string:namespace}
		LIMIT 1',
		array(
			'namespace' => $values['namespace'],
		)
	);
	list ($has_duplicate) = $db->fetch_row($request);
	$db->free_result($request);

	if ($has_duplicate)
	{
		fatal_lang_error('sa_error_duplicate_namespace', false);
	}

	$db->insert('', '
		{db_prefix}sa_positions',
		$fields,
		$values,
		array('id_position')
	);

	cache_put_data('sa_positions', null, 240);
}

/**
 * Fetch the membergroup listing for use in the template
 */
function get_ads_membergroups()
{
	global $txt;

	$db = database();

	loadLanguage('ManageBoards');

	$groups = array(
		-1 => $txt['parent_guests_only'],
		0 => $txt['parent_members_only'],
	);

	$request = $db->query('', '
		SELECT
			group_name, id_group, min_posts
		FROM {db_prefix}membergroups
		WHERE id_group != {int:moderator_group}
		ORDER BY min_posts, group_name',
		array(
			'moderator_group' => 3,
		)
	);
	while ($row = $db->fetch_assoc($request))
	{
		$groups[(int) $row['id_group']] = trim($row['group_name']);
	}
	$db->free_result($request);

	return $groups;
}

/**
 * Fetch the board listing for use in the template
 */
function get_ads_boards()
{
	$db = database();

	$request = $db->query('order_by_board_order', '
		SELECT
			b.id_board, b.name AS board_name,
			c.name AS cat_name
		FROM {db_prefix}boards AS b
			LEFT JOIN {db_prefix}categories AS c ON (c.id_cat = b.id_cat)
		WHERE redirect = {string:empty_string}',
		array(
			'empty_string' => '',
		)
	);
	$boards = array();
	while ($row = $db->fetch_assoc($request))
	{
		$boards[$row['id_board']] = $row['cat_name'] . ' - ' . $row['board_name'];
	}
	$db->free_result($request);

	return $boards;
}

/**
 * Fetch the action array for use in the template
 */
function get_ads_actions()
{
	global $txt;

	return array(
		'board_index' => $txt['sa_generic_board_index'],
		'recent' => $txt['recent_posts'],
		'unread' => $txt['unread_topics_visit'],
		'unreadreplies' => $txt['unread_replies'],
		'profile' => $txt['profile'],
		'pm' => $txt['pm_short'],
		'calendar' => $txt['calendar'],
		'admin' => $txt['admin'],
		'login' => $txt['login'],
		'register' => $txt['register'],
		'post' => $txt['post'],
		'stats' => $txt['forum_stats'],
		'search' => $txt['search'],
		'mlist' => $txt['members_list'],
		'moderate' => $txt['moderate'],
		'help' => $txt['help'],
		'who' => $txt['who_title'],
	);
}

/**
 * Fetch the allowed ad positions for use in templates etc
 *
 * @return string[]
 */
function get_ads_positions()
{
	$db = database();

	$request = $db->query('', '
		SELECT
			id_position, name
		FROM {db_prefix}sa_positions
		ORDER BY id_position',
		array()
	);
	$positions = array();
	while ($row = $db->fetch_assoc($request))
	{
		$positions[$row['id_position']] = $row['name'];
	}
	$db->free_result($request);

	return $positions;
}

/**
 * Builds the predefined helper templates for ads
 *
 * @return string[]
 */
function get_ads_body_templates()
{
	$templates = array(
		array('window', 'no_title'),
		array('window', 'title'),
		array('window', 'category'),

		array('roundframe', 'no_title'),
		array('roundframe', 'title'),
		array('roundframe', 'category'),

		array('information', 'no_title'),
		array('information', 'title'),
		array('information', 'category'),
	);

	$window = '<div class="content">
	{Content}
</div>';

	$information = '<div class="information">
	{Content}
</div>';

	$roundframe = '<div class="roundframe">
	{Content}
</div>';

	$no_title = '';

	$category = '<h3 class="secondary_header">
	{Title}
</h3>
';

	$title = '<h3 class="category_header">
	{Title}
</h3>
';

	// Build all the combinations from the above bits
	$body_templates = array();
	foreach ($templates as $data)
	{
		$key = implode(' + ', $data);
		$body_templates[$key] = htmlspecialchars(${$data[1]} . ${$data[0]});
	}

	return $body_templates;
}

/**
 * Builds the image tag for use in the templates
 *
 * @param string $name
 * @param boolean|string $id
 * @return string
 */
function sa_embed_image($name, $id = false)
{
	global $settings, $txt;

	if (!isset($settings['sa_images_url']))
	{
		if (file_exists($settings['theme_dir'] . '/images/sa'))
		{
			$settings['sa_images_url'] = $settings['theme_url'] . '/images/sa';
		}
		else
		{
			$settings['sa_images_url'] = $settings['default_theme_url'] . '/images/sa';
		}
	}

	$alt = $txt['sa_generic_' . $name] ?? '';
	return '<img src="' . $settings['sa_images_url'] . '/' . $name . '.png" alt="' . $alt . '" title="' . $alt . '"' . ($id ? ' id="' . $id . '"' : '') . ' />';
}
