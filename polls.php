<?php
	$user = OpenVBX::getCurrentUser();
	$tenant_id = $user->values['tenant_id'];
	$ci =& get_instance();
	$queries = explode(';', file_get_contents(dirname(__FILE__).'/db.sql'));
	foreach($queries as $query)
		if(trim($query))
			$ci->db->query($query);
	if($remove = intval($_POST['remove'])){
		$ci->db->delete('polls', array('id' => $remove, 'tenant' => $tenant_id));
		if($ci->db->affected_rows())
			$ci->db->delete('polls_responses', array('poll' => $remove));
		die();
	}
	if($poll = intval($_REQUEST['poll'])){
		echo $ci->db->query(sprintf('SELECT data FROM polls WHERE tenant=%d AND id=%d', $tenant_id, $poll))->row()->data;
		die();
	}
	if(($name = htmlentities($_POST['name']))&&($options = $_POST['option'])&&is_array($options)){
		foreach($options as &$option)
			$option = htmlentities($option);
		$ci->db->insert('polls', array(
			'tenant' => $tenant_id,
			'name' => $name,
			'data' => json_encode($options)
		));
	}
	$polls = $ci->db->query(sprintf('SELECT id, name, data, (SELECT COUNT(id) FROM polls_responses WHERE polls_responses.poll=polls.id) AS responses FROM polls WHERE tenant=%d', $tenant_id))->result();
	OpenVBX::addJS('polls.js');
?>
<style>
	.vbx-polls h3 {
		font-size:16px;
		font-weight:bold;
		margin-top:0;
	}
	.vbx-polls .poll,
	.vbx-polls div.option {
		clear:both;	
		width:95%;
		overflow:hidden;
		margin:0 auto;
		padding:5px 0;
		border-bottom:1px solid #eee;
		list-style:disc;
	}
	.vbx-polls div.option {
		display:none;
		background:#ccc;
	}
	.vbx-polls .poll span {
		display:inline-block;
		width:25%;
		text-align:center;
		float:left;
		vertical-align:middle;
		line-height:24px;
	}
	.vbx-polls .option span {
		display:inline-block;
		width:25%;
		text-align:center;
		float:left;
		vertical-align:middle;
		line-height:24px;
	}
	.vbx-polls .poll a {
		text-decoration:none;
		color:#111;
	}
	.vbx-polls form {
		display:none;
		padding:20px 5%;
		background:#eee;
		border-bottom:1px solid #ccc;
	}
	.vbx-polls a.delete {
		display:inline-block;
		height:24px;
		width:24px;
		text-indent:-999em;
		background:transparent url(/assets/i/action-icons-sprite.png) no-repeat -68px 0;
	}
</style>
<div class="vbx-content-main">
	<div class="vbx-content-menu vbx-content-menu-top">
		<h2 class="vbx-content-heading">Polls</h2>
		<ul class="vbx-menu-items-right">
			<li class="menu-item"><button id="button-add-poll" class="inline-button add-button"><span>Add Poll</span></button></li>
		</ul>
	</div><!-- .vbx-content-menu -->
    <div class="vbx-table-section vbx-polls">
		<form method="post" action="">
			<h3>Add Poll</h3>
			<fieldset class="vbx-input-container">
				<label class="field-label">Poll Name
					<input type="text" class="medium" name="name" />
				</label>
				<label class="field-label option">Option
					<input type="text" class="medium" name="option[]" />
				</label>
				<p>
					<button type="submit" class="inline-button submit-button"><span>Add Option</span></button>
					<button type="submit" class="inline-button submit-button"><span>Save</span></button>
				</p>
			</fieldset>
		</form>
<?php if(count($polls)): ?>
		<div class="poll">
			<h3>
				<span>Name</span>
				<span>Options</span>
				<span>Responses</span>
				<span>Delete</span>
			</h3>
		</div>
<?php foreach($polls as $poll):
	$options = json_decode($poll->data);
	$responses = $ci->db->query(sprintf('SELECT COUNT(id) AS num FROM polls_responses WHERE polls_responses.poll=%d GROUP BY response ORDER BY response', $poll->id))->result(); ?>
		<div class="poll" id="poll_<?php echo $poll->id; ?>">
			<p>
				<span><?php echo $poll->name; ?></span>
				<span><a href="" class="options"><?php echo count($options); ?></a></span>
				<span><a href="" class="options"><?php echo $poll->responses; ?></a></span>
				<span><a href="" class="delete">X</a></span>
			</p>
		</div>
<?php foreach($options as $i => $option): ?>
		<div class="option poll_<?php echo $poll->id; ?>">
			<p>
				<span><?php echo $option; ?></span>
				<span>&nbsp;</span>
				<span><?php echo $ci->db->query(sprintf('SELECT COUNT(id) AS num FROM polls_responses WHERE poll=%d AND response=%d', $poll->id, $i))->row()->num; ?></span>
				<span>&nbsp;</span>
			</p>
		</div>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>
    </div>
</div>