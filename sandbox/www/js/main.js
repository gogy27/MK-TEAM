$(function() {

	$('.base-number-format-input').keyup(function() {
		$(this).parent('.nowrap').siblings('.nowrap').find('.base-number-format').text($(this).val());
	});

	$('a.remove-user').click(function(e) {
		e.preventDefault();
		var $anchor = $(this);
		$.getJSON($(this).attr('href'), function(payload) {
			if (payload.accepted) {
				$anchor.parent('td').parent('tr').fadeOut(400, function(){
					$anchor.closest('table').before($('<div></div>').addClass('alert alert-success text-center col-xs-6 col-xs-offset-3').html(payload.message));
					$(this).remove();
				});
			}
			else {
				$anchor.closest('table').before($('<div></div>').addClass('alert alert-danger text-center col-xs-6 col-xs-offset-3').html(payload.message));
			}
		});
	});
});
