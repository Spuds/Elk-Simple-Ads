<?php

/**
 * @package SimpleAds
 *
 * @author [SiNaN]
 * @copyright 2008-2021 by: [SiNaN] (sinan@simplemachines.org)
 * @license BSD 3-clause
 *
 * @version 1.0.4
 */

/**
 * Show all the ads in the system, displays the list created by createlist
 */
function template_list_ads()
{
	template_show_list('list_ads');
}

/**
 * Template to display the add/edit screen for adds
 */
function template_ads_edit()
{
	global $context, $scripturl, $txt;

	echo '
	<div id="edit_ad_container">
		<h3 class="category_header">', $context['page_title'], '</h3>
		<form id="admin_form_wrapper" action="', $scripturl, '?action=admin;area=ads;sa=editad" method="post" accept-charset="UTF-8" name="sa_ads">
			<div class="content">
				<dl class="settings">
					<dt>
						<label for="name_input">', $txt['sa_ads_name'], ':</label>
					</dt>
					<dd>
						<input type="text" name="name" id="name_input" value="', $context['ad']['name'], '" class="input_text" />
					</dd>
					<dt>
						<label for="name_input">', $txt['sa_ads_body_template'], ':</label>
					</dt>
					<dd>
						<select name="body_template" onchange="document.sa_ads.body.value = this.options[this.selectedIndex].value">
							<option value="" selected="selected"></option>';

	foreach ($context['body_template'] as $label => $body)
	{
		echo '
							<option value="', $body, '">', $label, '</option>';
	}

	echo '
						</select>
					</dd>
					<dt>
						<label for="body_textarea">', $txt['sa_ads_body'], ':</label>
					</dt>
					<dd>
						<textarea id="body_textarea" name="body" cols="75" rows="7">', $context['ad']['body'], '</textarea>
					</dd>
					<dt>
						<label for="status_input">', $txt['sa_generic_active'], ':</label>
					</dt>
					<dd>
						<input type="checkbox" name="status" id="status_input" value="1"', $context['ad']['status'] ? ' checked="checked"' : '', ' class="input_check" />
					</dd>
				</dl>
				<hr  />
				<dl class="settings">';

	if (!$context['is_new'])
	{
		echo '
					<dt>
						', $txt['sa_ads_created'], ':
					</dt>
					<dd>
						', $context['ad']['created'], '
					</dd>';
	}

	echo '
					<dt>
						<label for="expiration_input">', $txt['sa_ads_expiration'], ':</label>
					</dt>
					<dd>
						<input type="text" name="expiration" id="expiration_input" value="', $context['ad']['expiration'][0], '" class="input_text" size="5" />
						<select name="expiration_type">
							<option value="1"', $context['ad']['expiration'][1] == 1 ? ' selected="selected"' : '', '>', $txt['sa_generic_expire_days'], '</option>
							<option value="2"', $context['ad']['expiration'][1] == 2 ? ' selected="selected"' : '', '>', $txt['sa_generic_expire_months'], '</option>
							<option value="3"', $context['ad']['expiration'][1] == 3 ? ' selected="selected"' : '', '>', $txt['sa_generic_expire_years'], '</option>
						</select>
					</dd>';

	if (!$context['is_new'])
	{
		echo '
					<dt>
						', $txt['sa_ads_clicks'], ':
					</dt>
					<dd>
						', $context['ad']['clicks'], '
					</dd>';
	}

	echo '
					<dt>
						<label for="click_limit_input">', $txt['sa_ads_click_limit'], ':</label>
					</dt>
					<dd>
						<input type="text" name="max_clicks" id="click_limit_input" value="', $context['ad']['max_clicks'], '" class="input_text" size="10" />
					</dd>';

	if (!$context['is_new'])
	{
		echo '
					<dt>
						', $txt['sa_ads_impressions'], ':
					</dt>
					<dd>
						', $context['ad']['impressions'], '
					</dd>';
	}

	echo '
					<dt>
						<label for="impression_limit_input">', $txt['sa_ads_impression_limit'], ':</label>
					</dt>
					<dd>
						<input type="text" name="max_impressions" id="impression_input" value="', $context['ad']['max_impressions'], '" class="input_text" size="10" />
					</dd>
				</dl>
				<hr />
				<dl class="settings">
					<dt>
						<label for="selectpositions">', $txt['sa_ads_positions'], ':</lable>
					</dt>
					<dd>
						<fieldset id="selectpositions">
							<legend>', $txt['sa_ads_positions'], '</legend>
							<ul class="reset">';

	foreach ($context['positions'] as $id => $label)
	{
		echo '
								<li>
									<input type="checkbox" name="positions[]" id="position_', $id, '" value="', $id, '"', in_array($id, $context['ad']['positions']) ? ' checked="checked"' : '', ' class="input_check"> <label for="position_', $id, '">', $label, '</label>
								</li>';
	}

	echo '
								<li>
									<input type="checkbox" onclick="invertAll(this, this.form, \'positions[]\');" /> <em>', $txt['check_all'], '</em>
								</li>
							</ul>
						</fieldset>
					</dd>
				</dl>
				<hr />
				<dl class="settings">
					<dt>
						<label for="selectgroups">', $txt['sa_ads_membergroups'], ':</label>
					</dt>
					<dd>
					<fieldset id="selectgroups">
						<legend>', $txt['sa_generic_membergroup'], '</legend>
							<table>
								<tr>
									<th class="lefttext">', $txt['sa_generic_membergroup'], '</td>
									<th>', $txt['sa_generic_groups_allow'], '</th>
									<th>', $txt['sa_generic_groups_disallow'], '</th>
									<th>', $txt['sa_generic_groups_deny'], '</th>
								</tr>';

	foreach ($context['membergroups'] as $id => $label)
	{
		$current = 0;
		if (in_array($id, $context['ad']['allowed_groups']))
		{
			$current = 1;
		}
		elseif (in_array($id, $context['ad']['denied_groups']))
		{
			$current = -1;
		}

		echo '
								<tr>
									<td>', $label, '</td>
									<td>
										<input type="radio" name="membergroups[', $id, ']" value="1"', $current == 1 ? ' checked="checked"' : '', ' class="input_radio">
									</td>
									<td>
										<input type="radio" name="membergroups[', $id, ']" value="0"', $current == 0 ? ' checked="checked"' : '', ' class="input_radio">
									</td>
									<td>
										<input type="radio" name="membergroups[', $id, ']" value="-1"', $current == -1 ? ' checked="checked"' : '', ' class="input_radio">
									</td>
								</tr>';
	}

	echo '
							</table>
						</fieldset>
					</dd>
				</dl>
				<hr />
				<dl class="settings">
					<dt>
						<label for="selectactions">', $txt['sa_ads_actions'], ':</label>
					</dt>
					<dd>
						<fieldset id="selectactions">
							<legend>', $txt['sa_generic_actions'], '</legend>
							<ul class="reset">';

	foreach ($context['actions'] as $id => $label)
	{
		echo '
								<li>
									<input type="checkbox" name="actions[]" id="action_', $id, '" value="', $id, '"', in_array($id, $context['ad']['default_display']) ? ' checked="checked"' : '', ' class="input_check"> <label for="action_', $id, '">', $label, '</label>
								</li>';
	}

	echo '
								<li>
									<input type="checkbox" onclick="invertAll(this, this.form, \'actions[]\');" /> <em>', $txt['check_all'], '</em>
								</li>
							</ul>
						</fieldset>
					</dd>
				</dl>
				<hr />
				<dl class="settings">
					<dt>
						<label for="selectboards">', $txt['sa_ads_boards'], ':</label>
					</dt>
					<dd>
						<fieldset id="selectboards">
							<legend>', $txt['sa_ads_boards'], '</legend>
							<ul class="reset">';

	foreach ($context['boards'] as $id => $label)
	{
		echo '
								<li>
									<input type="checkbox" name="boards[]" id="board_', $id, '" value="', $id, '"', in_array($id, $context['ad']['default_display']) ? ' checked="checked"' : '', ' class="input_check"> <label for="board_', $id, '">', $label, '</label>
								</li>';
	}

	echo '
								<li>
									<input type="checkbox" onclick="invertAll(this, this.form, \'boards[]\');" /> <em>', $txt['check_all'], '</em>
								</li>
							</ul>
						</fieldset>
					</dd>
				</dl>
				<hr />
				<dl class="settings">
					<dt>
						<label for="custom_display_input">', $txt['sa_ads_custom_display'], ':</label>
					</dt>
					<dd>
						<input type="text" name="custom_display" id="custom_display_input" value="', $context['ad']['custom_display'], '" class="input_text" size="30" />
					</dd>
				</dl>
				<div class="submitbutton">
					<input type="submit" name="submit" value="', $context['page_title'], '" />
					<input type="hidden" name="ad" value="', $context['ad']['id_ad'], '" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				</div>
			</div>
		</form>
	</div>';
}

