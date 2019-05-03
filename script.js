var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var tmrw = dd+1;
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
var yyyy = today.getFullYear();
today = mm + '/' + dd + '/' + yyyy;
var url_sounding = "http://weather.uwyo.edu/cgi-bin/sounding?region=europe&TYPE=GIF%3ASKEWT&YEAR="+yyyy+"&MONTH="+mm+"&FROM="+dd+"12&TO="+tmrw+"12&STNM=16080"
//sounding?region=europe&TYPE=GIF%3ASKEWT&YEAR=2019&MONTH=05&FROM=0312&TO=0412&STNM=16080

//triggers

$('#snd').attr('src', url_sounding);

$('.mappat850').toggle();
$('.mappavv850').toggle();
$('.map_KO').toggle();

$('.mp_whiting').toggle();

$('.show').on('click', function(){
	var id = $(this).attr('id');
	$('.mappa' + id).toggle();
});

$('.show_whiting').on('click', function(){
	$('.mp_whiting').toggle();
});

$('.show_ko').on('click', function(){
	$('.map_KO').toggle();
});

function toRadians (angle) {
  return angle * (Math.PI / 180);
}

function toDegrees (angle) {
  return angle * (180 / Math.PI);
}

function clcSWEAT(){
	//SWEAT = 12Td850 + 20(T - 49) + 2*f850 + f500 + 125 (S + 0.2)
	//T = somma t850+td850 - 2*t500 Se T<49, allora 20(T-49) = 0

	/*
	Per quanto concerne il termine relativo allo shear del vento 125 (S + 0.2), bisogna far riferimento alla seguente tabella:
	- direzione del vento a 850 hPa compresa nell'intervallo 130° - 250°	
	- direzione del vento a 500 hPa compresa nell'intervallo 210° - 310°	
	- direzione del vento a 500 hPa meno direzione del vento a 850 hPa > 0 	
	- sia f850 che f500 >= 15 nodi
	
	E' sufficiente rispondere NO ad una delle precedenti condizioni per porre il termine 125(S + 0.2) = 0

	*/

	var t850 = $("#t850_SWEAT").val();
	var td850 = $("#td850_SWEAT").val();
	var t500 = $("#t500_SWEAT").val();
	var vv850 = $("#vv850_SWEAT").val();
	var vv500 = $("#vv500_SWEAT").val();
	var dir850 = $("#dv850_SWEAT").val();
	var dir500 = $("#dv500_SWEAT").val();
	
	var shear, sweat, VNT;
	td850 < 0 ? td850 = 0 : td850;
	var T = Number(t850)+Number(td850) - 2*Number(t500);
	var L = 20*(T - 49);
	T < 49 ? L=0 : L;

	if ((dir850 >= 130 && dir850 <= 250) && (dir500 >= 210 && dir500 <= 310) && ((dir500 - dir850) > 0) && (vv850 >= 15 && vv500 >= 15)){
		dir850 = toRadians(dir850);
		dir500 = toRadians(dir500);
		shear = Math.sin(dir500 - dir850);
		VNT = 125*(shear + 0.2);
	}
	else {
		VNT = 0;
	}
	debugger;
	sweat = 12*Number(td850) + Number(L) + 2*Number(vv850) + Number(vv500) + Number(VNT);
	$('#SWEAT').html(sweat);
}


function TEQ(tmp, dwp, prs){
	const A = 19.0785;
	const B = 4098.025
	const C = 237.3;
	const F = 18/28.96;
	return 7.4131 * Math.pow((1/Number(prs)), 0.29) * ((2480*Math.pow(Math.E, (A - B / (C + Number(dwp))))) / ( Number(prs) - Math.pow(Math.E, (A - B / (C + Number(dwp))) )) + Number(tmp) + 273.15);
}

function clcTEQ() {
	var tmp = $("#temp").val();
	var dwp = $("#dewp").val();
	var prs = $("#press").val();

	var teqK = TEQ(tmp, dwp, prs);
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

function getTemp1000(prs) {
	var deltaPrs = 1000 - prs;
	var Q = deltaPrs*8.22; //gv
	return t1000hpa = Q*0.56 / 100; //gv
}

function getDewp10000(t1000hpa, ts, tds){
	return t1000hpa - (ts - tds);
}

function clcKO() {

	var prs = $('#prsKO').val();
	var ts = $('#tempKOs').val();
	var tds = $('#dewpKOs').val(); 
	var t1000hpa;
	var d1000hpa;

	//correzione 1000hPa
	if (prs < 1000){
		t1000hpa = Number(ts) + getTemp1000(prs);
		d1000hpa = getDewp10000(t1000hpa, ts, tds);
	} else {
		t1000hpa = ts;
		d1000hpa = tds;
	}

	var t850 = $('#tempKO850').val();
	var d850 = $('#dewpKO850').val();
	var t700 = $('#tempKO700').val();
	var d700 = $('#dewpKO700').val();
	var t500 = $('#tempKO500').val();
	var d500 = $('#dewpKO500').val();

	var teq1000 = TEQ(t1000hpa, d1000hpa, 1000);
	var teq500 = TEQ(t500, d500, 500);
	var teq700 = TEQ(t700, d700, 700);
	var teq850 = TEQ(t850, d850, 850);
	

	var KO = (Number(teq500) + Number(teq700)) / 2 - (Number(teq850)+ Number(teq1000)) / 2;
	KO = Math.round(KO * 1000) / 1000;
	var color, instab;
	if (KO < -4){
		color = 'red';
		instab = 'Instabilita\' potenziale medio-alta';
	}
	if (KO >= -4 && KO < 2){
		color = 'yellow';
		instab = 'Instabilita\' potenziale medio-bassa';
	}
	if (KO >= 2){
		color = 'green';
		instab = 'Aria stabile';
	} 

	$('#ko').html(KO + ' (' + instab + ')');
	$('#ko').addClass(color);
}