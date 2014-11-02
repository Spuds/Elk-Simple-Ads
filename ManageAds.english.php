<?php
// Version: 1.0.1; ManageAds

// Ads
$txt['sa_ads_name'] = 'Name';
$txt['sa_ads_body_template'] = 'Body Template';
$txt['sa_ads_body'] = 'Body';
$txt['sa_ads_status'] = 'Status';

$txt['sa_ads_click_limit'] = 'Click Limit';
$txt['sa_ads_impression_limit'] = 'Impression Limit';
$txt['sa_ads_expiration'] = 'Expiration';
$txt['sa_ads_created'] = 'Created';

$txt['sa_ads_positions'] = 'Positions';
$txt['sa_ads_membergroups'] = 'Membergroups';
$txt['sa_ads_actions'] = 'Actions';
$txt['sa_ads_boards'] = 'Boards';
$txt['sa_ads_custom_display'] = 'Custom Display';

$txt['sa_ads_clicks'] = 'Clicks';
$txt['sa_ads_impressions'] = 'Impressions';
$txt['sa_ads_expires'] = 'Expires';

$txt['sa_no_ads'] = 'There are no ads created.';

// Positions
$txt['sa_positions_name'] = 'Name';
$txt['sa_positions_namespace'] = 'Namespace';
$txt['sa_positions_type'] = 'Type';

$txt['sa_positions_notice_title'] = 'Position Created';
$txt['sa_positions_notice_body'] = '<p>Position was created successfully!</p><p>In order to display the ads for the created position, please add the following function call to the place where you want ads to be displayed:<br /><br /><code class="bbc_code">template_ad_position(\'%1$s\');</code></p>';
$txt['sa_no_positions'] = 'There are no positions created.';

// Generic
$txt['sa_generic_actions'] = 'Actions';
$txt['sa_generic_continue'] = 'Continue';
$txt['sa_generic_edit'] = 'edit';
$txt['sa_generic_delete'] = 'delete';

$txt['sa_generic_active'] = 'Active';
$txt['sa_generic_disabled'] = 'Disabled';

$txt['sa_generic_expired'] = 'Expired';
$txt['sa_generic_never'] = 'Never';

$txt['sa_generic_membergroup'] = 'Membergroup';
$txt['sa_generic_groups_allow'] = 'A';
$txt['sa_generic_groups_disallow'] = 'X';
$txt['sa_generic_groups_deny'] = 'D';

$txt['sa_generic_expire_days'] = 'Day(s)';
$txt['sa_generic_expire_months'] = 'Month(s)';
$txt['sa_generic_expire_years'] = 'Year(s)';

$txt['sa_generic_board_index'] = 'Board Index';

$txt['sa_generic_plain'] = 'Plain';
$txt['sa_generic_rotating'] = 'Rotating';

// Errors
$txt['sa_error_empty_name'] = 'You left the name field empty.';
$txt['sa_error_empty_body'] = 'You left the body field empty.';
$txt['sa_error_empty_namespace'] = 'You left the namespace field empty.';
$txt['sa_error_duplicate_namespace'] = 'The namespace you selected is in use. Namespace should be unique.';
$txt['sa_error_invalid_namespace'] = 'Namespace can only contain letters, numbers and underscore characters.';

// Admin
$txt['sa_admin_cat_title'] = 'SimpleAds';
$txt['sa_admin_title'] = 'Advertisements';
$txt['sa_admin_desc'] = 'You can manage advertisements and positions in this area.';
$txt['sa_ads_list_title'] = 'Ad List';
$txt['sa_ads_add_title'] = 'Add Ad';
$txt['sa_ads_edit_title'] = 'Edit Ad';
$txt['sa_positions_list_title'] = 'Position List';
$txt['sa_positions_add_title'] = 'Add Position';
$txt['sa_positions_edit_title'] = 'Edit Position';

$helptxt['sa_ads_custom_display'] = 'Using this field to define on specific action(s) on which you want the ad to be displayed i.e. {action} == \'contact\' || {action} == \'forum\'.  Can also enter all to enable everywhere.  Use this if the action is not one of the standard ones defined above';