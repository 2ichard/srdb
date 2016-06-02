
function onSegSelected(prefix) {
	var id = prefix + "Seg";
	var e = document.getElementById(id);
	var seg = e.options[e.selectedIndex].text;
	setCookie("seg", seg);
}

function selectCurrentSeg(prefix) {
	var seg = getCookie("seg");
	var id = prefix + "Seg";
	var sel = document.getElementById(id);
	for (i = 0; i < sel.length; i++)
		if (sel.options[i].text == seg) {
			sel.selectedIndex = i;
			break;
		}
}

function getSelectedSeg(prefix) {
	var sel = document.getElementById(prefix + 'Seg');
	return(sel.options[sel.selectedIndex].text);
}

function writeSegSelect(prefix) {
	var id = prefix + "Seg";
	document.writeln('<select name="seg" id="' + id + '" onChange="onSegSelected(\'' + prefix + '\')">');
    document.writeln('<option value="0" selected="selected">-</option>');
    document.writeln('<option value="1-04">1-04</option>');
    document.writeln('<option value="2-09">2-09</option>');
    document.writeln('<option value="3-50">3-50</option>');
    document.writeln('<option value="4-23">4-23</option>');
    document.writeln('<option value="5-69">5-69</option>');
    document.writeln('</select>');
	selectCurrentSeg(prefix);
	window.setInterval(function() { selectCurrentSeg(prefix); }, 1000);
}