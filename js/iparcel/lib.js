/**
 * i-parcel frontend lib for post scripts
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
var iparcelMage = {
	displayEligibility: function(){
		try{
			$_ipar.fn.iparcel.ux.displayEligibility();
		}catch(exception){}
	},
	ajax: {
		post: function(sku, super_attribute, url){
			var $jip = jQuery.noConflict();
			var data = super_attribute+'sku='+sku;
			$jip.ajax({
				'url': url,
				'data': data,
				type: 'POST',
				async: true,
				success: function(data){
					if (data){
						$jip('.iparcelsku').text(data.sku);

						iparcelPost.setStock('true');

						var $options = $jip('.iparceloptions');
						$options.empty();
						$jip.each(data.attributes, function(key, value){
							var $block = $jip('<div/>');
							$block.attr('id',key);
							$block.text(value);
							$options.append($block);
						});

						$jip('.iparcelstockquantity').text(data.stock);
					}
					iparcelMage.displayEligibility();
				}
			});
		}
	},
	parseHtmlEntities: function(str){
		return str.replace(/&#([0-9]{1,3});/gi,function(match,numStr){
			var num = parseInt(numStr,10);
			return String.fromCharCode(num);
		});
	}
}
