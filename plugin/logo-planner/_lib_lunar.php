<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

function sunlunar_data() { 
    return "1212122322121-1212121221220-1121121222120-2112132122122-2112112121220-2121211212120-2212321121212-2122121121210-2122121212120-1232122121212-1212121221220-1121123221222-1121121212220-1212112121220-2121231212121-2221211212120-1221212121210-2123221212121-2121212212120-1211212232212-1211212122210-2121121212220-1212132112212-2212112112210-2212211212120-1221412121212-1212122121210-2112212122120-1231212122212-1211212122210-2121123122122-2121121122120-2212112112120-2212231212112-2122121212120-1212122121210-2132122122121-2112121222120-1211212322122-1211211221220-2121121121220-2122132112122-1221212121120-2121221212110-2122321221212-1121212212210-2112121221220-1231211221222-1211211212220-1221123121221-2221121121210-2221212112120-1221241212112-1212212212120-1121212212210-2114121212221-2112112122210-2211211412212-2211211212120-2212121121210-2212214112121-2122122121120-1212122122120-1121412122122-1121121222120-2112112122120-2231211212122-2121211212120-2212121321212-2122121121210-2122121212120-1212142121212-1211221221220-1121121221220-2114112121222-1212112121220-2121211232122-1221211212120-1221212121210-2121223212121-2121212212120-1211212212210-2121321212221-2121121212220-1212112112210-2223211211221-2212211212120-1221212321212-1212122121210-2112212122120-1211232122212-1211212122210-2121121122210-2212312112212-2212112112120-2212121232112-2122121212110-2212122121210-2112124122121-2112121221220-1211211221220-2121321122122-2121121121220-2122112112322-1221212112120-1221221212110-2122123221212-1121212212210-2112121221220-1211231212222-1211211212220-1221121121220-1223212112121-2221212112120-1221221232112-1212212122120-1121212212210-2112132212221-2112112122210-2211211212210-2221321121212-2212121121210-2212212112120-1232212122112-1212122122120-1121212322122-1121121222120-2112112122120-2211231212122-2121211212120-2122121121210-2124212112121-2122121212120-1212121223212-1211212221220-1121121221220-2112132121222-1212112121220-2121211212120-2122321121212-1221212121210-2121221212120-1232121221212-1211212212210-2121123212221-2121121212220-1212112112220-1221231211221-2212211211220-1212212121210-2123212212121-2112122122120-1211212322212-1211212122210-2121121122120-2212114112122-2212112112120-2212121211210-2212232121211-2122122121210-2112122122120-1231212122212-1211211221220-2121121321222-2121121121220-2122112112120-2122141211212-1221221212110-2121221221210-2114121221221"; 
} 

