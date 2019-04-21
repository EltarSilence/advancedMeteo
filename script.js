function clcTEQ() {
	var tmp = document.getElementById("temp").value;
	var dwp = document.getElementById("dewp").value;
	var prs = document.getElementById("press").value;

	const A = 19.0785;
	const B = 4098.025
	const C = 237.3;
	const F = 18/28.96;

	var teqK = 7.4131 * Math.pow((1/Number(prs)), 0.29) * ((2480*Math.pow(Math.E, (A - B / (C + Number(dwp))))) / ( Number(prs) - Math.pow(Math.E, (A - B / (C + Number(dwp))) )) + Number(tmp) + 273.15);
	document.getElementById('resultK').innerHTML = Math.round(teqK * 1000) / 1000;
	document.getElementById('resultC').innerHTML = Math.round((teqK - 273.15) * 1000) / 1000;
}