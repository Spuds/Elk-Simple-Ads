function sa_change_status(id, type, session_var, session_id)
{
	if (type !== 'ad' && type !== 'position')
		return false;

	sendXMLDocument(elk_prepareScriptUrl(elk_scripturl) + 'action=admin;area=ads;sa=' + type + 's;xml', 'status=' + id + '&' + session_var + '=' + session_id, sa_on_status_received);

	return false;
}

function sa_on_status_received(XMLDoc)
{
	let xml = XMLDoc.getElementsByTagName('elk')[0],
		id = xml.getElementsByTagName('id')[0].childNodes[0].nodeValue,
		status = xml.getElementsByTagName('status')[0].childNodes[0].nodeValue,
		label = xml.getElementsByTagName('label')[0].childNodes[0].nodeValue,
		old = status === 'active' ? 'disabled.png' : 'active.png';

	if (id !== 0)
	{
		let status_image = document.getElementById('status_image_' + id);
		status_image.src = status_image.src.replace(old, status + '.png');
		status_image.alt = status_image.title = label;
	}

	return false;
}