<?php
$today = date("Y-m-d");
$yesterday = date("Y-m-d", mktime(0,0,0, date("m") , date("d")-1 , date("Y")));
$tomorrow = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d")+1, date("Y")));

$startofcurrentmonth = date("Y-m-d", mktime(0,0,0, date("m") , 1, date("Y")));
$endofcurrentmonth = date("Y-m-d", mktime(0,0,0, date("m")+1 , 0, date("Y")));
$endofnexttmonth = date("Y-m-d", mktime(0, 0, 0, date("m")+2 , 0, date("Y")));
$startoflastmonth = date("Y-m-d", mktime(0,0,0, date("m")-1 , 1, date("Y")));
$endoflastmonth = date("Y-m-d", mktime(0,0,0, date("m") , 0, date("Y")));
$startofyear = date("Y-m-d", mktime(0,0,0, 1 , 1, date("Y")));
$endofyear = date("Y-m-d", mktime(0,0,0, 12 , 31, date("Y")));
$start2mons = date("Y-m-d", mktime(0, 0, 0, date("m")-2 , 1, date("Y")));
$end2mons = date("Y-m-d", mktime(0,0,0, date("m")-1 , 0, date("Y")));
$start3mons = date("Y-m-d", mktime(0,0,0, date("m")-3 , 1, date("Y")));
$start4mons = date("Y-m-d", mktime(0, 0, 0, date("m")-4 , 1, date("Y")));
$start3mons_roll = date("Y-m-d", mktime(0, 0, 0, date("m")-3 , date("d")-2, date("Y")));
$start30days_roll = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-31, date("Y")));

$endof2monthsago = date("Y-m-d", mktime(0, 0, 0, date("m")-1 , 0, date("Y")));
$startofcurrentyear = date("Y-m-d", mktime(1,1,1,1,1, date("Y")));
$endofcurrentyear = date("Y-m-d", mktime(1,1,1,1 , 0, date("Y")+1));

$plus3mons = date("Y-m-d", mktime(0, 0, 0, date("m")+3 , 1, date("Y")));
$plus6mons = date("Y-m-d", mktime(0, 0, 0, date("m")+6 , 1, date("Y")));

// pay period "Epoch" = 1/13/2013
$ts1 = strtotime("2013-01-13");
$ts2 = strtotime($today);
$seconds_diff = $ts2 - $ts1;
$days_diff = floor($seconds_diff/3600/24);
$week_multiple = $days_diff - (floor($days_diff/14)*14);
if ($week_multiple >= 7) $last_saturday = strtotime("last Saturday",strtotime("last Saturday"));
else $last_saturday = strtotime("last Saturday");
$sunday_2nd_b4_last_saturday = strtotime("-13 days",$last_saturday);
$start2weeks_pay = date("Y-m-d", $sunday_2nd_b4_last_saturday);
$end2weeks_pay = date("Y-m-d", $last_saturday);

$date_ranger = get('date_ranger',$default_date_ranger);
$startdate = get('startdate',$startoflastmonth);
$startdatetime = strtotime($startdate);
$enddate = get('enddate',$endoflastmonth);
if ($enddate < $startdate ) $enddate = $startdate;

function get_quarter( $minus = NULL ) {
	$bingo = array(); //put correct begin dates and end dates in Bingo array    
	$quarters = array(
		1 => array( '01/01/', '03/31/'),
		2 => array( '04/01/', '06/30/'),
		3 => array( '07/01/', '09/30/'),
		4 => array( '10/01/', '12/31/')
	);
	if( $minus ) {
		$adjust = 3 * $minus;
		$today2 = strtotime($adjust . " months", time());
	}
	else $today2 = time();
	$year = date("Y", $today2);
	foreach($quarters as $key => $val) {
		$date1 = strtotime($val[0] . $year);
		$date2 = strtotime($val[1] . $year);
		if( ($today2 > $date1) && ($today2 < $date2)) {
			$bingo[1] = $quarters[$key][0] . $year;
			$bingo[2] = $quarters[$key][1] . $year;
		}
	}
	return $bingo;
}

$this_quarter = get_quarter( NULL );
$last_quarter = get_quarter( -1 );

$this_quarter_start = date("Y-m-d",strtotime($this_quarter[1]));
$this_quarter_end = date("Y-m-d",strtotime($this_quarter[2]));
$last_quarter_start = date("Y-m-d",strtotime($last_quarter[1]));
$last_quarter_end = date("Y-m-d",strtotime($last_quarter[2]));
		
