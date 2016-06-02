
function onTechSelected(prefix) {
	var id = prefix + "Tech";
	var e = document.getElementById(id);
	var tech = e.options[e.selectedIndex].text;
	setCookie("tech", tech);
}

function selectCurrentTech(prefix) {
	var tech = getCookie("tech");
	var id = prefix + "Tech";
	var sel = document.getElementById(prefix + "Tech");
	for (i = 0; i < sel.length; i++)
		if (sel.options[i].text == tech) {
			sel.selectedIndex = i;
			break;
		}
}

function getSelectedTech(prefix) {
	var sel = document.getElementById(prefix + "Tech");
	return(sel.options[sel.selectedIndex].text);
}


function writeTechSelect(prefix) {
	var id = prefix + "Tech";
	document.writeln('<select name="tech" id="' + id + '" onChange="onTechSelected(\'' + prefix + '\')">');
    document.writeln('<option selected="selected">-</option>');
    document.writeln('<option>Atticus</option>');
    document.writeln('<option>Declan</option>');
    document.writeln('<option>Gino</option>');
    document.writeln('<option>Reggie</option>');
    document.writeln('<option>Barbara</option>');
	document.writeln('</select>');
	selectCurrentTech(prefix);
	window.setInterval(function() { selectCurrentTech(prefix); }, 1000);
}