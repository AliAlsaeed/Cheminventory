$('document').ready(function() {
	$('form[name=edit-item]').submit(function(evt) {
		evt.preventDefault();
		
		var id = $(this).data('id');
		var name = $('input[name=item-name]').val();
		var descrp = $('textarea[name=item-descrp]').val();
		var cat = $('select[name=item-category]').val();
		var molecularformula = $('input[name=item-molecularformula]').val();
        var cas = $('input[name=item-cas]').val();
        var hazard = $('input[name=item-hazard]').val();
        var owner = $('select[name=item-owner]').val();


		if(name == '') {
			alert('Please insert a name');
			return false;
		}
		
		$.post('edit-item.php', {
			'act':'1',
			'itemid':id,
			'name':name,
			'descrp':descrp,
			'cat':cat,
            'hazard':hazard,
            'molecularformula':molecularformula,
            'cas':cas,
			'owner':owner
     



		},function(data) {

			if(data == '1') {
				alert('Changes have been successfully made');
			}else{
				alert('Something went wrong. here ?  Please try again');
				return false;
			}
		});
	});
	
	$('textarea[name=item-descrp]').keyup(function(evt) {
		var count = $(this).val().length;
		var limit = 400;
		var val = $(this).val();
		var t = $(this);
		
		if(count > limit){
			t.val(val.substr(0,400));
			var dif = 0;
		}else
			var dif = limit-count;
		$('span.item-desc-left').html('Description ('+dif+' characters left):');
	});
	
	$('input[name=item-price]').keyup(function(evt) {
		var val = $(this).val();
		var re = /^\d*\.{0,1}\d{0,2}$/;
		var t = $(this);
		
		if((re.test(val)) == false)
			t.val(val.substr(0, val.length - 1));
		return;
	});
});