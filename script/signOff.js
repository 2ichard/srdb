function updateSignOffState(proc, sec, step, id) {
	var request = new XMLHttpRequest();
	var url;
	url = new String("../lib/SRDb.php?dbcmnd=is_signed_off");
	url += "&seg=" + getCookie("seg") + "&proc=" + proc + "&sec=" + sec + "&step=" + step;
	request.onreadystatechange = function() {
		if (request.readyState == 4) {
			if (request.status == 200) {
				if (request.responseText == "true") {
					showSignedOff(true, id);
					updateSignedOffBy(proc, sec, step, id);
					updateSignOffTime(proc, sec, step, id);
				}
				else {
					//alert("not signed off");
					showSignedOff(false, id);
				}
			}
		}
	}
	request.open("GET", url, true);
	request.send(null);
}

function updateSignedOffBy(proc, sec, step, id) {
	var request = new XMLHttpRequest();
	var url;
	url = new String("../lib/SRDb.php?dbcmnd=signed_off_by");
	url += "&seg=" + getCookie("seg") + "&proc=" + proc + "&sec=" + sec + "&step=" + step;
	request.onreadystatechange = function() {
		if (request.readyState == 4) {
			if (request.status == 200) {
				document.getElementById(id + "Who").innerHTML=request.responseText;
			}
		}
	}
	request.open("GET", url, true);
	request.send(null);
}

function updateSignOffTime(proc, sec, step, id) {
	var request = new XMLHttpRequest();
	var url;
	url = new String("../lib/SRDb.php?dbcmnd=sign_off_time");
	url += "&seg=" + getCookie("seg") + "&proc=" + proc + "&sec=" + sec + "&step=" + step;
	request.onreadystatechange = function() {
		if (request.readyState == 4) {
			if (request.status == 200) {
				document.getElementById(id + "Time").innerHTML=request.responseText;
			}
		}
	}
	request.open("GET", url, true);
	request.send(null);
}

function showSignedOff(isSignedOff, id) {
	
	//alert(id);
	
	var signOffE = document.getElementById(id + "SignOff");
	var signedOffE = document.getElementById(id + "SignedOff");

	if (isSignedOff) {
		// has already been signed off
		signedOffE.style.display = "block";
		signedOffE.style.visibility = "visible";
		signOffE.style.display = "none";
		signOffE.style.visibility = "hidden";
	}
	else {
		// has not been signed off
		signedOffE.style.display = "none";
		signedOffE.style.visibility = "hidden";
		signOffE.style.display = "block";
		signOffE.style.visibility = "visible";
	}
}

function signOffOnStep(proc, sec, step, id) {
	var who = getCookie("tech");
	if (!confirm("You are signing-off on\nProcedure " + proc + ", Section " 
			+ sec + ", Step " + step + "\nfor Segment " 
			+ getCookie("seg") + " as " + who)) {
		return;
	}

	var request = new XMLHttpRequest();
	var url;
	url = new String("../lib/SignOff.php?cmnd=save_signoff&type=step");
	
	url = url + "&seg=" + getCookie("seg") + "&who=" + getCookie("tech");
	url += url + "&proc=" + proc + "&sec=" + sec + "&step=" + step;	
	
	request.onreadystatechange = function() {
		if (request.readyState == 4) {
			if (request.status == 200)
				updateSignOffState(proc, sec, step, id);
		}
	}
	request.open("GET", url, true);
	request.send(null);
}
