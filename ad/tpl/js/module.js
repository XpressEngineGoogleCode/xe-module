/* 광고 시간 범위 초기화 */
function initAdTimeRange() {
	var sel_obj = jQuery('#selAdTimeRange')[0];
	var hidden_obj = jQuery('#hidAdTimeRange');
	if(hidden_obj.val()) {
		var tempValue = hidden_obj.val().split(',');
		var tempCount = tempValue.length;
		var items = [];
		for (var i=0; i<tempCount; i++) {
			var opt = new Option(tempValue[i],tempValue[i],true,true);
			sel_obj.options[sel_obj.options.length] = opt;
			sel_obj.size = sel_obj.options.length;
			sel_obj.selectedIndex = -1;
		}
	}
}

/* 광고 시간 범위 추가 */
function doInsertAdTime() {
	var fo_obj = jQuery('#fo_insert_module')[0];
	var sel_obj = fo_obj._ad_time_range;
	var ad_time = fo_obj._ad_time.value;
	if(!ad_time) {
		alert('시간을 입력해 주세요.');
		return;
	}

	var numberPattern = /^[0-9]*$/;
	if(ad_time == 0) {
		alert('유효하지 않은 시간입니다');
		return;
	} else if(!numberPattern.test(ad_time)) {
		alert('숫자만 입력해 주세요.');
		return;
	}

	var text = ad_time;

	var items = new Array();
	for(var i=0;i<sel_obj.options.length;i++) {
		if(sel_obj.options[i].value == ad_time) {
			alert('이미 추가하셨습니다.');
			return;
		}
	}

	var opt = new Option(text,text,true,true);
	sel_obj.options[sel_obj.options.length] = opt;

	fo_obj._ad_time.value = '';
	sel_obj.size = sel_obj.options.length;
	sel_obj.selectedIndex = -1;

	for(var i=0;i<sel_obj.options.length;i++) {
		items[items.length] = sel_obj.options[i].value;
	}

	fo_obj.ad_time_range.value = items.join(',');
	fo_obj._ad_time_range.focus();
}

/* 광고 시간 범위 삭제 */
function doDeleteAdTime() {
	var fo_obj = jQuery('#fo_insert_module')[0];
	var sel_obj = fo_obj._ad_time_range;
	if(sel_obj.selectedIndex != -1) sel_obj.remove(sel_obj.selectedIndex);
	else return;

	sel_obj.size = sel_obj.options.length;
	sel_obj.selectedIndex = -1;

	var items = new Array();
	for(var i=0;i<sel_obj.options.length;i++) {
		items[items.length] = sel_obj.options[i].value;
	}
	fo_obj.ad_time_range.value = items.join(',');
}

/* 허용 글자색 초기화 */
function initAdColorRange(sel_obj) {
	(function($){
		var hidden_obj = $('#hiddenColorRange');
		if(hidden_obj.val()) {
			var tempValue = hidden_obj.val().split(',');
			var tempCount = tempValue.length;
			var items = new Array();

			for (var i=0; i<tempCount; i++) {
				var opt = new Option(tempValue[i] ,tempValue[i] ,true ,true);
				opt['style']['color'] = tempValue[i];
				opt['style']['background'] = tempValue[i];
				sel_obj.options[sel_obj.options.length] = opt;
				sel_obj.size = sel_obj.options.length;
				sel_obj.selectedIndex = -1;
			}
		}
	})(jQuery);
}

/* 허용 글자색 추가 */
function doInsertAdColor(color, sel_obj) {
	var colorPattern = /^#/;

	// 색을 선택하지 않았다면 에러
	if(!color || color == 'transparent') {
		alert(msg_choose_color);
		return;
	}

	// 올바른 색인지 확인
	if(!colorPattern.test(color)) {
		alert(msg_choose_correct_color);
		return;
	}

	var text = color;
	var items = new Array();
	var length = sel_obj.options.length;

	for(var i=0;i<length;i++) {
		if(sel_obj.options[i].value == color) {
			alert(msg_already_inserted);
			return;
		}
	}

	var opt = new Option(color, color, true, true);
	opt['style']['color'] = color;
	opt['style']['background'] = color;
	sel_obj.options[sel_obj.options.length] = opt;

	jQuery('#tColor').val('');
	sel_obj.size = sel_obj.options.length;
	sel_obj.selectedIndex = -1;

	for(var i=0;i<sel_obj.options.length;i++) {
		items[items.length] = sel_obj.options[i].value;
	}

	jQuery('#hiddenColorRange').val(items.join(','));
	jQuery('#selColorRange').focus();
}

