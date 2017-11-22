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
 * Function to return the template and content for a given position
 *
 * @param string $position
 */
function template_ad_position($position)
{
	global $context;

	// No ad in this position
	if (!isset($context['ads'][$position]))
	{
		return;
	}

	// A rotating ad at this position
	if ($context['ads'][$position]['type'] == 1)
	{
		$random = array_rand($context['ads'][$position]['ads']);

		echo '
	<div id="ad_', $position, '_', $random, '" class="ad ad_', $position, '" >
		', $context['ads'][$position]['ads'][$random], '
	</div>';

		$context['displayed_ads'][] = $random;
	}
	// Otherwise a static ad
	else
	{
		foreach ($context['ads'][$position]['ads'] as $id => $content)
		{
			echo '
	<div id="ad_', $position, '_', $id, '" class="ad ad_', $position, '" >
		', $content, '
	</div>';

			$context['displayed_ads'][] = $id;
		}
	}
}

function template_ads_outer_above()
{
	template_ad_position('overall_header');
}

function template_ads_outer_below()
{
	template_ad_position('overall_footer');
}

function template_ads_inner_above()
{
	global $context;

	template_ad_position('below_menu');

	// Side ad's
	if (isset($context['ads']['left_side']) || isset($context['ads']['right_side']))
	{
		echo '
	<table class="spads_table">
		<tr>';

		if (isset($context['ads']['left_side']))
		{
			echo '
			<td class="spads-left" >';

			template_ad_position('left_side');

			echo '
			</td>';
		}

		// td for the "forum" itself
		echo '
			<td>';
	}
}

function template_ads_inner_below()
{
	global $context;

	if (isset($context['ads']['left_side']) || isset($context['ads']['right_side']))
	{
		// Close the forum
		echo '
			</td>';

		// Right side ad's
		if (isset($context['ads']['right_side']))
		{
			echo '
			<td class="spads-right">';

			template_ad_position('right_side');

			echo '
			</td>';
		}

		echo '
		</tr>
	</table>';
	}

	template_ad_position('above_footer');
}

function template_ic_above_info_center()
{
	template_ad_position('above_info_center');
}

function template_last_post_below()
{
	template_ad_position('after_last_post');
}