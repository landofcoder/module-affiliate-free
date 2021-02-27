/* Credit Card Type Check */
function cardValidate()
{
	require([
        'jquery',
        ],function($){
        	$('#card_number').validateCreditCard(function(result) {
				var N=$(this).val();
				var C=$(this).attr("class");
				$(this).attr("class","");
				if(result && N.length>0){
					$(this).addClass(result.card_type.name);
					if(result.valid && result.length_valid && result.luhn_valid){
						$(this).addClass('valid');  
						$(this).attr("rel","1");
					}else{
						$(this).attr("rel","0");  
					}
				}else{
					$(this).removeClass(C);
				}
			});

    });
	
}