<?php
include_once("./_common.php");
if (@is_file($g4['path']."/lib/etc.lib.php"))
    include_once("$g4[path]/lib/etc.lib.php");

if (!function_exists('convert_charset')) 
{
    /*
    -----------------------------------------------------------
        Charset 을 변환하는 함수
    -----------------------------------------------------------
    iconv 함수가 있으면 iconv 로 변환하고
    없으면 mb_convert_encoding 함수를 사용한다.
    둘다 없으면 사용할 수 없다.
    */
    function convert_charset($from_charset, $to_charset, $str) 
    {

        if( function_exists('iconv') )
            return iconv($from_charset, $to_charset, $str);
        elseif( function_exists('mb_convert_encoding') )
            return mb_convert_encoding($str, $to_charset, $from_charset);
        else
            die("Not found 'iconv' or 'mbstring' library in server.");
    }
}

$subject = str_replace(" ", "", strtolower($_POST['subject']));
$content = str_replace(" ", "", strtolower($_POST['content']));
$link1 = str_replace(" ", "", strtolower($_POST['link1']));
$link2 = str_replace(" ", "", strtolower($_POST['link2']));

//euc-kr 일 경우 $config['cf_filter'] 를 utf-8로 변환한다.
if (strtolower($g4[charset]) == 'euc-kr') 
{
    //$subject = convert_charset('utf-8', 'cp949', $subject);
    //$content = convert_charset('utf-8', 'cp949', $content);
    $config['cf_filter'] = convert_charset('cp949', 'utf-8', $config['cf_filter']);
}

//$filter = explode(",", strtolower(trim($config['cf_filter'])));
// strtolower 에 의한 한글 변형으로 아래 코드로 대체 (곱슬최씨님이 알려 주셨습니다.)
$filter = explode(",", trim($config['cf_filter']));
for ($i=0; $i<count($filter); $i++) 
{
    $str = strtolower($filter[$i]);

    // 제목 필터링 (찾으면 중지)
    $subj = "";
    $pos = @strpos($subject, $str);
    if ($pos !== false) 
    {
        if (strtolower($g4[charset]) == 'euc-kr') 
            $subj = convert_charset('utf-8', 'cp949', $str);//cp949 로 변환해서 반환
        else 
            $subj = $str;
        break;
    }

    // 내용 필터링 (찾으면 중지)
    $cont = "";
    $pos = @strpos($content, $str);
    if ($pos !== false) 
    {
        if (strtolower($g4[charset]) == 'euc-kr') 
            $cont = convert_charset('utf-8', 'cp949', $str);//cp949 로 변환해서 반환
        else 
            $cont = $str;
        break;
    }

    // link1 필터링 (찾으면 중지)
    $lin1 = "";
    $pos = @strpos($link1, $str);
    if ($pos !== false) 
    {
        if (strtolower($g4[charset]) == 'euc-kr') 
            $lin1 = convert_charset('utf-8', 'cp949', $str);//cp949 로 변환해서 반환
        else 
            $lin1 = $str;
        break;
    }

    $lin2 = "";
    $pos = @strpos($link2, $str);
    if ($pos !== false) 
    {
        if (strtolower($g4[charset]) == 'euc-kr') 
            $lin2 = convert_charset('utf-8', 'cp949', $str);//cp949 로 변환해서 반환
        else 
            $lin2 = $str;
        break;
    }
}
$str = "{\"subject\":\"$subj\",\"content\":\"$cont\",\"link1\":\"$lin1\",\"link2\":\"$lin2\"}";

//write_log("$g4[path]/data/filter-test", "$str\n");

die($str);
