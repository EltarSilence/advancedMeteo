//triggers

$('.mappat850').toggle();
$('.mappavv850').toggle();

$('.mp_whiting').toggle();

$('.show').on('click', function(){
	var id = $(this).attr('id');
	$('.mappa' + id).toggle();
});

$('.show_whiting').on('click', function(){
	$('.mp_whiting').toggle();
});

function clcTEQ() {
	var tmp = $("#temp").val();
	var dwp = $("#dewp").val();
	var prs = $("#press").val();

	const A = 19.0785;
	const B = 4098.025
	const C = 237.3;
	const F = 18/28.96;

	var teqK = 7.4131 * Math.pow((1/Number(prs)), 0.29) * ((2480*Math.pow(Math.E, (A - B / (C + Number(dwp))))) / ( Number(prs) - Math.pow(Math.E, (A - B / (C + Number(dwp))) )) + Number(tmp) + 273.15);
	$('#resultK').html(Math.round(teqK * 1000) / 1000);
	$('#resultC').html(Math.round((teqK - 273.15) * 1000) / 1000);
}

function clcHnC() {
	var tmp = $("#temp1").val();
	var dwp = $("#dewp1").val();

	var height = 125 * (Number(tmp) - Number(dwp));
	var height_feet = height * 3.28;
	$('#result1h').html(height + ' metri (' + height_feet + ' ft)');
}

function clcFogSI() {
	$('#fsi').removeClass(function() {
  		return $('#fsi').attr('color');
  	});
	var ts = $('#temp2').val();
	var t850 = $('#t850_in').val();
	var tds = $('#tds').val();
	var vv850 = $('#vv850_in').val();

	var color, rischio;
	var fsi = 4*Number(ts) - 2* (Number(t850) + Number(tds)) + Number(vv850);

	if (fsi < 31){
		color = 'red';
		rischio = 'Elevato';
	}
	if (fsi >= 31 && fsi < 55){
		color = 'yellow';
		rischio = 'Moderato';
	}
	if (fsi >= 55){
		color = 'green';
		rischio = 'Basso';
	}
	$('#fsi').html(fsi + '<br> (rischio '+rischio+')');
	$('#fsi').addClass(color);
}

function clcWhiting() {
	$('#whiting').removeClass(function() {
  		return $('#whiting').attr('color');
  	});
	var t850 = $('#t850_w').val();
	var t500 = $('#t500_w').val();
	var d850 = $('#d850_w').val();
	var t700 = $('#t700_w').val();
	var d700 = $('#d700_w').val();

	var whiting = (Number(t850) - Number(t500)) + Number(d850) - (Number(t700) - Number(d700));

	var color, rischio, instab;
	if (whiting < 15){
		color = 'green';
		rischio = 'basso';
		instab = 'stabile';
	}
	if (whiting >= 15 && whiting < 20){
		color = 'yellow';
		rischio = 'moderato';
		instab = 'leggermente instabile';
	}
	if (whiting >= 20 && whiting < 30){
		color = 'red';
		rischio = 'elevato';
		instab = 'instabile';
	}
	if (whiting >= 30){
		color = 'purple';
		rischio = 'altissimo';
		instab = 'potentemente instabile';
	}

	$('#whiting').html('Tra ' + (whiting-5)+' e '+ (whiting+5)+' ('+whiting+') '+'<br>'+'Rischio: '+rischio+', aria '+instab);
	$('#whiting').addClass(color);
}