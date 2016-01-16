<?php 
//debugbreak();
error_reporting(0); // E_ALL);
ini_set('display_errors', 0);
require ("../include/class_funcs.php");                 // all static functions
require ("../include/class_alex_data.php");
require ("../include/class_alex_dental_globals.php");
session_start();
$_g = new alex_dental_globals();
$pagetitle = '';
?>
<!DOCTYPE html>
<html>
<head>
    <title><?=$pagetitle?> | <?=$_g->mailname?></title><meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
    <meta http-equiv="Content-Language" content="en-us" />
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		
	<link rel="shortcut icon" type="image/x-icon" href="px/favicon.png" /><link rel="icon" type="image/png" href="px/favicon.png" />
    <?php
        if ($_g->devmode) echo '<link rel="stylesheet" href="css/di.css" type="text/css" />';
        else echo '<link rel="stylesheet" href="css/di.min.css" type="text/css" />';
        ?>
    
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.gvChart-1.0.1.min.js"></script>
    <script type="text/javascript" language="javascript" src="js/funcs.min.js"></script>
</head>
<body onLoad="initclock();" >
<script type='text/javascript' src='js/simple_calendar_widget.min.js'></script>
<?php
// $_g->pages - all pages in here, subapges are just the ones whose parent is NOT "root"
if ($_g->uid) {
    foreach ($_g->pages as $curpage => $current) {
	    if ($curpage == "login" && $_g->uid) {
            $curpage = "logout"; 
            $current['title'] = "Logout";
        }

	    if ( $_g->sys_admin == 1 || $_g->access_level <= $current['access_level'] ) {
		    if ($current['parent'] == 'root' && $curpage != "logout") {
			    echo '<a ';
			    if ( $_g->curr_page["parent"] == $curpage  || $curpage  == $_g->page ) echo ' class="sc_up" ';
			    echo ' href="'.$curpage.'">'.$current['title'].'</a>';
		    }
	    }
    } // end foreach
} // end if uid
else {
	foreach ($_g->pages as $curpage => $current) {
		if ( $current["login"] == 0 && $current['access_level'] == 9 && $current['admin'] == 0 ) {
			if ($current['parent'] == 'root') {
				echo '<a ';
				if ( $_g->curr_page["parent"] == $curpage || $curpage == $_g->page ) echo ' class="sc_up" ';
				echo ' href="'.$curpage.'">'.$current['title'].'</a>';
			}
		}
	}
}
?>
        </div>
        <div id="headSubMen">
<?php
foreach ($_g->pages as $curpage => $current) {
	if ( $_g->sys_admin == 1 || $_g->access_level <= $current['access_level']) {
		if ( ($current['parent'] == $_g->page || $current['parent'] == $_g->curr_page["parent"]) && $current['parent'] != "root") {
			echo '<a ';
			if ($current['page'] == $_g->page) echo ' style="color:#000000; background-color:#8DC245;" ';
			echo ' href="'.$current['page'].'">'.$current['title'].'</a>';
		}
	}
}
?>              
        </div><!--  headSubMen -->
    </div><!--  headOut -->
    <div id="contOut" class="contOut">
		<div id="contIn" class="contIn">    
<?php
if ( $_g->page == '404' || $_g->page == 'logout') {
    include ("../pages/" . $_g->page . ".php");
} else if ( ($_g->sys_admin == 1 || $_g->access_level <= $_g->curr_page['access_level']) ) {
	$sqlstring='INSERT INTO log (uid, ip, page, url) VALUES ('.$_g->uid.',"'.$_SERVER['REMOTE_ADDR'].'","'.$_g->page.'","'.$_SERVER["REQUEST_URI"].'") ;';
    $_g->upsert($sqlstring, array(), 'sd');
    $_SESSION['login_time'] = time();
    if ( ( ($_g->curr_page['parent'] != 'admin' && $_g->page != 'admin')  ||  $_g->sys_admin == 1) ) {
        include ("../pages/" . $_g->page . ".php"); 
    } else { 
        include ("../pages/dash.php");
    }
    
}
?><br />&nbsp;
<div style=" position:absolute; left:990px; top:0px; float:right;"><a id="print_preview_btn" href="javascript:void(0);"><img src="px/icon_small_print.gif" width="21" height="22" alt="Print Preview" title="Print Preview" /></a></div>

<?php if (0 && $_g->uid == 139 && $_g->sys_admin == 1) { ?>
<div id="debugger_div" class="debug_small" style="position:absolute; left:2px; top:32px; background:#ffffcc; border:1px black solid; padding:2px; overflow:hidden;"><a id="debugger" href="javascript:void(0);"><img border="0" src="px/bug_32.gif" width="32" height="32" align="left" /></a><div>secs 2 load page :
<?php $_g->time_end = funcs::microtime_float();
echo number_format($_g->time_end - $_g->time_start, 4);?> secs<br />
</div></div>
<script language="javascript" type="text/javascript"> $('a#debugger').click(function() { 
	$('div#debugger_div').toggleClass("debug_big");
	$('input[type="hidden"]').each (function() { this.type = 'text'; });
	$('div').toggleClass("debug_redborder");
}); </script>
<?php } ?>
<div id="print_preview_div" style="position:absolute; left:2px; top:64px; background:none; border:none; padding:2px; overflow:hidden;">
<script language="javascript" type="text/javascript">
    $('a#print_preview_btn').click(function() {
	    $('#contIn').toggleClass("print_preview_blank");
	    $('div#headOut').toggle();
	    $('#cached_div').toggleClass("print_preview_blank");
	    $('body').toggleClass("print_preview_blank");
    });
</script>
</div>

</body>
</html>