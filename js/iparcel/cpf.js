/**
 * i-parcel's CPF field processing script
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
var iparcel = {
	/**
	 * Processing Shipping/Billing country changes
	 */
	cpf: function(field_data){
		var $jip = jQuery.noConflict();
		$jip(document).ready(function(){
			$jip('select[id="billing:country_id"]').change(function(){
				$jip('#cpf-billing').remove();
				if ($jip(this).val() in field_data){
					iparcel.addCpfField('billing', field_data[$jip(this).val()]);
				}
			});
			$jip('select[id="shipping:country_id"]').change(function(){
				$jip('#cpf-shipping').remove();
				if ($jip(this).val() in field_data){
					iparcel.addCpfField('shipping', field_data[$jip(this).val()]);
				}
			});
		});
	},

	/**
	 * Adding proper CPF field
	 */
	addCpfField: function(form, data){
		var $jip = jQuery.noConflict();
		var $list_item = $jip('<li>');
		$list_item.attr('id','cpf-'+form);
		var $label = $jip('<label>');
		$label.attr('for',form+':cpf');
		var $div = $jip('<div>');
		$div.addClass('input-box');
		var $input = $jip('<input>');
		$input.attr({
			id: form+':cpf',
			class: 'input-text',
			type: 'text',
			value: '',
			name: form+'[cpf]',
			title: data['name']
		});
		if (data['required'] != '0'){
			$label.addClass('required');
			$input.addClass('required-entry');
			$label[0].innerHTML = '<em>*</em>'
		}
		$label[0].innerHTML += data['name'];
		$div.append($input);
		$list_item.append($label);
		$list_item.append($div);
		$jip('#co-'+form+'-form .fieldset ul').append($list_item);
	}
}
