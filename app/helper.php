<?php

use Illuminate\Support\Facades\Storage;

// function fileUrl($src){

    // if(env('APP_ENV') == "local"){
        // return Storage::disk('s3')->temporaryUrl($src,'+2 minutes');
    // }else{
    //     return url($src);
    // }

//     var_dump(env('APP_ENV'));
// }

function limit_title($text, $limit) {
    if (str_word_count($text, 0) > $limit) {
        $words  = str_word_count($text, 2);
        $pos    = array_keys($words);
        $text   = '';
        if (strlen($text)>5){
            $text = substr($text, 0, $pos[$limit], "utf-8");
        }
    }
    $text = mb_substr(strip_tags($text),0,$limit, 'UTF-8').' ... ';
    return $text;
}
function sendSms($to,$content){
    $soap = new SoapClient("http://idehpayam.ir/webservice/send.php?wsdl");
    $soap->Username="912545635";
    $soap->Password="2580";
    $soap->fromNum="+985000121505";
    $soap->toNum=$to;
    $soap->Content = $content;
    $soap->Type = '0';
    $soap->SendSMS($soap->fromNum,$soap->toNum,$soap->Content,$soap->Type,$soap->Username,$soap->Password);
}
function persian($string) {
    $persian_num = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $latin_num = range(0, 9);

    $string = str_replace($latin_num, $persian_num, $string);

    return $string;
}
function slug($string){

    $string=urldecode($string);


    $total_count=strlen($string);
    $txt="";
    $num=0;

    preg_replace("/[^[:alnum][:space]]/ui", '', $string);
    $string=str_replace(' ', '-', $string);
    $string=str_replace('!', '-', $string);
    $string=str_replace('?', '-', $string);
    $string=str_replace('%', '-', $string);
    $string=str_replace('-', '-', $string);
    $string=str_replace('"', '-', $string);
    $string=str_replace('\'', '-', $string);
    $string=str_replace('__', '-', $string);
    if(substr($string, 0, 1)=="-"){
        $string=substr($string, 1);
    }
    if(Trim(substr($string, ($total_count-2)))=="-"){
        $string=substr($string, 0, ($total_count-2));
    }

    $string=trim($string,'-');

    return  $string;


}

function clear($value){
    return addslashes(htmlspecialchars(trim($value)));
}


/*  date function  */


function div($a,$b) {
    return (int) ($a / $b);
}

function gregorian_to_jalali ($g_y, $g_m, $g_d)
{
    $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);





    $gy = $g_y-1600;
    $gm = $g_m-1;
    $gd = $g_d-1;

    $g_day_no = 365*$gy+div($gy+3,4)-div($gy+99,100)+div($gy+399,400);

    for ($i=0; $i < $gm; ++$i)
        $g_day_no += $g_days_in_month[$i];
    if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
        /* leap and after Feb */
        $g_day_no++;
    $g_day_no += $gd;

    $j_day_no = $g_day_no-79;

    $j_np = div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */
    $j_day_no = $j_day_no % 12053;

    $jy = 979+33*$j_np+4*div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */

    $j_day_no %= 1461;

    if ($j_day_no >= 366) {
        $jy += div($j_day_no-1, 365);
        $j_day_no = ($j_day_no-1)%365;
    }

    for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
        $j_day_no -= $j_days_in_month[$i];
    $jm = $i+1;
    $jd = $j_day_no+1;

    return array($jy, $jm, $jd);
}

