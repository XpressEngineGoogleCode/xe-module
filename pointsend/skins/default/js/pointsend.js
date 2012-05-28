function doUpdateFee(obj){
    (function($){
        var el = $(obj);
        var fee = $('input[name=fee]').val() / 100;
        var fee_apply_point = $('input[name=fee_apply_point]').val();
        var point = el.val();
        if(point) {
            var cur_fee = 0;
            if(fee_apply_point) {
                if(point >= fee_apply_point) {
                    cur_fee = point * fee;
                }
            } else {
                cur_fee = point * fee;
            }
            $('#cur_fee').html(addCommas(parseInt(cur_fee)));
		} else {
			$('#cur_fee').html(0);
		}
	})(jQuery);
}

function doUpdateRemainingPoint(obj){
    (function($){
        var el = $(obj);
        var cur_point = $('input[name=cur_point]').val();
        if(parseInt(el.val())>0) {
            var point = parseInt(cur_point) - parseInt(obj.value);
            $('#remaining_point').html(addCommas(point));
        } else {
            $('#remaining_point').html(addCommas(cur_point));
        }
    })(jQuery);
}

function addCommas(number) {
	number += '';
	x = number.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}