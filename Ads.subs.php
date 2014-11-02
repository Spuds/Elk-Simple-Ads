<?php

/**
 * @package SimpleAds
 *
 * @author [SiNaN]
 * @copyright 2008-2014 by: [SiNaN] (sinan@simplemachines.org)
 * @license BSD 3-clause
 *
 * @version 1.0.1
 */

if (!defined('ELK'))
	die('No access...');

/**
 * Load the ads for display
 * Determins what ads will display in what areas
 *
 * @return type
 */
function load_ads()
{
	global $context, $modSettings;

	$db = database();

	$context['ads'] = array();
	$context['displayed_ads'] = array();

	// Tracking them clicks
	if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'update_ad_clicks')
		update_ad_clicks();

	// Don't ever show adds in for these actions
	$ignore_actions = array('helpadmin', 'printpage', 'quotefast', 'spellcheck', 'dlattach', 'findmember', 'jsoption', 'requestmembers', 'jslocale', 'xmlpreview', 'suggest', '.xml', 'api', 'xmlhttp', 'verificationcode', 'viewquery', 'viewadminfile');
	if (isset($_REQUEST['xml']) || isset($_REQUEST['api']) || isset($_REQUEST['debug'])
			|| (!empty($_REQUEST['sa']) && !empty($_REQUEST['package']) && $_REQUEST['sa'] == 'uninstall2' && strpos($_REQUEST['package'], 'SimpleAds') !== false)
			|| (!empty($_REQUEST['action']) && in_array($_REQUEST['action'], $ignore_actions)))
		return;

	// Ad template and css
	loadTemplate('Ads', 'manageads');

	// Get all the active ad positions
	if (($positions = cache_get_data('sa_positions', 240)) === null)
	{
		$request = $db->query('', '
			SELECT
				id_position, namespace, type
			FROM {db_prefix}sa_positions
			WHERE status = {int:active}',
			array(
				'active' => 1,
			)
		);
		$positions = array();
		while ($row = $db->fetch_assoc($request))
			$positions[$row['id_position']] = $row;
		$db->free_result($request);

		cache_put_data('sa_positions', $positions, 240);
	}

	// No postions, done
	if (empty($positions))
		return;

	// Quick portal detection
	if (empty($context['disable_sp']) && !empty($modSettings['sp_portal_mode']) && $modSettings['sp_portal_mode'] == 1)
		$portal = true;

	// Load in all the active ads
	$request = $db->query('', '
		SELECT
			id_ad, body, positions, default_display, custom_display, allowed_groups,
			denied_groups, created, duration, clicks, max_clicks, impressions, max_impressions
		FROM {db_prefix}sa_ads
		WHERE status = {int:active}
			AND expired = {int:not_expired}',
		array(
			'active' => 1,
			'not_expired' => 0,
		)
	);
	while ($row = $db->fetch_assoc($request))
	{
		// Skip if expired
		if (is_ad_expired($row))
			continue;

		// Skip if its not for this user / group
		if (!is_ad_allowed($row['allowed_groups'], $row['denied_groups']))
			continue;

		// Skip if it is not to be shown for this action
		if (!is_ad_displayed($row['default_display'], $row['custom_display']))
			continue;

		$row['positions'] = !empty($row['positions']) ? explode(',', $row['positions']) : array();

		foreach ($row['positions'] as $position)
		{
			if (!isset($positions[$position]))
				continue;

			// Dont want right/left ads on the portal
			if (isset($portal) && in_array($position, array(6, 7)))
				continue;

			if (!isset($context['ads'][$positions[$position]['namespace']]))
			{
				$context['ads'][$positions[$position]['namespace']] = array(
					'type' => $positions[$position]['type'],
					'ads' => array(),
				);
			}

			$body = preg_replace('~<a([^>]+)>~', '<a$1 onclick="update_ad_clicks(' . $row['id_ad'] . ');">', un_htmlspecialchars($row['body']));
			$context['ads'][$positions[$position]['namespace']]['ads'][$row['id_ad']] = $body;
		}
	}
	$db->free_result($request);

	// Do we have any ads to spam them with
	if (empty($context['ads']))
		return;

	loadJavascriptFile('ads.js', array('defer' => true));

	// Get all the layers and insert ours where needed
	$template_layers = Template_Layers::getInstance();
	$temp_layers = $template_layers->getLayers();

	foreach ($temp_layers as $layer)
	{
		if ($layer === 'html')
			$template_layers->addAfter('ads_outer', 'html');
		elseif ($layer === 'body')
			$template_layers->addAfter('ads_inner', 'body');
	}
}

/**
 * Track ad clicks
 * Updates the db counter for this ad
 * Used by xml
 *
 * @return type
 */
