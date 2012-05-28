(function($) {
	$(document).ready(function(){
		$('#popBody a.btnSign').click(function(event){
			$('#subtraction').click();
			event.preventDefault();
		});
		$('#subtraction').change(function(event){
			if(this.checked){
				$(subtractMessage).slideDown(100);
				$('#popBody a.btnSign').addClass('active');
			} else{
				$(subtractMessage).slideUp(100);
				$('#popBody a.btnSign').removeClass('active');
			}
		});
		$('#input_send_point').keydown(function(event){
			if(event.keyCode < 48 || event.keyCode > 57)
			{
				// 8, 13, 116 (백스페이스, 엔터, F5)는 예외
				if(event.keyCode == 8 || event.keyCode == 13 || event.keyCode == 116)
				{
				}
				else
				{
				event.preventDefault();
				return;
				}
			}
			var point = this.value;
			if(cf_useFee == 'Y')
			{
				var fee = cf_fee / 100;
				var fee_point = parseInt(cf_feePoint);
				var cur_fee = 0;

				if(point) {
					if(fee_point)
					{
						cur_fee = (point >= fee_point) ? point * fee: 0;
					}
					else
					{
						cur_fee = point * fee;
					}
				}

				var feeResult = point ? addCommas(parseInt(cur_fee)) : 0;
				$('#popBody .curFee').html(feeResult);
			}

			var remainingPoint = point > 0 ? parseInt(cf_curPoint) - point : parseInt(cf_curPoint);
			$('#popBody .remainingPoint').html(addCommas(remainingPoint));
		});
	});
})(jQuery);

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