$start_date_ranges = array(
	'Custom' => $startdate,
	'Today' => $today,
	'Yesterday' => $yesterday,
	'ThisMonth' => $startofcurrentmonth,
	'MTD' => $startofcurrentmonth,
	'LastMonth' => $startoflastmonth,
	'ThisQuarter' => $this_quarter_start,
	'QuarterToDate' => $this_quarter_start,
	'ThisYear' => $startofyear,
	'YTD' => $startofyear,
	'Past Year' => date("Y-m-d", mktime(0,0,0, date("m") , date("d"), date("Y")-1)),
	'LastFiscalQuarter' => $last_quarter_start,
	'LastFiscalYear' => date("Y-m-d", mktime(0,0,0, 1 , 1, date("Y")-1)),
	'Previous 3 Months' => $start3mons,
	'3 monthRolling' => date("Y-m-d", mktime(0,0,0, date("m")-3 , date("d"), date("Y"))),
	'6 monthRolling' => date("Y-m-d", mktime(0,0,0, date("m")-6 , date("d"), date("Y"))),
	'9 MonthRolling' => date("Y-m-d", mktime(0,0,0, date("m")-9 , date("d"), date("Y"))),
	'12 MonthRolling' => date("Y-m-d", mktime(0,0,0, date("m")-12 , date("d"), date("Y"))),
	'1st Quarter' => date("Y-m-d", mktime(0,0,0, 1, 1, date("Y"))),
	'2nd Quarter' => date("Y-m-d", mktime(0,0,0, 4, 1, date("Y"))),
	'3rd Quarter' => date("Y-m-d", mktime(0,0,0, 7, 1, date("Y"))),
	'4th Quarter' => date("Y-m-d", mktime(0,0,0, 10, 1, date("Y"))),
	'Pay period' => $start2weeks_pay
);
$end_date_ranges = array(
	'Custom' => $enddate,
	'Today' => $today,
	'Yesterday' => $yesterday,	
	'ThisMonth' => $endofcurrentmonth,
	'MTD' => $yesterday,
	'LastMonth' => $endoflastmonth,
	'ThisQuarter' => $this_quarter_end ,
	'QuarterToDate' => $yesterday,
	'ThisYear' => $endofyear,
	'YTD' => $yesterday,
	'Past Year' => $yesterday,
	'LastFiscalQuarter' => $last_quarter_end,
	'LastFiscalYear' => date("Y-m-d", mktime(0,0,0, 12 , 31, date("Y")-1)),
	'Previous 3 Months' => $endoflastmonth,
	'3 monthRolling' => $yesterday,
	'6 monthRolling' => $yesterday,
	'9 MonthRolling' => $yesterday,
	'12 MonthRolling' => $yesterday,
	'1st Quarter' => date("Y-m-d", mktime(0,0,0, 3,31, date("Y"))),
	'2nd Quarter' => date("Y-m-d", mktime(0,0,0, 6,30, date("Y"))),
	'3rd Quarter' => date("Y-m-d", mktime(0,0,0, 9,30, date("Y"))),
	'4th Quarter' => date("Y-m-d", mktime(0,0,0, 12,31, date("Y"))),
	'Pay period' => $end2weeks_pay
);

$date_ranger = get('date_ranger',$default_date_ranger);

if ($default_date_ranger != 'Custom') {
	$startdate = $start_date_ranges[$default_date_ranger];
	$enddate = $end_date_ranges[$default_date_ranger];
}
if (get('startdate','')!='') $startdate = get('startdate',$startdate);
if (get('enddate','')!='') $enddate = get('enddate',$enddate);

if ($enddate < $startdate ) $enddate = $startdate;

$sortcol = get('sortcol','adj_collections');
$sortdir = get('sortdir','SORT_DESC');

if ($uid) :
?>
<script type="text/javascript">
	function set_dates(date_ranger) {
		var start_date_ranges = [];
		var end_date_ranges = [];
		<?php foreach ($start_date_ranges as $key => $val) {
		echo 'start_date_ranges["' . $key . '"]="' . $val . '";';
		echo 'end_date_ranges["' . $key . '"]="' . $end_date_ranges[$key] . '";';
		}
		?>
		if (date_ranger != 'Custom') {
			document.getElementById('startdate').value = start_date_ranges[date_ranger];
			document.getElementById('enddate').value = end_date_ranges[date_ranger];
		}
	}
	
	function set_date_ranger_2_custom() {
		document.getElementById('date_ranger').value='Custom';
	}
</script>
<script type="text/javascript">
 function sort(sortcol,sortdir) {
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
</script>
<?php endif ?>
