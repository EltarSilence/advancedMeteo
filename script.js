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
	var ts = $("#temp2").val();
	var t850 = $("#t850").val();
	var tds = $("#tds").val();
	var vv850 = $("#vv850").val();

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