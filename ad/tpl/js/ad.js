function completeRegisterAd(ret_obj) {
	var message = ret_obj['message'];

	alert(message);

	location.href = current_url.setQuery('act','');
}

function completeModifyAd(ret_obj) {
	var message = ret_obj['message'];

	alert(message);

	location.href = current_url.setQuery('act','');
}

function completeDeleteAd(ret_obj) {
	var message = ret_obj['message'];

	alert(message);

	location.href = current_url.setQuery('act','');
}

function doCheckAttachFile(obj) {
	if((!/\.(gif|jpg|png|jpeg)$/i.test(obj.value))) {
		alert('올바른 파일을 선택해 주시기 바랍니다.');
		obj.focus();
		return;
	}
}