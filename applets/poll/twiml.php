<?php
$ci =& get_instance();
$poll = AppletInstance::getValue('poll');
$option = AppletInstance::getValue('option');
$direction = 'inbound';

if(!empty($_REQUEST['Direction'])) {
  $direction = $_REQUEST['Direction'];
	$number = normalize_phone_to_E164('inbound' == $direction ? $_REQUEST['From'] : $_REQUEST['To']);
	$ci->db->delete('polls_responses', array('poll' => $poll, 'value' => $number));
	$ci->db->insert('polls_responses', array(
		'poll' => $poll,
		'value' => $number,
		'response' => $option,
		'time' => time()
	));
}

$response = new TwimlResponse;

$next = AppletInstance::getDropZoneUrl('next');
if(!empty($next))
	$response->redirect($next);

$response->respond();
