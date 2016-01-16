<?php 
require ("../include/oldphpfuncs.php");
require ("../include/class_funcs.php");                 // all static functions
require ("../include/class_alex_data.php");
require ("../include/class_alex_dental_globals.php");
session_start();
$_g = new alex_dental_globals();

$startdate = $_g->get('d', $_g->today);
$uid = $_g->sget('uid',0);
$home_office = $_g->sget('home_office', 'fh');
$access_level = $_g->sget('access_level', 9);
$_g->sys_admin = $_g->sget('sys_admin', 0);

$office = strtolower($_g->get('o', $home_office));
$qb_class = $_g->qb_classes[$office];
$details_type = $_g->get('t','');
$details = array();
if ( $_g->sys_admin == 1 || $uid ) { 
	echo '<strong>'.$startdate.'</strong>';
	if ($details_type=='q') { // qb
		echo '<br>Quickbooks<br><table border="1" class="fixed_table">';
		
        $srch_cat = " cat IN ( 'Fee for Service Income', 'Returned Checks', 'Refunds', 'Misc Income') ";

        $metric = 0.00;

        $plusminus_flag = -1;
    
        // simliar to globals, public function qb_details_ary($qb_class, $srch_cat, $startdate, $enddate, $plusminus_flag, $details = 0) {, but not grouped - need details
    
        $qb_tables_minus  = 'alexqb_bill';
        $qb_deposit_minus = 'alexqb_deposit';

        $result = array();
        $sql = "
            SELECT
                amt,
                cat,
                memo,
                name,
                view
            FROM
                acct_transactions
            WHERE
                view IN (':QBTABLES')
                AND
                dayt BETWEEN ':STARTDATE' AND ':ENDDATE'
                AND
                class = ':QB_CLASS'
                AND
                {$srch_cat}
            ORDER BY amt
        ";
        $binds = array(
            'QBTABLES'  => $qb_tables_minus,
            'STARTDATE' => $startdate,
            'ENDDATE'   => $startdate,
            'QB_CLASS'  => $qb_class
            
        );

        $result_minus = $_g->select($sql, $binds, 'alex');
        foreach ($result_minus as $k => $v) $result_minus[$k]['amt'] = -$plusminus_flag * $v['amt'];

        $sql = "
            SELECT
                abs_amt AS amt,
                cat,
                memo,
                name,
                view
            FROM
                (SELECT
                    id,
                    cat,
                    ABS(amt) as abs_amt,
                    memo,
                    name,
                    dayt,
                    view
                FROM
                    acct_transactions
                WHERE
                    view IN (':QBTABLES')
                    AND
                    dayt BETWEEN ':STARTDATE' AND ':ENDDATE'
                    AND
                    class = ':QB_CLASS'
                    AND
                    {$srch_cat} ) d
            ORDER BY amt
        ";
        $binds['QBTABLES'] = $qb_deposit_minus;
        $result_deposit = $_g->select($sql, $binds, 'alex');
        foreach ($result_deposit as $k => $v) $result_deposit[$k]['amt'] = -$plusminus_flag * $v['amt'];

        $result = $result_minus;
        $result = array_merge($result, $result_deposit);

        foreach ($result as $valid_row) {
            echo '<tr><td>'.substr($valid_row['view'], 7, 4).'</td><td>'.substr($valid_row['cat'],0,4).'</td><td>'.substr($valid_row['memo'],0,12).'</td><td>$'.number_format($valid_row['amt'],2).'</td><td>'.substr($valid_row['name'],0,12).'</td></tr>';
        }

		echo '</table>';
	}
	if ($details_type == 'c') { // collect
	    echo '<br><a href="acct_detail&startdate='.$startdate.'&office='.$office.'" target="_acctdetail">Collections</a><br><table border="1" class="fixed_table">';
		
        $sql = "
            SELECT
                patient_id,
                provider_id,
                ABS(amount) AS metric
            FROM 
                ledger
            WHERE 
                office_id IN (:OFFICES)
                AND 
                dayt BETWEEN ':STARTDATE' AND ':ENDDATE'
                AND
                entry_type = 3 -- types: 2 = prodadj, 1 = coladj, 3= collect
            ORDER BY 
                amount DESC
        ";

        $binds = array(
            'OFFICES'   => $_g->offices[$office]['id'],
            'STARTDATE' => $startdate,
            'ENDDATE'   => $startdate
        );
        
        $result = $_g->select($sql, $binds, 'alex');
        $_g->get_providers();
        
        foreach ($result as $valid_row) {
            echo '<tr><td><a href="patients&patid='.$valid_row['patient_id'].'&off='.$office.'" target="_patdetail">'.$valid_row['patient_id'].'</a></td><td>$'.number_format(-$valid_row['metric'],2).'</td><td>'.$_g->providers2rsc[$valid_row['provider_id']].'</td></tr>';
        }

		echo '</table>';
	}
	if ($details_type == 'a') { // adjs
		echo '<br>Adjustments<br><table border="1" class="fixed_table">';
        
        
        
         $sql = "
            SELECT
                leg.patient_id,
                CONCAT(first_name, ' ', last_name) as patname,
                leg.amount AS metric
            FROM 
                ledger leg LEFT JOIN patients pt ON pt.office_id = leg.office_id AND pt.patient_id = leg.patient_id
            WHERE 
                leg.office_id IN (:OFFICES)
                AND 
                leg.dayt BETWEEN ':STARTDATE' AND ':ENDDATE'
                AND
                leg.entry_type = 1 -- types: 2 = prodadj, 1 = coladj, 3= collect
            ORDER BY 
                amount DESC
        ";

        $binds = array(
            'OFFICES'   => $_g->offices[$office]['id'],
            'STARTDATE' => $startdate,
            'ENDDATE'   => $startdate
        );
        
        $result = $_g->select($sql, $binds, 'alex');
        $_g->get_providers();
        
        foreach ($result as $valid_row) {
            echo '<tr><td><a href="patients&patid='.$valid_row['patient_id'].'&off='.$office.'" target="_patdetail">'.$valid_row['patient_id'].'</a></td><td>$'.number_format($valid_row['metric'],2).'</td><td>';
            echo '&nbsp;</td><td>'.$valid_row['patname'].'</td></tr>';
        }
		
		echo '</table>';
	}
	
}
else echo 'error';

?>