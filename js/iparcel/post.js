/**
 * Default i-parcel's post script
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski
 */
var iparcelPost = {
	single: function(sku, url, qty){
		var $jip = jQuery.noConflict();
		iparcelPost.stock(qty);
		url+='shippingip/ajax/configurable';
		$jip(document).ready(function(){
			var $sku = $jip("<div/>");
			$sku.css("display","none");
			$sku.attr("class","iparcelsku");
			$sku.text(sku);
			$jip('.add-to-cart').append($sku);

			var $options = $jip("<div/>");
			$options.css("display","none");
			$options.attr("class","iparceloptions");
			$jip('.add-to-cart').append($options);

			var $stockqty = $jip("<div/>");
			$stockqty.css("display","none");
			$stockqty.attr("class","iparcelstockquantity");
			$stockqty.text(qty);
			$jip('.add-to-cart').append($stockqty);

			var change = function(){
				iparcelPost.setStock('false');
				var validate = true;
				var super_attribute = '';
				$jip('.super-attribute-select').each(function(){
					var $this = $jip(this);
					if ($this.val().length == 0){
						validate = false;
					}
					super_attribute+=$this.attr('name')+'='+$this.val()+'&';
				});
				if (!validate){
					iparcelMage.displayEligibility();
					return;
				}
				iparcelMage.ajax.post(sku, super_attribute, url);
			}
			change();
			$jip('.super-attribute-select').change(function(){
				change();
			});
		});
	},
	stock: function(qty){
		var $jip = jQuery.noConflict();
		$jip(document).ready(function(){
			var $stock = $jip("<div/>");
			$stock.css("display","none");
			$stock.attr("class","iparcelstock");
			$stock.text(qty > 0 ? 'true' : 'false');
			$jip('.add-to-cart').append($stock);
		});
	},
	setStock: function(value){
		jQuery('.iparcelstock').text(value);
	},
	sku_list: function(sku_list){
		var $jip = jQuery.noConflict();
		$jip(document).ready(function(){
			$jip.each(sku_list, function(sku,name){
				var $sku = $jip("<div/>");
				$sku.css("display","none");
				$sku.attr("class","iparcelsku");
				$sku.text(sku);
				$jip('.item>a[title="'+name+'"]').parent().find('.actions').append($sku);
			});
		});
	},
	stock_list: function(stock_list){
		var $jip = jQuery.noConflict();
		$jip(document).ready(function(){
			$jip.each(stock_list, function(name,qty){
				var $stock = $jip("<div/>");
				$stock.css("display","none");
				$stock.attr("class","iparcelstock");
				$stock.text(qty > 0 ? 'true' : 'false');
				$jip('.item>a[title="'+name+'"]').parent().find('.actions').append($stock);

				var $stockQty = $jip("<div/>");
				$stockQty.css('display','none');
				$stockQty.attr('class','iparcelstockquantity');
				$stockQty.text(qty);
				$jip('.item>a[title="'+name+'"]').parent().find('.actions').append($stockQty);
			});
		});
	}
}
