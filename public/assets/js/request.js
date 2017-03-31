$(document).ready(function() {
	var domain = $('#domain').val();
	init();
	//scroll
	$(window).scroll(function() {
		var p_top = getpositiontop();
		if (p_top > 260) {
			$('#submit-one, #clear-respon, #edit-request, #history-request, #save-request').addClass('fix-top');
			$('#clear-respon').addClass('fix-left-clearrespon');
			$('#edit-request').addClass('fix-left-editrequest');
			$('#history-request').addClass('fix-left-history');
			$('#save-request').addClass('fix-left-savehistory');
		}
		if (p_top < 215) {
			$('#submit-one, #clear-respon, #edit-request, #history-request, #save-request').removeClass('fix-top');
			$('#clear-respon').removeClass('fix-left-clearrespon');
			$('#edit-request').removeClass('fix-left-editrequest');
			$('#history-request').removeClass('fix-left-history');
			$('#save-request').removeClass('fix-left-savehistory');
		}
	});
	// change input api
	$('.api-select').change(function() {
		showselectapi();
	});

	//change api of select
	$('.get-api').find('select').change(function() {
		getDetailForSelect();
	});
	$('.get-api').find('select').keyup(function(event) {
		var key = event.keyCode;
		if (key == 37 || key == 38 || key == 39 || key == 40) {
			getDetailForSelect();
		}
	});
	$('#clear-respon').click(function() {
		$('#respon').html('');
	});

	//change api of input
	$('#API').change(function() {
		var api_change = $(this).val();
		if ( typeof (hint_api[api_change]) != 'undefined') {
			getDetail(hint_api[api_change], api_change);
		} else {
			getDetail('');
		}
	});
	$('.submit').click(function() {
		var dialog_request = $('#dialog-request').parent().css('display');
		var comment_history_auto = "auto save +_+";
		if (dialog_request == 'block') {
			var request = $('#Request-dialog').val();
			$('#Request').val(request);
		} else {
			var request = $('#Request').val();
		}
		var patt = /\s/g;
		var result = $.trim(request).replace(patt, "");
		var api = $('.get-api').find(".api-option:not(:hidden)").val();

		if (api != '') {
			try {
				var json_respon = $('.parsejson').find('input[type="radio"]:checked').val();
				var object = $.parseJSON(result);

				$.ajax({
					type : type_api[api],
					url : api,
					dataType : 'html',
					data : object,
					success : function(data) {
						if(json_respon == 1){
							$('#respon').html(data);
						}
						if(json_respon == 2){
							try {
								var obj = $.parseJSON($.trim(data));
								$.post(domain + '/test/api/parsejson', obj, function(data_debug) {
									$('#respon').html(data_debug);
								});
							} catch(e) {
								console.log(e);
								$('#respon').html(data);
							}
						}
					},
					error : function(jqXHR, textStatus, errorThrown) {
						moveToBeginRespon();
						$('#respon').html(jqXHR.responseText);
						$('#respon').prepend("<h1>" + errorThrown + "</h1>");
						console.log('Ajax Status: ' + textStatus);
					}
				}).done(function() {
					moveToBeginRespon();
					if (!storage.isSet(api)) {
						saveHistory(api, request, comment_history_auto);
					}
					console.log('ajax done');
				});

			} catch(e) {
				console.log(e);
				alert("Sai data nhap vao!");
			}
		} else {
			alert("Nhap API");
		}

	});
});

function init() {
	showselectapi();
	$('#btt').click(function() {
		$("html,body").animate({
			opacity : 1,
			scrollTop : 0,
		}, 1000, function() {
			// Animation complete.
		});
	});
}

function showselectapi() {
	var api_select_tmp = $('.api-select').find('input[type="radio"]:checked').val();
	$('#respon').html('');
	$('#Request').val('');
	$('#detail').html('');
	if (api_select_tmp == 1) {
		$('#API').show('slow');
		$('#select-list-api').hide('fast');
		setbegin('#API');
	}
	if (api_select_tmp == 2) {
		$('#API').hide('slow');
		$('#select-list-api').show('fast');
		getDetailForSelect();
		setbegin('#select-list-api');
	}

}

function getDetailForSelect() {
	var detail = $('#select-list-api').find('option:selected').attr('name');
	if(note_api[$('#select-list-api').val()]) {
		$('#detail').html(detail + ' ( P/S:' + note_api[$('#select-list-api').val()] + ' )');
	} else{
		$('#detail').html(detail);
	}

	$('#respon').html('');
	var obj_detail = $.csv.toArray(detail);
	var maxkey = obj_detail.length;
	var html = "{\n";
	for (key in obj_detail) {
		v = "\"\"";
		if ( obj_detail[key] == "token" ) {
			v = "\"" + $('#token').val() + "\"";
		}
		if (key != maxkey - 1) {
			html += "\"" + obj_detail[key] + "\"" + ":" + v + ",\n";
		} else {
			html += "\"" + obj_detail[key] + "\"" + ":" + v;
		}
	}
	html += "\n}";
	$('#Request').val(html);
}

function getDetail(detail , api) {
	if(note_api[api]) {
		$('#detail').html(detail + ' ( P/S:' + note_api[api] + ' )');
	} else{
		$('#detail').html(detail);
	}

	var obj_detail = $.csv.toArray(detail);
	var maxkey = obj_detail.length;
	var html = "{\n";
	for (key in obj_detail) {
		v = "\"\"";
		if ( obj_detail[key] == "token" ) {
			v = "\"" + $('#token').val() + "\"";
		}
		if (key != maxkey - 1) {
			html += "\"" + obj_detail[key] + "\"" + ":" + v + ",\n";
		} else {
			html += "\"" + obj_detail[key] + "\"" + ":" + v;
		}
	}
	html += "\n}";
	$('#Request').val(html);
	$('#respon').html('');
}

function setbegin(id_select) {
	var begin_api = $(id_select).val();
	if (begin_api.length > 0) {
		if ( typeof (hint_api[begin_api]) != 'undefined') {
			getDetail(hint_api[begin_api], begin_api);
		} else {
			getDetail('');
		}
	}
}

function getpositiontop() {
	var p_top = $("html,body").scrollTop();
	if (p_top == 0) {
		p_top = $("body").scrollTop();
	}
	return p_top;
}

function getApi() {
	var api = $('.get-api').find(".api-option:not(:hidden)").val();
	if (api != '') {
		return api;
	} else {
		alert("Nhap API");
	}
	return '';
}

function moveToBeginRespon() {
	var p_top_cur = getpositiontop();
	if (p_top_cur < 600) {
		$("html,body").animate({
			opacity : 1,
			scrollTop : 550,
		}, 1500);
	}
}
