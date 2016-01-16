function getRefToDiv(divID) {
	if( document.layers ) { return document.layers[divID+'C'].document.layers[divID]; };
	if( document.getElementById ) {return document.getElementById(divID); };
	if( document.all ) { return document.all[divID]; };
	if( document[divID+'C'] ) { return document[divID+'C'].document[divID]; };
	return false;
}


var timerID = null;
var timerRunning = false;
var id, pause = 0, position = 0;
var refresher = 0;

function initclock(){
	// var calwidget = document.getElementById(divID);
	// calwidget.style.visibility = 'hidden';
var now = new Date();
var hours = now.getHours();
var minutes = now.getMinutes();
var seconds = now.getSeconds();
document.clock.tsecsnow.value = hours * 3600 + minutes  * 60 + seconds;
document.clock.tsecsdf.value = document.clock.tsecsoa.value - document.clock.tsecsnow.value;
startclock()
}

function stopclock (){
        if(timerRunning)
                clearTimeout(timerID);
        timerRunning = false;
}

function showtime () {
 refresher++;
 if (refresher > 6000) location.reload(true);
 var now = new Date();
 var hours = now.getHours();
 var minutes = now.getMinutes();
 var seconds = now.getSeconds();
 var ttime = (hours * 3600 + minutes  * 60 + seconds) - (-document.clock.tsecsdf.value);
 hours = parseInt(ttime / 3600,10);
 ttime = ttime - hours * 3600;
 minutes = parseInt(ttime /60,10);
 ttime = ttime - minutes * 60;
 seconds  = ttime;
 var timeValue = "" + ((hours >12) ? hours -12 :hours);
 timeValue += ((minutes < 10) ? ":0" : ":") + minutes;
 timeValue += ((seconds < 10) ? ":0" : ":") + seconds;
 timeValue += (hours >= 12) ? " pm" : " am";
 document.clock.face.value = timeValue;
 timerID = setTimeout("showtime()",1000);
 timerRunning = true;
}

function startclock () {
 stopclock();
 showtime();
}

function shohyde(id) {
	var tdiv = getRefToDiv(id); 
	if (tdiv.style.visibility == "visible") {
		tdiv.style.visibility = "hidden";
		tdiv.style.height = "2px";
		tdiv.style.overflow = "hidden";
		eval ("document." + id + "_btn.src='px/plus.gif'");
		eval ("document." + id + "_btn.alt='Open section'");
	}
	else {
		tdiv.style.visibility = "visible";
		tdiv.style.height = "auto"; // 272px
		tdiv.style.overflow = "visible";
		eval ("document." + id + "_btn.src='px/minus.gif'");
		eval ("document." + id + "_btn.alt='Close section'");
	}
}

function set_date_ranger_2_custom() {
    document.getElementById('date_ranger').value='Custom';
}

 function sort(sortcol, sortdir) {
     document.getElementById('sortcol').value = sortcol;
     document.getElementById('sortdir').value = sortdir;
     document.form_search_date.submit();
 }
 
 function back1month() {
     document.getElementById('startdate').value = document.getElementById('dummy_prevmonth_start').value;
     document.getElementById('enddate').value = document.getElementById('dummy_prevmonth_end').value;
     document.form_search_date.submit();
 }
 function forward1month() {
     document.getElementById('startdate').value = document.getElementById('dummy_nextmonth_start').value;
     document.getElementById('enddate').value = document.getElementById('dummy_nextmonth_end').value;
     document.form_search_date.submit();
 }
