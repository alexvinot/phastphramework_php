<?php
class funcs {
    const
        TEMPLATES_DIR       = '../templates/',
        DS                  = DIRECTORY_SEPARATOR
    ;
    
    public static function microtime_float() 
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public static function percent($numerator, $denominator, $endchar = '%', $tag = '', $css_class = '') {
        $result = number_format(100.00 * self::safely_divide($numerator, $denominator), 2) . $endchar;
        $class  = '';
        if ($css_class) $class  = ' class="' . $css_class . '" ';
        if ($tag) {
            $result = '<' . $tag . $class . '>' . $result . '</' . $tag . '>';
        }
        return $result;
    }
    
    public static function money($money_in, $tag = '', $css_class = '') 
    {
        $result = '$' . number_format($money_in, 2);
        $class  = '';
        if ($css_class) $class  = ' class="' . $css_class . '" ';
        if ($tag) {
            $result = '<' . $tag . $class . '>' . $result . '</' . $tag . '>';
        }
        return $result;
    }

    public static function manage_money($money_in) 
    {
        return ('$' . number_format($money_in / 1000,1).'k');
    }

    public static function time_decimal2hhmm($time_decimal) 
    {
        $hrs = intval($time_decimal);
        $hrs_frac = $time_decimal - $hrs;
        $mins = str_pad(round($hrs_frac*60), 2, "0", STR_PAD_LEFT);
        return ($hrs . ':' . $mins);
    }

    public static function time2hrs($timestr) 
    {
        $times = explode(":",$timestr);
        $hrs = intval($times[0]);
        $mins = intval(($times[1]/60*100))/100;
        return ($hrs+$mins);
    }

    public static function strleft($s1, $s2) 
    { 
        return substr($s1, 0, strpos($s1, $s2)); 
    }

    public static function selfURL() { 
        $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : ""; 
        $protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s; 
        $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); 
        return $protocol . "://".$_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI']; 
    } 

    public static function titleCase($string) 
    { 
        $len = strlen($string); 
        $i = 0;
        $last = ""; 
        $new = ""; 
        $string = strtoupper($string); 
        while ($i < $len): 
            $char = substr($string,$i,1); 
            if (ereg("[A-Z]",$last)): 
                    $new.= strtolower($char); 
            else: 
                    $new.= strtoupper($char); 
            endif; 
            $last = $char; 
            $i++;
        endwhile; 
        return $new; 
    }
    
    public static function safely_divide($n, $d) {
        $result = 0;
        if (intval($d) != 0) {
            $result = ($n / $d);
        }
        return $result;
    }
    
    public static function format_phone($phone)
    {
        $phone = preg_replace("/[^0-9]/", "", $phone);
     
        if (strlen($phone) == 7)
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
        elseif (strlen($phone) == 10)
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
        else
            return $phone;
    }
    
    public static function get_report_template_csv($page)
    {
        $report_data = array();
        if (($handle = fopen(self::TEMPLATES_DIR . "reports_{$page}.csv", "r")) !== FALSE) {
            if (($indices = fgetcsv($handle, 2000, ',')) !== FALSE) {
                foreach ($indices as $index) $report_data["fieldname"][$index] = $index;
                while (($csvdata = fgetcsv($handle, 4000, ',')) !== FALSE) {
                    for ($c=0; $c < count($indices); $c++) $report_data[$csvdata[0]][$indices[$c]] = $csvdata[$c];
                }
            }
            fclose($handle);
        }
        
        return ($report_data);
    }

    public static function array2table($data, $uid_ok2delete) 
    {
        $table = '';
        if (count($data)) {
            $keys = array_keys(current($data));
            $link_delete = in_array('remove', $keys);
            $table.= '<table border="1" cellpadding="4" class="datatable"><thead><tr><th>&nbsp;' . 
                implode('&nbsp;</th><th>&nbsp;', $keys) . '&nbsp;</th></tr></thead><tbody>';
        
            foreach ($data as $row) {
                array_map('htmlentities', $row);
                if ($uid_ok2delete && $link_delete) {
                    $val = intval($row['remove']);
                    $row['remove'] = '<a href="javascript:delete_rec('.$val.');"> X '.$val.'</a>';
                }
                $table.= '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
            }
        $table.= '</tbody></table>';
        }
        return $table;
    }

    public static function array2select($id, $options, $label = '', $selected = '', $use_only_keys = 0) 
    {
        $select = $label;
        if (count($options)) {
            $select.= ' <select name="' . $id . '" id="' . $id . '">' ;
            foreach ($options as $k => $v) {
                if ($use_only_keys) $v = $k;
                $select.= '<option value="' . $v . '"';
                if ($v == $selected) $select.= ' selected="selected" ';
                $select.= '>' . $k . '</option>';
            }
            $select.= '</select>';
        }
        return $select;
    }
    
    public static function clean_array($array, $tokens) 
    {
        if (is_array($array) && !empty($array)) {
            foreach ($array as &$val) {
                if (is_array($val)) $val = self::clean_array($val, $tokens);
                else $val = trim(stripslashes(urldecode(strtr($val, $tokens))));
          }
       }
       return (array)$array;
    }
}
