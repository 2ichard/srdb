
function getDateTime() {
	var montharray=new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep", "Oct","Nov","Dec");
	var now = new Date(); 
	var datetime = montharray[now.getMonth()] + '&nbsp;' + now.getDate() + ', ' + now.getFullYear(); 
	datetime += ' '+now.getHours()+':'+now.getMinutes()+':'+now.getSeconds(); 
	return(datetime);
}

function setCookie(name, value) {
	//alert("setCookie(" + name + ", " + value + ")");
	var d = new Date();
	d.setTime(d.getTime() + (24 * 60 * 60 * 1000));
	var expires = "expires=" + d.toGMTString();
	document.cookie = name + "=" + value + "; " + expires + "; path=/optics";
}

function getCookie(name) {
	var name = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
  		var c = ca[i].trim();
  		if (c.indexOf(name) == 0) {
			//alert("getCookie(" + name + ") = " + c.substring(name.length, c.length));
			return c.substring(name.length, c.length);
		}
  	}
	return "";
}

function openProcedure(type, num) {
	switch (type) {
		case "Segment":
			window.location.href = "ProcedurePage.php?num=" + num + "&type=" + type + 
				"&content=" + getCookie("content");
			break;
		case "General":
			// window.location.href = "GeneralProcedurePage.php?num=" + num + "&content=" + getCookie("content");
			window.open("GeneralProcedurePage.php?num=" + num + "&content=" + getCookie("content"), "_blank");
			break;
		case "Batch":
			window.location.href = "BatchProcedurePage.php?num=" + num + "&content=" + getCookie("content");
			break;
		default:
			alert("Unknown procedure type: " + type);
			break;
	}
}

function send_req(url, status_id) {
	alert(url + ", " + status_id);
	var request = new XMLHttpRequest();
	var url;
	document.getElementById(status_id).innerHTML="";
	request.onreadystatechange = function() {
		if (request.readyState == 4) {
			if (request.status == 200) {
				document.getElementById(status_id).innerHTML=request.responseText;
			}
		}
	}
	request.open("GET", url, true);
	request.send(null);
}

function styleSet(title) {
  var i, a
  var o;
  for(i=0; (a = document.getElementsByTagName("link")[i]); i++) 
    if (a.getAttribute("rel").indexOf("style") != -1 && a.getAttribute('data-title') ) {
      if (a.getAttribute('data-title') == title) 
        o = a

      a.setAttribute("rel", "alternate stylesheet"); 
      a.setAttribute("title", a.getAttribute('data-title'));
      a.disabled = true
  }

  o.setAttribute("title", undefined); 
  o.setAttribute("rel", "stylesheet"); 
  o.disabled = false
  //app.cookieCreate("style", title, 365);
}

function changeStyle(title) {
    setCookie("css_data_title", title);
    styleSet(title);
}