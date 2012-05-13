/* 모듈 생성 후 */
function completeInsertConfig(ret_obj, response_tags) {
	alert(ret_obj['message']);

	location.href = current_url.setQuery('act','dispAdAdminConfig');
}

/* 모듈 생성 후 */
function completeInsertModule(ret_obj, response_tags) {
	var page = ret_obj['page'];
	var module_srl = ret_obj['module_srl'];

	alert(ret_obj['message']);

	var url = current_url.setQuery('act','dispAdAdminModuleInfo');
	if(module_srl) url = url.setQuery('module_srl',module_srl);
	if(page) url.setQuery('page',page);
	location.href = url;
}

/* 모듈 삭제 후 */
function completeDeleteModule(ret_obj) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];
	var page = ret_obj['page'];
	alert(message);

	var url = current_url.setQuery('act','dispAdAdminModuleList').setQuery('module_srl','');
	if(page) url = url.setQuery('page',page);
	location.href = url;
}


/* 공지 등록 후 */
function completeInsertNotice(ret_obj, response_tags) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];

	alert(message);

	var url = current_url.setQuery('act','dispAdAdminNoticeList');
	location.href = url;
}

/* 일괄 설정 */
function doCartSetup(url) {
	var module_srl = new Array();
	jQuery('#fo_list input[name=cart]:checked').each(function() {
		module_srl[module_srl.length] = jQuery(this).val();
	});

	if(module_srl.length<1) return;

	url += "&module_srls="+module_srl.join(',');
	popopen(url,'modulesSetup');
}