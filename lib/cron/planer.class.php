<?php

namespace chilimatic\lib\cron;
/**
 * Class Planer
 *
 * @package chilimatic\lib\cron
 */
class Planer
{


    /**
     * the complete cron string before
     * the parsin process starts
     *
     * @var string
     */
    public $cron_string = null;


    /**
     * the current time -> for the calculation of the next and the
     * last interval
     *
     * @var int
     */
    public $current_time = null;


    /**
     * the year position [optional]
     *
     * @var string
     */
    public $cron_year = null;


    /**
     * the month position
     *
     * @var string
     */
    public $cron_month = null;


    /**
     * the weekday
     *
     * @var string
     */
    public $cron_day_of_week = null;


    /**
     * the day position
     *
     * @var string
     */
    public $cron_day = null;


    /**
     * the hour position
     *
     * @var string
     */
    public $cron_hour = null;


    /**
     * the minute position
     *
     * @var string
     */
    public $cron_minute = null;

    /**
     * calc time
     *
     * @var null
     */
    public $calc_time = null;


    /**
     * unixtimestamp next
     *
     * @var int
     */
    public $next_run = 0;


    /**
     * isodate next
     *
     * @var string
     */
    public $date_next_run = null;


    /**
     * complete data saved within
     *
     * @var array
     */
    protected $cron_array = array();


    /**
     * constructor initializes the first parsing
     *
     * @param $cron_string string           
     */
    public function __construct( $cron_string = null )
    {

        if ( empty($cron_string) ) return;
        
        $this->calc_cron((string) $cron_string);
    }


    public function __set_value( $val )
    {

        switch ( 1 )
        {
            case preg_match('/[\*]/', $val['range']) :
                // if there is no interval just take now
                if ( empty($val['interval']) )
                {
                    $this->{$val['p_name']} = (string) date((string) $val['placeholder']);
                    return true;
                }
                
                $val_string = (string) substr($val['p_name'], 5);

                // 100 iterations otherwise just screw it
                for ( $j = 0 ; $j <= 100 ; $j++ )
                {
                    $l_calc_date = (string) date($val['placeholder'], strtotime("+$j $val_string", $this->calc_time));
                    if ( (int) $l_calc_date % (int) $val['interval'] == 0 )
                    {
                        $this->{$val['p_name']} = (string) date((string) $val['placeholder'], strtotime("+$j $val_string", $this->calc_time));
                        return true;
                    }
                }
                
                break;
            default :
                // if it's no timerange with no specific
                // interval
                if ( empty($val['interval']) && strpos($val['range'], '-') === false )
                {
                    if ( date($val['placeholder'], $this->calc_time) <= $val['range'] )
                    {
                        $this->{$val['p_name']} = (string) $val['range'];
                    }
                }
                else
                {
                    
                    $position = (!is_array($val['range'])) ? explode('-', $val['range']) : explode('-', $val);
                    for ( $i = $position[0] ; $i <= $position[1] ; $i++ )
                    {
                        if ( !empty($val['interval']) && ($i % $val['interval']) != 0 ) continue;
                        
                        if ( date($val['placeholder'], $this->calc_time) <= $i )
                        {
                            $this->{$val['p_name']} = (string) $i;
                            break;
                        }
                    }
                    return true;
                }
                break;
        }
        return true;
    }