function jalali_to_gregorian($j_y, $j_m, $j_d)
{
    $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);



    $jy = $j_y-979;
    $jm = $j_m-1;
    $jd = $j_d-1;

    $j_day_no = 365*$jy + div($jy, 33)*8 + div($jy%33+3, 4);
    for ($i=0; $i < $jm; ++$i)
        $j_day_no += $j_days_in_month[$i];

    $j_day_no += $jd;

    $g_day_no = $j_day_no+79;

    $gy = 1600 + 400*div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
    $g_day_no = $g_day_no % 146097;

    $leap = true;
    if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
    {
        $g_day_no--;
        $gy += 100*div($g_day_no,  36524); /* 36524 = 365*100 + 100/4 - 100/100 */
        $g_day_no = $g_day_no % 36524;

        if ($g_day_no >= 365)
            $g_day_no++;
        else
            $leap = false;
    }

    $gy += 4*div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
    $g_day_no %= 1461;

    if ($g_day_no >= 366) {
        $leap = false;

        $g_day_no--;
        $gy += div($g_day_no, 365);
        $g_day_no = $g_day_no % 365;
    }

    for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
        $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
    $gm = $i+1;
    $gd = $g_day_no+1;

    return array($gy, $gm, $gd);
}
function TsToDateTime($timestamp){





    $year=date("Y", $timestamp);

    $month=date("m", $timestamp);

    $day=date("d", $timestamp);

    $hour=date("H", $timestamp);

    $minute=date("i", $timestamp);

    $second=date("s", $timestamp);
    $week_day=date("l", $timestamp);

    $weekday=array("Monday"=>"دوشنبه","Tuesday"=>"سه شنبه","Wednesday"=>"چهارشنبه","Thursday"=>"پنجشنبه","Friday"=>"جمعه","Saturday"=>"شنبه","Sunday"=>"یکشنبه");
    $month_name=array('فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند');

    $irantimestamp = $timestamp+16200;

    $ihour=date("H", $irantimestamp);

    $iminute=date("i", $irantimestamp);

    $isecond=date("s", $irantimestamp);



    $shamsi=gregorian_to_jalali(date("Y", $timestamp), date("m", $timestamp), date("d", $timestamp));

    $shamsi_y=$shamsi['0'];

    $shamsi_m=$shamsi['1'];

    $shamsi_d=$shamsi['2'];





    $data["Timestamp"]=$timestamp;

    $data["MiladiYear"]=$year;

    $data["MiladiMonth"]=$month;

    $data["MiladiDay"]=$day;

    $data["ShamsiYear"]=$shamsi_y;

    $data["ShamsiMonth"]=$shamsi_m;

    $data["ShamsiDay"]=$shamsi_d;

    $data["WorldHour"]=$hour;

    $data["WorldMinute"]=$minute;

    $data["WorldSecond"]=$second;

    $data["IranHour"]=$ihour;

    $data["IranMinute"]=$iminute;

    $data["IranSecond"]=$isecond;
    $data["WeekDay"]=$weekday[$week_day];
    $data["month_name"]=$month_name[$shamsi_m-1];



    return $data;





}
function DateTimeToTs($shamsiyear, $shamsimonth, $shamsiday, $ihour, $iminute, $isecond){
    $miladi=jalali_to_gregorian("$shamsiyear", "$shamsimonth", "$shamsiday");
    $tmm=mktime($ihour,$iminute,$isecond,$miladi['1'],$miladi['2'],$miladi['0']);
    return $tmm-16200;


}

function fulldate($timestamp){
    $dt=TsToDateTime($timestamp);
    // $dt["IranWeekDay"]." ".
    $dt=$dt["ShamsiYear"]."/".$dt["ShamsiMonth"]."/".$dt["ShamsiDay"]."&nbsp; ".$dt["IranHour"].":".$dt["IranMinute"];
    return $dt;
}
function justdate($timestamp){
    $dt=TsToDateTime($timestamp);
    $dt=$dt["ShamsiYear"]."/".$dt["ShamsiMonth"]."/".$dt["ShamsiDay"];
    return $dt;
}
function wweek($timestamp){
    $dt=TsToDateTime($timestamp);
    $dt=$dt['WeekDay'].' '.$dt['ShamsiDay'].' '.$dt['month_name'].' '.$dt['ShamsiYear'];;
    return $dt;
}


/* end date function */



?>