function lun2sol($yyyymmdd) { 
    $getYEAR = (int)substr($yyyymmdd,0,4); 
    $getMONTH = (int)substr($yyyymmdd,4,2); 
    $getDAY = (int)substr($yyyymmdd,6,2); 

    $arrayDATASTR = sunlunar_data(); 
    $arrayDATA = explode("-",$arrayDATASTR); 

    $arrayLDAYSTR="31-0-31-30-31-30-31-31-30-31-30-31"; 
    $arrayLDAY = explode("-",$arrayLDAYSTR); 

    $arrayYUKSTR="갑-을-병-정-무-기-경-신-임-계"; 
    $arrayYUK = explode("-",$arrayYUKSTR); 

    $arrayGAPSTR="자-축-인-묘-진-사-오-미-신-유-술-해"; 
    $arrayGAP = explode("-",$arrayGAPSTR); 

    $arrayDDISTR="쥐-소-호랑이-토끼-용-뱀-말-양-원숭이-닭-개-돼지"; 
    $arrayDDI = explode("-",$arrayDDISTR); 

    $arrayWEEKSTR="일-월-화-수-목-금-토"; 
    $arrayWEEK = explode("-",$arrayWEEKSTR); 

    if ($getYEAR <= 1881 || $getYEAR >= 2050) { //년수가 해당일자를 넘는 경우 
        $YunMonthFlag = 0; 
        return false;   //년도 범위가 벗어남.. 
    } 
    if ($getMONTH > 12) { // 달수가 13이 넘는 경우 
        $YunMonthFlag = 0; 
        return false;   //달수 범위가 벗어남.. 
    } 
    $m1 = $getYEAR - 1881; 
    if (substr($arrayDATA[$m1],12,1) == 0) { // 윤달이 없는 해임 
        $YunMonthFlag = 0; 
    } else { 
        if (substr($arrayDATA[$m1],$getMONTH, 1) > 2) { 
            $YunMonthFlag = 1; 
        } else { 
            $YunMonthFlag = 0; 
        } 
    } 
    //------------- 
    $m1 = -1; 
    $td = 0; 

    if ($getYEAR > 1881 && $getYEAR < 2050) { 
        $m1 = $getYEAR - 1882; 
        for ($i=0;$i<=$m1;$i++) { 
            for ($j=0;$j<=12;$j++) { 
                $td = $td + (substr($arrayDATA[$i],$j,1)); 
            } 
            if (substr($arrayDATA[$i],12,1) == 0) { 
                $td = $td + 336; 
            } else { 
                $td = $td + 362; 
            } 
        } 
    } else { 
        $gf_lun2sol = 0; 
    } 

    $m1++; 
    $n2 = $getMONTH - 1; 
    $m2 = -1; 

    while(1) { 
        $m2++; 
        if (substr($arrayDATA[$m1], $m2, 1) > 2) { 
            $td = $td + 26 + (substr($arrayDATA[$m1], $m2, 1)); 
            $n2++; 
        } else { 
            if ($m2 == $n2) { 
                if ($gf_yun) { 
                    $td = $td + 28 + (substr($arrayDATA[$m1], $m2, 1)); 
                } 
                break; 
            } else { 
                $td = $td + 28 + (substr($arrayDATA[$m1], $m2, 1)); 
            } 
        } 
    } 

    $td = $td + $getDAY + 29; 
    $m1 = 1880; 
    while(1) { 
        $m1++; 
        if ($m1 % 400 == 0 || $m1 % 100 != 0 && $m1 % 4 == 0) { 
            $leap = 1; 
        } else { 
            $leap = 0; 
        } 

        if ($leap == 1) { 
            $m2 = 366; 
        } else { 
            $m2 = 365; 
        } 

        if ($td < $m2) break; 

        $td = $td - $m2; 
    } 
    $syear = $m1; 
    $arrayLDAY[1] = $m2 - 337; 

    $m1 = 0; 

    while(1) { 
        $m1++; 
        if ($td <= $arrayLDAY[$m1-1]) { 
            break; 
        } 
        $td = $td - $arrayLDAY[$m1-1]; 
    } 
    $smonth = $m1; 
    $sday = $td; 
    $y = $syear - 1; 
    $td = intval($y*365) + intval($y/4) - intval($y/100) + intval($y/400); 

    if ($syear % 400 == 0 || $syear % 100 != 0 && $syear % 4 == 0) { 
        $leap = 1; 
    } else { 
        $leap = 0; 
    } 

    if ($leap == 1) { 
        $arrayLDAY[1] = 29; 
    } else { 
        $arrayLDAY[1] = 28; 
    } 
    for ($i=0;$i<=$smonth-2;$i++) { 
        $td = $td + $arrayLDAY[$i]; 
    } 
    $td = $td + $sday; 
    $w = $td % 7; 

    $sweek = $arrayWEEK[$w]; 
    $gf_lun2sol = 1; 

    return($syear."|".$smonth."|".$sday."|".$sweek); 
} 

