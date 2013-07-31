<?php

class instDates
    {
    function __construct($proposal)
        {
        $this->dates = array();
        $this->proposal = $proposal;
        }
    function output()
        {
        $s = '';
        sort($this->dates);
        $start = -999;
        $end = -999;
        foreach ($this->dates as $d)
            {
            $daynum = dateToDaynum($d);
            if ($start < 0)
                {
                $start = $daynum;
                $end = $daynum;
                }
            else if ($daynum == $end+1)
                $end = $daynum;
            else
                {
                if ($start == $end)
                    $s .= date('M j, ',strtotime(dayToDate($start)));
                else
                    $s .= date('M j - ',strtotime(dayToDate($start))) . date('M j, ',strtotime(dayToDate($end)));
                $start = $daynum;
                $end = $daynum;
                }
            }
        if ($start > -1)
            {
            if ($start == $end)
                $s .= date('M j',strtotime(dayToDate($start)));
            else
                $s .= date('M j - ',strtotime(dayToDate($start))) . date('M j',strtotime(dayToDate($end)));
            }
        return $s;
        }
    }
?>