function update_ad_clicks()
{
	$db = database();

	$id_ad = !empty($_REQUEST['ad']) ? (int) $_REQUEST['ad'] : 0;

	// No ad, no dice
	if (empty($id_ad))
		return;

	$db->query('', '
		UPDATE {db_prefix}sa_ads
		SET clicks = clicks + 1
		WHERE id_ad = {int:id_ad}',
		array(
			'id_ad' => $id_ad,
		)
	);

	obExit(false);
}

/**
 * Updates the number of impressions for all displayed ad
 */
function update_ad_impressions()
{
	global $context;

	$db = database();

	// No ad displayed, no impressions
	if (empty($context['displayed_ads']))
		return;

	$db->query('', '
		UPDATE {db_prefix}sa_ads
		SET impressions = impressions + 1
		WHERE id_ad IN ({array_int:displayed_ads})',
		array(
			'displayed_ads' => $context['displayed_ads'],
		)
	);
}

/**
 * Determines if a given ad has reached
 * - its expiration date
 * - its max impressions
 * - its max clicks
 * Updated the db to set expired flag if any of these conditions are met
 *
 * @param mixed[] $data
 * @return boolean
 */
function is_ad_expired($data)
{
	$db = database();

	$expired = false;

	if (!empty($data['duration']) && $data['created'] + $data['duration'] < time())
		$expired = true;
	elseif (!empty($data['max_impressions']) && $data['impressions'] >= $data['max_impressions'])
		$expired = true;
	elseif (!empty($data['max_clicks']) && $data['clicks'] >= $data['max_clicks'])
		$expired = true;

	// Set the expired flag
	if ($expired)
	{
		$db->query('', '
			UPDATE {db_prefix}sa_ads
			SET expired = {int:expired}
			WHERE id_ad = {int:id_ad}',
			array(
				'id_ad' => $data['id_ad'],
				'expired' => 1,
			)
		);
	}

	return $expired;
}

/**
 * Determines if a given user can see the ad at all
 *
 * @param string $allowed csv string
 * @param string $denied csv string
 * @return boolean
 */
function is_ad_allowed($allowed, $denied)
{
	global $user_info;
	static $group_cache;

	$cache_key = $allowed . $denied;

	if (!isset($group_cache[$cache_key]))
	{
		$allowed = !empty($allowed) ? explode(',', $allowed) : array();
		$denied = !empty($denied) ? explode(',', $denied) : array();

		$is_allowed = false;
		if (count(array_intersect($user_info['groups'], $denied)) > 0)
			$is_allowed = false;
		elseif (count(array_intersect($user_info['groups'], $allowed)) > 0)
			$is_allowed = true;

		$group_cache[$cache_key] = $is_allowed;
	}

	return $group_cache[$cache_key];
}

/**
 * Determins is an ad should be displayed given where on the site the user is
 *
 * @param type $default
 * @param type $custom
 * @return type
 */
function is_ad_displayed($default, $custom)
{
	global $context;
	static $display_cache, $action, $board;

	$cache_key = $default . $custom;

	$display = false;

	if (!isset($action) || !isset($board))
	{
		$action = !empty($context['current_action']) ? $context['current_action'] : '';

		$board = !empty($context['current_board']) ? $context['current_board'] : 0;

		// Define child actions that we will act on like as though its the parent action
		$exceptions = array(
			'post' => array('announce', 'editpoll', 'emailuser', 'post2', 'sendtopic'),
			'register' => array('activate', 'coppa'),
			'board_index' => array('collapse', 'forum'),
			'admin' => array('credits', 'theme', 'viewquery', 'viewsmfile'),
			'moderate' => array('groups'),
			'login' => array('reminder'),
			'profile' => array('trackip', 'viewprofile'),
		);

		foreach ($exceptions as $key => $exception)
		{
			if (in_array($action, $exception))
			{
				$action = $key;
				break;
			}
		}
	}

	if (!isset($display_cache[$cache_key]))
	{
		// Standard actions
		if (!empty($default) && empty($custom))
		{
			$default = !empty($default) ? explode(',', $default) : array();

			if (!empty($action) && in_array($action, $default))
				$display = true;
			elseif (!empty($board) && in_array($board, $default))
				$display = true;
			elseif (in_array('board_index', $default) && empty($action) && empty($board) && count($_GET) < 1)
				$display = true;
		}
		// Custom actions
		elseif (!empty($custom))
		{
			if ($custom === 'all')
				$display = true;
			else
			{
				$variables = array(
					'{action}' => "'$action'",
					'{board}' => $board,
				);

				$custom = un_htmlspecialchars(str_replace(array_keys($variables), array_values($variables), $custom));

				$display = @eval('return (' . $custom . ') ? true : false;');
			}
		}

		$display_cache[$cache_key] = $display;
	}

	return $display_cache[$cache_key];
}