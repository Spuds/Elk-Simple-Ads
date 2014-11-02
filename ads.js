function update_ad_clicks(id_ad)
{
	var temp_image = new Image();

	temp_image.src = elk_prepareScriptUrl(elk_scripturl) + 'action=update_ad_clicks;ad=' + parseInt(id_ad) + ';xml';
}