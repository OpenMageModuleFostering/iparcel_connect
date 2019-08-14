/**
 * Label Printing script for i-parcel shipping
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski
 */
var iparcel = {
	xhr: function (url, obj, async, cfunc){
		object=obj;
		if (XMLHttpRequest){
			xmlhttp=new XMLHttpRequest ();
		}else{
			xmlhttp=new ActiveXObject ("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=cfunc;
		xmlhttp.open("GET", url, async);
		xmlhttp.send();
	},
	
	print_label: function(url){
		this.xhr(url, null, true, function(){
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200 && xmlhttp.response.isJSON()){
				var response = xmlhttp.response.evalJSON();
				if (response.error){
					packaging.messages.show().innerHTML = response.message;
				}else if(response.ok){
					packaging.labelCreatedCallback(response);
				}
			}
		});
	}
}