/**
 * List all the ad positions in the system
 */
function template_list_positions()
{
	template_show_list('list_positions');
}

/**
 * Show a selected placement details
 */
function template_positions_edit()
{
	global $context, $scripturl, $txt;

	echo '
	<div id="edit_position_container">
		<h3 class="category_header">', $context['page_title'], '</h3>
		<form id="admin_form_wrapper" action="', $scripturl, '?action=admin;area=ads;sa=editposition" method="post" accept-charset="UTF-8">
			<div class="content">
				<dl class="settings">
					<dt>
						<label for="name_input">', $txt['sa_positions_name'], ':</label>
					</dt>
					<dd>
						<input type="text" name="name" id="name_input" value="', $context['position']['name'], '" class="input_text" />
					</dd>
					<dt>
						<label for="namespace_input">', $txt['sa_positions_namespace'], ':</label>
					</dt>
					<dd>
						<input type="text" name="namespace" id="namespace_input" value="', $context['position']['namespace'], '" class="input_text" />
					</dd>
					<dt>
						<label for="type">', $txt['sa_positions_type'], ':</label>
					</dt>
					<dd>
						<ul class="reset">
							<li>
								<input type="radio" name="type" id="type_0" value="0"', $context['position']['type'] == 0 ? ' checked="checked"' : '', ' class="input_radio"> <label for="type_0">', $txt['sa_generic_plain'], '</label>
							</li>
							<li>
								<input type="radio" name="type" id="type_1" value="1"', $context['position']['type'] == 1 ? ' checked="checked"' : '', ' class="input_radio"> <label for="type_1">', $txt['sa_generic_rotating'], '</label>
							</li>
						</ul>
					</dd>
					<dt>
						<label for="status_input">', $txt['sa_generic_active'], ':</label>
					</dt>
					<dd>
						<input type="checkbox" name="status" id="status_input" value="1"', $context['position']['status'] ? ' checked="checked"' : '', ' class="input_check" />
					</dd>
				</dl>
				<div class="submitbutton">
					<input type="submit" name="submit" value="', $context['page_title'], '" />
					<input type="hidden" name="position" value="', $context['position']['id_position'], '" />
					<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
				</div>
			</div>
		</form>
	</div>';
}

function template_positions_notice()
{
	global $context, $scripturl, $txt;

	echo '
	<div id="position_notice_container">
		<h3 class="category_header">', $context['page_title'], '</h3>
		<form id="admin_form_wrapper" action="', $scripturl, '?action=admin;area=ads;sa=positions" method="post" accept-charset="UTF-8">
			<div class="content">
				', sprintf($txt['sa_positions_notice_body'], $context['namespace']), '
				<div id="submitbutton">
					<input type="submit" name="submit" value="', $txt['sa_generic_continue'], '" />
				</div>
			</div>
		</form>
	</div>';
}

/**
 * Provide an xml response to an active on/off toggle
 */
function template_sa_change_status()
{
	global $context, $txt;

	echo '<?xml version="1.0" encoding="UTF-8" ?', '>
	<elk>
		<id>', $context['item_id'], '</id>
		<status>', $context['status'], '</status>
		<label>', $txt['sa_generic_' . $context['status']], '</label>
	</elk>';
}