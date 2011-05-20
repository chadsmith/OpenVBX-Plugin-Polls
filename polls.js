$(function(){
	$('#button-add-poll').click(function(e) {
		$('.vbx-polls form:not(:first):visible, .vbx-polls .option').slideUp();
		$('.vbx-polls form:first').slideToggle();
		return false;
	});
	$('.vbx-polls form button').eq(0).click(function() {
		var $label = $('label.option');
		$label.last().after($('<div>').append($label.first().clone()).html());
		return false;
	});
	$('.vbx-polls a.options').click(function() {
		var $poll = $(this).parent().parent().parent();
		var id = $poll.attr('id');
		$('.vbx-polls .option:not(.' + id + '):visible, .vbx-polls form').slideUp();
		$('.vbx-polls .option.' + id).slideToggle();
		return false;
	});
	$('.vbx-polls .poll a.delete').click(function() {
		var $poll = $(this).parent().parent().parent();
		var id = $poll.attr('id');
		if(confirm('You are about to delete "' + $poll.children().children('span').eq(0).text() + '" and all its responses.'))
			$.ajax({
				type: 'POST',
				url: window.location,
				data: { remove: id.match(/([\d]+)/)[1] },
				success: function() {
					$poll.add('.vbx-polls .option.' + id).hide(500);
				},
				dataType: 'text'
			});
		return false
	});
})