    /**
     * initialize calculation
     *
     * @param $cron_string string
     * @return bool|int
     */
    public function calc_cron( $cron_string = null )
    {

        if ( empty($cron_string) ) return false;
        
        // set the given cronstring
        $this->cron_string = $cron_string;
        // get the curren timestamp
        $this->current_time = strtotime(date('Y-m-d H:i:00', time()));
        $this->calc_time = strtotime(date('Y-m-d H:i:00', time()));
        
        $this->next_run = 0;
        $this->date_next_run = 0;
        
        // display cleanup
        $this->year = null;
        $this->cron_month = null;
        $this->cron_day = null;
        $this->cron_minute = null;
        $this->cron_hour = null;
        
        $count = count($tmp_array = explode(' ', $this->cron_string));
        
        if ( trim($this->cron_string) == '* * * * *' || trim($this->cron_string) == '* * * * * *' )
        {
            $this->next_run = strtotime('+1 minute', $this->current_time);
            $this->date_next_run = date('Y-m-d H:i:s', $this->next_run);
            
            // display cleanup
            $this->cron_minute = (strlen($this->cron_minute = date('i', $this->current_time)) < 2) ? "0$this->cron_minute" : $this->cron_minute;
            $this->cron_hour = (strlen($this->cron_hour = date('h', $this->current_time)) < 2) ? "0$this->cron_hour" : $this->cron_hour;
            $this->cron_day = (strlen($this->cron_day = date('d', $this->current_time)) < 2) ? "0$this->cron_day" : $this->cron_day;
            $this->cron_month = (strlen($this->cron_month = date('m', $this->current_time)) < 2) ? "0$this->cron_month" : $this->cron_month;
            $this->cron_year = (strlen($this->cron_year = date('Y', $this->current_time)) < 2) ? "0$this->cron_hour" : $this->cron_hour;
            $this->cron_array = array();
            
            return true;
        }
        
        // set the cron array
        $this->cron_array = array(
                                0 => array(
                                        'placeholder' => 'i', 
                                        'range' => array(
                                                        'range' => '*', 
                                                        'interval' => false
                                        ), 
                                        'p_name' => 'cron_minute'
                                ), 
                                1 => array(
                                        'placeholder' => 'H', 
                                        'range' => array(
                                                        'range' => '*', 
                                                        'interval' => false
                                        ), 
                                        'p_name' => 'cron_hour'
                                ), 
                                2 => array(
                                        'placeholder' => 'd', 
                                        'range' => array(
                                                        'range' => '*', 
                                                        'interval' => false
                                        ), 
                                        'p_name' => 'cron_day'
                                ), 
                                3 => array(
                                        'placeholder' => 'm', 
                                        'range' => array(
                                                        'range' => '*', 
                                                        'interval' => false
                                        ), 
                                        'p_name' => 'cron_month'
                                ), 
                                4 => array(
                                        'placeholder' => 'w', 
                                        'range' => array(
                                                        'range' => '*', 
                                                        'interval' => false
                                        ), 
                                        'p_name' => 'cron_weekday'
                                ), 
                                5 => array(
                                        'placeholder' => 'Y', 
                                        'range' => array(
                                                        'range' => '*', 
                                                        'interval' => false
                                        ), 
                                        'p_name' => 'cron_year'
                                )
        );
        
        // get the time range for the specific year / month
        for ( $i = 0 ; $i < $count ; $i++ )
        {
            $this->cron_array[$i]['range'] = $this->parse_position($tmp_array[$i]);
        }
        
        unset($tmp_array, $i);
        
        // since the year is optional
        if ( $count < 6 )
        {
            $this->cron_array[5]['range'] = $this->parse_position(date('Y'));
            $count++;
        }
        
        $next_up = false;
        
        // foreach ( $this->cron_array as $key => $val )
        for ( $i = 0 ; $i < $count ; $i++ )
        {
            if ( !empty($this->cron_array[$i]['range']) && !is_array($this->cron_array[$i]['range']['range']) )
            {
                $sub_range = array(
                                'range' => $this->cron_array[$i]['range']['range'], 
                                'interval' => $this->cron_array[$i]['range']['interval'], 
                                'placeholder' => $this->cron_array[$i]['placeholder'], 
                                'p_name' => $this->cron_array[$i]['p_name']
                );
                
                $this->__set_value($sub_range);
            }
            else
            {
                
                foreach ( $this->cron_array[$i]['range'] as $range )
                {
                    if ( !is_array($range) ) continue;
                    
                    if ( $next_up === true )
                    {
                        $val_string = (string) substr($this->cron_array[$i]['p_name'], 5);
                        $val = (string) date($this->cron_array[$i]['placeholder'], strtotime("+1 $val_string", $this->calc_time));
                        $date_string = '';
                        switch ( $i )
                        {
                            case 0 :
                                $date_string = (string) "Y-m-d H:$val:00";
                                break;
                            case 1 :
                                $date_string = (string) "Y-m-d $val:00:00";
                                break;
                            case 2 :
                                $date_string = (string) "Y-m-$val 00:00:00";
                                break;
                            case 3 :
                                $date_string = (string) "Y-$val-01 00:00:00";
                                break;
                            case 4 :
                                break;
                            case 5 :
                                $date_string = (string) "$val-01-01 00:00";
                                break;
                            default :
                                $date_string = (string) "Y-m-d H:i:s";
                                break;
                        }
                        // the calcuation date
                        $this->calc_time = (int) strtotime(date((string) $date_string));
                        
                        // reset the restart
                        $next_up = false;
                        
                        for ( $x = 0 ; count($this->cron_array) > $x ; $x++ )
                        {
                            $this->{$this->cron_array[$x]['p_name']} = null;
                        }
                        // cleanup useless vars
                        unset($x, $val, $val_string, $date_string);
                        // reset to the first field
                        $i = -1;
                        // start from beginning break this loop
                        break;
                    }
                    
                    // go through all subranges
                    foreach ( $range as $sub_range )
                    {
                        $sub_range['placeholder'] = (string) $this->cron_array[$i]['placeholder'];
                        $sub_range['p_name'] = (string) $this->cron_array[$i]['p_name'];
                        if ( !empty($this->{$this->cron_array[$i]['p_name']}) ) break;
                        $this->__set_value((array) $sub_range);
                    }
                    
                    // check if a value has been set
                    if ( !isset($this->{$this->cron_array[$i]['p_name']}) )
                    {
                        $next_up = true;
                    }
                    unset($sub_range);
                }
                unset($range);
            }
        }
        // display cleanup
        $this->cron_minute = (strlen($this->cron_minute) < 2) ? "0$this->cron_minute" : $this->cron_minute;
        $this->cron_hour = (strlen($this->cron_hour) < 2) ? "0$this->cron_hour" : $this->cron_hour;
        $this->cron_day = (strlen($this->cron_day) < 2) ? "0$this->cron_day" : $this->cron_day;
        $this->cron_month = (strlen($this->cron_month) < 2) ? "0$this->cron_month" : $this->cron_month;
        // set the next possible timestamp
        $this->date_next_run = "{$this->cron_year}-{$this->cron_month}-{$this->cron_day} {$this->cron_hour}:{$this->cron_minute}:00";
        $this->next_run = strtotime($this->date_next_run);
        
        // clear cron array
        $this->cron_array = array();
        
        return $this->next_run;
    }


