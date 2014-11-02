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
 * Admin hook, integrate_admin_areas, called from Admin.php
 *
 * - Adds the ads admin menu
 * @param mixed[] $admin_areas
 */
function iaa_ads(&$admin_areas)
{
	global $txt;

	loadLanguage('ManageAds');

	// our main awards menu area, under the members tab
	$admin_areas['layout']['areas']['ads'] = array(
		'label' => $txt['sa_admin_cat_title'],
		'file' => 'ManageAds.controller.php',
		'controller' => 'ManageAds_Controller',
		'function' => 'action_index',
		'icon' => 'ads.png',
		'permission' => array('admin_forum'),
		'subsections' => array(
			'ads' => array($txt['sa_ads_list_title']),
			'addad' => array($txt['sa_ads_add_title']),
			'positions' => array($txt['sa_positions_list_title']),
			'addposition' => array($txt['sa_positions_add_title']),
		)
	);
}

/**
 * integrate_action_boardindex_after, called from the dispatcher
 * Adds the above info center ad to the top of the info center templates
 */
function ibia_ads()
{
	global $context;

	if (!empty($context['info_center_callbacks']))
		array_unshift($context['info_center_callbacks'], 'above_info_center');
}

/**
 * integrate_prepare_display_context, called from the message display callback
 * used to add the inside first and after first post ads
 *
 * @param mixed[] $output
 */
function ipdc_ads(&$output)
{
	global $context;
	static $msg_counter;

	$msg_counter = !isset($msg_counter) ? 0 : $msg_counter;

	// First message inside
	if ($output['id'] == $context['first_message'])
		$output['body'] = template_ad_position('inside_first_post') . $output['body'];

	// After the first message
	if (++$msg_counter === 2)
		template_ad_position('after_first_post');
}

/**
 * integrate_display_topic hook, called from display.controller
 * Used to add the after last post ad via a template layer
 */
function idt_ads()
{
	// After last message
	$template_layers = Template_Layers::getInstance();
	$template_layers->addBefore('last_post', 'messages_informations');
}