function sol2lun($yyyymmdd) { 
    $getYEAR = (int)substr($yyyymmdd,0,4); 
    $getMONTH = (int)substr($yyyymmdd,4,2); 
    $getDAY = (int)substr($yyyymmdd,6,2); 

    $arrayDATASTR = sunlunar_data(); 
    $arrayDATA = explode("-",$arrayDATASTR); 

    $arrayLDAYSTR="31-0-31-30-31-30-31-31-30-31-30-31"; 
    $arrayLDAY = explode("-",$arrayLDAYSTR); 

    $arrayYUKSTR="갑-을-병-정-무-기-경-신-임-계"; 
    $arrayYUK = explode("-",$arrayYUKSTR); 

    $arrayGAPSTR="자-축-인-묘-진-사-오-미-신-유-술-해"; 
    $arrayGAP = explode("-",$arrayGAPSTR); 

    $arrayDDISTR="쥐-소-호랑이-토끼-용-뱀-말-양-원숭이-닭-개-돼지"; 
    $arrayDDI = explode("-",$arrayDDISTR); 

    $arrayWEEKSTR="일-월-화-수-목-금-토"; 
    $arrayWEEK = explode("-",$arrayWEEKSTR); 

    $dt = $arrayDATA; 

    for ($i=0;$i<=168;$i++) { 
        $dt[$i] = 0; 
        for ($j=0;$j<12;$j++) { 
            switch (substr($arrayDATA[$i],$j,1)) { 
                case 1: 
                    $dt[$i] += 29; 
                    break; 
                case 3: 
                    $dt[$i] += 29; 
                    break; 
                case 2: 
                    $dt[$i] += 30; 
                    break; 
                case 4: 
                    $dt[$i] += 30; 
                    break; 
            } 
        } 

        switch (substr($arrayDATA[$i],12,1)) { 
            case 0: 
                break; 
            case 1: 
                $dt[$i] += 29; 
                break; 
            case 3: 
                $dt[$i] += 29; 
                break; 
            case 2: 
                $dt[$i] += 30; 
                break; 
            case 4: 
                $dt[$i] += 30; 
                break; 
        } 
    } 


    $td1 = 1880 * 365 + (int)(1880/4) - (int)(1880/100) + (int)(1880/400) + 30; 
    $k11 = $getYEAR - 1; 

    $td2 = $k11 * 365 + (int)($k11/4) - (int)($k11/100) + (int)($k11/400); 

    if ($getYEAR % 400 == 0 || $getYEAR % 100 != 0 && $getYEAR % 4 == 0) { 
        $arrayLDAY[1] = 29; 
    } else { 
        $arrayLDAY[1] = 28; 
    } 

    if ($getMONTH > 13) { 
        $gf_sol2lun = 0; 
    } 

    if ($getDAY > $arrayLDAY[$getMONTH-1]) { 
        $gf_sol2lun = 0; 
    } 

    for ($i=0;$i<=$getMONTH-2;$i++) { 
        $td2 += $arrayLDAY[$i]; 
    } 

    $td2 += $getDAY; 
    $td = $td2 - $td1 + 1; 
    $td0 = $dt[0]; 

    for ($i=0;$i<=168;$i++) { 
        if ($td <= $td0) { 
            break; 
        } 
        $td0 += $dt[$i+1]; 
    } 

    $ryear = $i + 1881; 
    $td0 -= $dt[$i]; 
    $td -= $td0; 

    if (substr($arrayDATA[$i], 12, 1) == 0) { 
        $jcount = 11; 
    } else { 
        $jcount = 12; 
    } 
    $m2 = 0; 

    for ($j=0;$j<=$jcount;$j++) { // 달수 check, 윤달 > 2 (by harcoon) 
        if (substr($arrayDATA[$i],$j,1) <= 2) { 
            $m2++; 
            $m1 = substr($arrayDATA[$i],$j,1) + 28; 
            $gf_yun = 0; 
        } else { 
            $m1 = substr($arrayDATA[$i],$j,1) + 26; 
            $gf_yun = 1; 
        } 
        if ($td <= $m1) { 
            break; 
        } 
        $td = $td - $m1; 
    } 

    $k1=($ryear+6) % 10; 
    $syuk = $arrayYUK[$k1]; 
    $k2=($ryear+8) % 12; 
    $sgap = $arrayGAP[$k2]; 
    $sddi = $arrayDDI[$k2]; 

    $gf_sol2lun = 1; 

    return ($ryear."|".$m2."|".$td."|".$syuk.$sgap."년|".$sddi."띠"); 
}

?>