    /**
     * returns an array with 2 positions ->
     * range which can be an array or a
     * specific time positions
     *
     * @param $string string           
     *
     * @return array
     */
    public function parse_position( $string )
    {

        if ( empty($string) ) return array();
        
        // get the timestamp for this year
        $time_array = array(
                            'range' => '*', 
                            'interval' => false
        );
        
        if ( $string == '*' )
        {
            return $time_array;
        }
        else if ( preg_match('/^(\d{4})$/', $string) )
        {
            $time_array['range'] = $string;
            return $time_array;
        }
        else
        {
            $time_array['range'] = array();
            switch ( true )
            {
                // first check if it's a list
                case (strpos($string, ',') !== false) :
                    $multi_pos = explode(',', $string);
                    $interval = false;
                    foreach ( $multi_pos as $pos )
                    {
                        switch ( true )
                        {
                            // get the interval first than proceed to the next
                            case (($pos_pos = strpos($pos, '/')) !== false) :
                                $interval = explode('/', $pos);
                                $pos = substr($pos, 0, -(strlen($pos) - $pos_pos));
                            // check the ranges
                            default :
                                $time_array['range'][] = array(
                                                            'range' => $pos, 
                                                            'interval' => $interval[1]
                                );
                                $interval = false;
                                break;
                        
                        }
                    }
                    break;
                // check if it's a range
                case (strpos($string, '-') !== false) :
                    // get the interval first than proceed
                    if ( ($pos_pos = strpos($string, '/')) !== false )
                    {
                        $interval = explode('/', $string);
                        $time_array['interval'] = $interval[1];
                        $string = substr($string, 0, -$pos_pos);
                    }
                    $time_array['range'] = $string;
                    break;
                // check if it's an interval
                case (strpos($string, '/') !== false) :
                    $multi_pos = explode('/', $string);
                    $time_array['range'] = $multi_pos[0];
                    $time_array['interval'] = $multi_pos[1];
                    break;
            }
        }
        return $time_array;
    }

}
?>