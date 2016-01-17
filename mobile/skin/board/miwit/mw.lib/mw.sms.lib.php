<?
/**
 * ICODEKOREA.COM 모듈
 *
 * SMS 발송을 관장하는 메인 클래스이다.
 *
 * 접속, 발송, URL발송, 결과등의 실질적으로 쓰이는 모든 부분이 포함되어 있다.
 */
class mwBasicSMS {
	var $icode_id;
	var $icode_pw;
	var $socket_host;
	var $socket_port;
	var $Data = array();
	var $Result = array();

	// 접속을 위해 사용하는 변수를 정리한다.
	function SMS_con($host,$id,$pw,$portcode) {
		if ($portcode == 1) {
			$port=(int)rand(7192,7195); 
		} else {
			$port=(int)rand(7196,7199); 
		}

		$this->socket_host	= $host;
		$this->socket_port	= $port;
		$this->icode_id		= mwBasicFillSpace($id, 10);
		$this->icode_pw		= mwBasicFillSpace($pw, 10);
	}

	function Init() {
		$this->Data		= "";	// 발송하기 위한 패킷내용이 배열로 들어간다.
		$this->Result	= "";	// 발송결과값이 배열로 들어간다.
	}

	function Add($strDest, $strCallBack, $strCaller, $strURL, $strData, $strDate="", $nCount) {
		$Error = mwBasicCheckCommonTypeDest($strDest, $nCount);
		$Error = mwBasicCheckCommonTypeCallBack($strCallBack);
		$Error = mwBasicCheckCommonTypeDate($strDate);

		$strCallBack	= mwBasicFillSpace($strCallBack,11);
		$strCaller		= mwBasicFillSpace($strCaller,10);
		$strDate		= mwBasicFillSpace($strDate,12);

		for ($i=0; $i<$nCount; $i++) {
			$strDest[$i]	= mwBasicFillSpace($strDest[$i],11);

			if (!$strURL) {
				$strData	= mwBasicFillSpace(mwBasicCutChar($strData,80),80);

				$this->Data[$i]	= '01144 '.$this->icode_id.$this->icode_pw.$strDest[$i].$strCallBack.$strCaller.$strDate.$strData;
			} else {
				$strURL		= mwBasicFillSpace($strURL,50);
				$strData	= mwBasicFillSpace(mwBasicCheckCallCenter($strURL, $strDest[$i], $strData),80);

				$this->Data[$i]	= '05173 '.$this->icode_id.$this->icode_pw.$strDest[$i].$strCallBack.$strURL.$strDate.$strData;
			}
		}
		return true; // 수정대기
	}

	function Send() {
		$fsocket=fsockopen($this->socket_host,$this->socket_port);
		if (!$fsocket) return false;
		set_time_limit(300);
		
		## php4.3.10일경우
        ## zend 최신버전으로 업해주세요.. 
        ## 또는 69번째 줄을 $this->Data as $tmp => $puts 로 변경해 주세요.

		foreach($this->Data as $puts) {
			$dest = substr($puts,26,11);
			fputs($fsocket, $puts);
			while(!$gets) {
				$gets = fgets($fsocket,30);
			}
			if (substr($gets,0,19) == "0223  00".$dest)
				$this->Result[] = $dest.":".substr($gets,19,10);
			else
				$this->Result[$dest] = $dest.":Error(".substr($gets,6,2).")";
			$gets = "";
		}
		fclose($fsocket);
		$this->Data = "";
		return true;
	}
}

/**
 * 원하는 문자열의 길이를 원하는 길이만큼 공백을 넣어 맞추도록 합니다.
 *
 * @param	text	원하는 문자열입니다.
 *			size	원하는 길이입니다.
 * @return			변경된 문자열을 넘깁니다.
 */
function mwBasicFillSpace($text,$size) {
	for ($i=0; $i<$size; $i++) $text.=" ";
	$text = substr($text,0,$size);
	return $text;
}


/**
 * 원하는 문자열을 원하는 길에 맞는지 확인해서 조정하는 기능을 합니다.
 *
 * @param	word	원하는 문자열입니다.
 *			cut		원하는 길이입니다.
 * @return			변경된 문자열입니다.
 */
function mwBasicCutChar($word, $cut) {
	$word=substr($word,0,$cut);						// 필요한 길이만큼 취함.
	for ($k=$cut-1; $k>1; $k--) {	 
		if (ord(substr($word,$k,1))<128) break;		// 한글값은 160 이상.
	}
	$word=substr($word,0,$cut-($cut-$k+1)%2);
	return $word;
}


/**
 * 발송번호의 값이 정확한 값인지 확인합니다.
 *
 * @param	strDest	발송번호 배열입니다.
 *			nCount	배열의 크기입니다.
 * @return			처리결과입니다.
 */
function mwBasicCheckCommonTypeDest($strDest, $nCount) {
	for ($i=0; $i<$nCount; $i++) {
		$strDest[$i]=preg_replace("/[^0-9]/","",$strDest[$i]);
		if (strlen($strDest[$i])<10 || strlen($strDest[$i])>11) return "휴대폰 번호가 틀렸습니다";

		$CID=substr($strDest[$i],0,3);
		if ( preg_match("/[^0-9]/",$CID) || ($CID!='010' && $CID!='011' && $CID!='016' && $CID!='017' && $CID!='018' && $CID!='019') ) return "휴대폰 앞자리 번호가 잘못되었습니다";
	}
}


/**
 * 회신번호의 값이 정확한 값인지 확인합니다.
 *
 * @param	strDest	회신번호입니다.
 * @return			처리결과입니다.
 */
function mwBasicCheckCommonTypeCallBack($strCallBack) {
	if (preg_match("/[^0-9]/", $strCallBack)) return "회신 전화번호가 잘못되었습니다";
}


/**
 * 예약날짜의 값이 정확한 값인지 확인합니다.
 *
 * @param	text	원하는 문자열입니다.
 *			size	원하는 길이입니다.
 * @return			처리결과입니다.
 */
function mwBasicCheckCommonTypeDate($strDate) {
	$strDate=preg_replace("/[^0-9]/","",$strDate);
	if ($strDate) {
		if (!checkdate(substr($strDate,4,2),substr($strDate,6,2),substr($rsvTime,0,4))) return "예약날짜가 잘못되었습니다";
		if (substr($strDate,8,2)>23 || substr($strDate,10,2)>59) return "예약시간이 잘못되었습니다";
	}
}


/**
 * URL콜백용으로 메세지 크기를 수정합니다.
 *
 * @param	url		URL 내용입니다.
 *			msg		결과메시지입니다.
 *			desk	문자내용입니다.
 */
function mwBasicCheckCallCenter($url, $dest, $data) {
	switch (substr($dest,0,3)) {
		case '010': //20바이트
			return mwBasicCutChar($data,20);
			break;
		case '011': //80바이트
			return mwBasicCutChar($data,80);
			break;
		case '016': // 80바이트
			return mwBasicCutChar($data,80);
			break;
		case '017': // URL 포함 80바이트
			return mwBasicCutChar($data,80 - strlen($url));
			break;
		case '018': // 20바이트
			return mwBasicCutChar($data,20);
			break;
		case '019': // 20바이트
			return mwBasicCutChar($data,20);
			break;
		default:
			return mwBasicCutChar($data,80);
			break;
	}
}
?>
