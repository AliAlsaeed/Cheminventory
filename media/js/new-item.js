$('document').ready(function() {
	$('form[name=new-item]').submit(function(evt) {
		evt.preventDefault();
		
		var name = $('input[name=item-name]').val();
        var chemicalstate = $('select[name=chemicalstate]').val();
		var descrp = $('textarea[name=item-descrp]').val();
		var safety = $('textarea[name=item-safety]').val();
		var cat = $('select[name=item-category]').val();
		var owner = $('select[name=item-owner]').val();
		var qty = $('input[name=item-qty][id^=' + chemicalstate +']').val();
        var molecularformula = $('input[name=molecularformula]').val();
        var cas = $('input[name=cas]').val();
        var supplier = $('input[name=supplier]').val();
        var datereceived = $('input[name=datereceived]').val();
        var expierydate = $('input[name=expierydate]').val();
        
        var location = $('input[name=location]').val();
        var locationdetail = $('input[name=locationdetail]').val();
        var hazard = $('select[name=hazard]').val();

        
		
		if(name == '') {
			alert('Please insert an item name');
			return false;
		}
		if(cat == 'no') {
			alert('You need to create a category');
			location.href='new-category.php';
			return false;
		}
        
        if (chemicalstate === 'solid') {
            var solidA = parseFloat($('#solid-a').val());
            var solidB = parseFloat($('#solid-b').val());
            var tarewight = solidA - solidB;
        }
        else if (chemicalstate === 'liquid') {
            var liquidA = parseFloat($('#liquid-a').val()); // total weight in g
            var liquidB = parseFloat($('#liquid-b').val()); // qty (volume in mL)
            var density = parseFloat($('#liquid-c').val()); // density in g/mL

            
            
            var liquidBTimesDensity = (liquidB * density);
            var tarewight = liquidA - liquidBTimesDensity;
            density = parseFloat(density).toPrecision(density.length);


        }
        else {
            var tarewight = 0;
        }

		
		$.post('new-item.php', {
			'act':'1',
			'name':name,
			'descrp':descrp,
			'safety':safety,
			'cat':cat,
			'owner':owner,
			'qty':qty,
			'molecularformula':molecularformula,
			'cas':cas,
			'supplier':supplier,
			'datereceived':datereceived,
			'expierydate':expierydate,
			'tarewight':tarewight,
			'location':location,
			'locationdetail':locationdetail,
			'chemicalstate':chemicalstate,
			'hazard':hazard,
            'density': density
            
            
		}, function(data) {
			if(data == '1') {
				alert('Chemical successfully created');
				window.location.href = 'new-item.php';
			}else{
				alert('Something went wrong Here new item php');
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
	
	$('input[name=item-qty]').keyup(function(evt) {
		var val = $(this).val();
		var re = /^\d+$/;
		var t = $(this);
		
		if((re.test(val)) == false)
			t.val(val.substr(0, val.length - 1));
		return;
	});
	
});