/* 허용 글자색 삭제 */
function doDeleteAdColor() {
	var sel_obj = document.getElementById('selColorRange');

	if(!sel_obj.value) {
		alert(msg_cannot_delete_default_color);
		return;
	}

	if(sel_obj.selectedIndex != -1) {
		sel_obj.remove(sel_obj.selectedIndex);
	} else {
		return;
	}

	sel_obj.size = sel_obj.options.length;
	sel_obj.selectedIndex = -1;

	var items = new Array();
	for(var i=0;i<sel_obj.options.length;i++) {
		items[items.length] = sel_obj.options[i].value;
	}

	jQuery('#hiddenColorRange').val(items.join(','));
}

/* 허용 배경색 초기화 */
function initAdBgColorRange() {
	var fo_obj = jQuery('#fo_insert_module')[0];
	var sel_obj = fo_obj._ad_bgcolor_range;
	var hidden_obj = fo_obj.ad_bgcolor_range;
	if(hidden_obj.value) {
		var tempValue = hidden_obj.value.split(',');
		var tempCount = tempValue.length;
		var items = new Array();

		for (var i=0; i<tempCount; i++) {
			var opt = new Option(tempValue[i],tempValue[i],true,true);
			opt['style']['color'] = tempValue[i];
			opt['style']['background'] = tempValue[i];
			sel_obj.options[sel_obj.options.length] = opt;
			sel_obj.size = sel_obj.options.length;
			sel_obj.selectedIndex = -1;
		}
	}
}

/* 허용 배경색 추가 */
function doInsertAdBgColor() {
	var fo_obj = jQuery('#fo_insert_module')[0];
	var sel_obj = fo_obj._ad_bgcolor_range;
	var ad_bgcolor = fo_obj.ad_bgcolor.value;
	if(!ad_bgcolor) {
		alert(msg_choose_color);
		return;
	}

	var colorPattern = /^#/;
	if(!colorPattern.test(ad_bgcolor)) {
		alert('올바른 색을 선택해 주세요.');
		return;
	}

	var text = ad_bgcolor;

	var items = new Array();
	for(var i=0;i<sel_obj.options.length;i++) {
		if(sel_obj.options[i].value == ad_bgcolor) {
			alert('이미 추가하셨습니다.');
			return;
		}
	}

	var opt = new Option(text,text,true,true);
	opt['style']['color'] = fo_obj.ad_bgcolor.style.color;
	opt['style']['background'] = ad_bgcolor;
	sel_obj.options[sel_obj.options.length] = opt;

	fo_obj.ad_bgcolor.value = '';
	sel_obj.size = sel_obj.options.length;
	sel_obj.selectedIndex = -1;

	for(var i=0;i<sel_obj.options.length;i++) {
		items[items.length] = sel_obj.options[i].value;
	}
	fo_obj.ad_bgcolor_range.value = items.join(',');

	fo_obj._ad_bgcolor_range.focus();
}

/* 허용 배경색 삭제 */
function doDeleteAdBgColor() {
	var fo_obj = xGetElementById('fo_insert_module');
	var sel_obj = fo_obj._ad_bgcolor_range;
	if(sel_obj.selectedIndex == -1) return;
	sel_obj.remove(sel_obj.selectedIndex);

	sel_obj.size = sel_obj.options.length;
	sel_obj.selectedIndex = -1;

	var items = new Array();
	for(var i=0;i<sel_obj.options.length;i++) {
		items[items.length] = sel_obj.options[i].value;
	}
	fo_obj.ad_bgcolor_range.value = items.join(',');
}