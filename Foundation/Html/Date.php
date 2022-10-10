<?php
namespace Foundation\Html;

use DateTime;

class Date
{
    /**
     * @var \DateTime
     */
    protected $date;

    public function setDate($date)
    {
        $this->date = new DateTime($date);

        return $this;
    }

    public function getDayOfMonth()
    {
        return $this->date->format('d');
    }

    public function getYear()
    {
        return $this->date->format('Y');
    }

    public function getYearAndMonthSpelledOut()
    {
        return \utf8_encode(strftime('%B, %Y', $this->date->format('U')));
    }

    public function getDayOfWeek()
    {
        return \utf8_encode(strftime('%A', $this->date->format('U')));
    }

    public function format($format)
    {
        return $this->date->format($format);
    }

    public static function dateUSA($data, $hora=false)
    {
        if($data == '') return "NULL";

        if($hora){
            $arrData = explode(" ", $data);

            $hora = explode(":",$arrData[1]);
            if(count($hora)){
                $hora = "{$arrData[1]}:00";
            }

            return implode("-",array_reverse(explode("/",$arrData[0])))." ".$hora;
        }

        if($data != '')
            return implode("-",array_reverse(explode("/",$data)));

    }

    public static function dateBRA($data, $horas=false)
    {
        if($data != '0000-00-00 00:00:00'){
            if(!$horas)
                return date("d/m/Y", strtotime($data) );
            else
                return date("d/m/Y H:m:s", strtotime($data) );
        }
        return null;
        //return implode("/",array_reverse(explode("-",$data)));
    }
}
