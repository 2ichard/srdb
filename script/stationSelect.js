// JavaScript Document
function onStationSelected() {
	var station = document.getElementById("station").options[document.getElementById("station").selectedIndex].text;
	setCookie("station", station);
}

function selectCurrentStation() {
	var station = getCookie("station");
	var sel = document.getElementById('station');
	for (i = 0; i < sel.length; i++)
		if (sel.options[i].text == station) {
			sel.selectedIndex = i;
			break;
		}
}

function writeStationSelect() {
	document.writeln('<select name="station" id="station" onChange="onStationSelected()">');
    document.writeln('<option>A (west wall)</option>');
    document.writeln('<option>B (by window)</option>');
    document.writeln('<option>C (by door)</option>');
    document.writeln('</select>');
}