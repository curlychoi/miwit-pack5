<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가 

$icode_id = $mw_basic[cf_sms_id];
$icode_pw = $mw_basic[cf_sms_pw];

$socket_host = "211.172.232.124";
$port_setting = 1;

$strCallBack = str_replace("-", "", $strCallBack);

// SMS 모듈 클래스 생성
$SMS = new mwBasicSMS;
$SMS->SMS_con($socket_host,$icode_id,$icode_pw,$port_setting);

// 발송번호 목록을 가져옵니다.
//$strDest = explode(";",$strTelList); // 발송번호 목록
$nCount = count($strDest); // 발송번호 수

// 예약설정을 합니다.
if ($chkSendFlag) {
    $strDate = $R_YEAR.$R_MONTH.$R_DAY.$R_HOUR.$R_MIN;
} else {
    $strDate = "";
}
$strData = set_euckr($strData);

// 발송하기위해 패킷을 정의합니다.
$result = $SMS->Add($strDest, $strCallBack, $strCaller, $strURL, $strData, $strDate, $nCount);

// 패킷 정의의 결과에 따라 발송여부를 결정합니다.
if ($result) {
	$log = "-----------------------\n";
	$log .= "$g4[time_ymdhis] : 일반메시지 입력 성공\n";
	// 패킷이 정상적이라면 발송에 시도합니다.
	$result = $SMS->Send();
	if ($result) {
		$log .= "SMS 서버에 접속했습니다.\n";
		$success = $fail = 0;
		foreach($SMS->Result as $result) {
			list($phone,$code)=explode(":",$result);
			if (substr($code,0,5)=="Error") {
				$log .= "{$phone}로 발송하는데 에러가 발생했습니다.\n";
				switch (substr($code,6,2)) {
					case '02':	 // "02:형식오류"
						$log .= "형식이 잘못되어 전송이 실패하였습니다.\n";
						break;
					case '23':	 // "23:인증실패,데이터오류,전송날짜오류"
						$log .= "데이터를 다시 확인해 주시기바랍니다.\n";
						break;
					case '97':	 // "97:잔여코인부족"
						$log .= "잔여코인이 부족합니다.\n";
						break;
					case '98':	 // "98:사용기간만료"
						$log .= "사용기간이 만료되었습니다.\n";
						break;
					case '99':	 // "99:인증실패"
						$log .= "인증 받지 못하였습니다. 계정을 다시 확인해 주세요.\n";
						break;
					default:	 // "미 확인 오류"
						$log .= "알 수 없는 오류로 전송이 실패하었습니다.\n";
						break;
				}
				$fail++;
			} else {
				$log .= $phone."로 전송했습니다. (메시지번호:".$code.")\n";
				$success++;
			}
		}
		$log .= $success."건을 전송했으며 ".$fail."건을 보내지 못했습니다.\n";
		$SMS->Init(); // 보관하고 있던 결과값을 지웁니다.
	}
	else $log .= "에러: SMS 서버와 통신이 불안정합니다.\n";
}

if (function_exists("write_log"))
    write_log("$g4[path]/data/log/{$bo_table}.sms.".date("ym", $g4['server_time']), $log);

