$(function() {
	$('.base-number-format-input').keyup(function() {
		$(this).siblings('.base-number-format').text($(this).val());
	});

	$('a.remove-record').click(function(e) {
		e.preventDefault();
		if (confirm('Naozaj chcete odstrániť zadané údaje z databázy?')) {
			var $anchor = $(this);
			var customHref = $(this).attr('href');
			if ($anchor.hasClass('remove-tasks')) {
				if ($('#input-date').val().length === 0) {
					return;
				}
				customHref += '&date=' + $('#input-date').val();
				$('#input-date').val('');
			}
			$.getJSON(customHref, function(payload) {
				if ($anchor.hasClass('remove-tasks')) {
					$anchor.closest('.form-horizontal').before($('<div></div>').addClass('alert alert-success text-center col-xs-6 col-xs-offset-3').html(payload.message));
					return;
				}
				if (payload.accepted) {
					$anchor.parent('td').parent('tr').fadeOut(400, function() {
						$anchor.closest('table').before($('<div></div>').addClass('alert alert-success text-center col-xs-6 col-xs-offset-3').html(payload.message));
						$(this).remove();
					});
				}
				else {
					$anchor.closest('table').before($('<div></div>').addClass('alert alert-danger text-center col-xs-6 col-xs-offset-3').html(payload.message));
				}
			});
		}
	});

	$('.btn-hint').click(function(e) {
		e.preventDefault();
		var $task = $(this).closest('.form-group');
		var $anchor = $(this);
		$.getJSON($(this).attr('href'), function(payload) {
			if (payload.accepted) {
				if (payload.part == "base-number") {
					$task.find('.base-number-format-input').val(payload.value);
					$task.find('.base-number-format').text(payload.value);
				}
				else if (payload.part == "exp") {
					$task.find('.exp-input').val(payload.value);
				}
				else if (payload.part == "expBase") {
					$task.find('.expBase-input').val(payload.value);
				}
			}
			else {
				alert("Hint pre danú úlohu sa nepodarilo získať!");
			}
			if (payload.close) {
				$anchor.fadeOut(function() {
					$anchor.remove();
				});
			}
		});
	});

	$('#loadResults').click(function(e) {
		e.preventDefault();
		$count = $('.table-results').find('tr').length - 1;
		$anchor = $(this);
		$.getJSON($anchor.attr('href') + "&count=" + $count, function(payload) {
			console.log(payload);
			if (payload.accepted) {
				for (var i in payload.snippets) {
					$newResults = $("<table></table>").html(payload.snippets[i]);
					$newResults.find("tr").eq(0).remove();
					$("#" + i).find('tr').last().after($newResults.html());
				}
			}
			else {
				alert("Nepodarilo sa načítať viac úloh!");
				$anchor.fadeOut();
			}
		});
	});

	$('#choose-diff').click(function(e) {
		e.preventDefault();
		$select = $(this).parent().find('select');
		if ($select.parent().is(":visible")) {
			window.location.href = $(this).attr('href')+'?diff='+$select.val();
		} else {
			$(this).parent().find('select').parent().slideDown();
		}
	});

	$('#show_test_form').click(function(e) {
		$('#set_test_form').slideToggle(500, function() {

		});
	});
	$('#show_group_form').click(function(e) {
		$('#group_form').slideToggle(500, function() {

		});
	});
});
