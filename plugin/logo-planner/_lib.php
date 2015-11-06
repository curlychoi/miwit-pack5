<?php
/**
 * 로고 플래너 (Logo Planner for Gnuboard4)
 *
 * Copyright (c) 2011 Choi Jae-Young <www.miwit.com>
 *
 * 저작권 안내
 * - 저작권자는 이 프로그램을 사용하므로서 발생하는 모든 문제에 대하여 책임을 지지 않습니다. 
 * - 이 프로그램을 어떠한 형태로든 재배포 및 공개하는 것을 허락하지 않습니다.
 * - 이 저작권 표시사항을 저작권자를 제외한 그 누구도 수정할 수 없습니다.
 */

if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

include_once("$mw_logo_planner[path]/_lib_lunar.php");

function mw_lunar($date)
{
    $date = substr($date, 0, 4) . substr($date, 5, 2) . substr($date, 8, 2);
    $tmp = explode("|", lun2sol($date));
    return "$tmp[0]-".sprintf("%02d", $tmp[1])."-".sprintf("%02d", $tmp[2]);
}

function mw_logo_planner()
{
    global $g4, $mw_logo_planner, $is_admin;

    $mw_logo_path = '';

    $admin_link = '';
    if ($is_admin == "super") {
        $admin_link = "<div style=\"position:absolute;margin:-23px 0 0 0;\"><img src=\"$mw_logo_planner[path]/logo-planner-admin.png\" style=\"cursor:pointer;\" onclick=\"mw_logo = window.open('$mw_logo_planner[path]/list.php', 'logo_planner_admin', 'width=800,height=600,scrollbars=yes'); mw_logo.focus();\"></div>";
    }

    $sql = " select * from $mw_logo_planner[logo_table] where ls_use = '1' order by ls_order desc ";
    $qry = sql_query($sql, false);
    while ($row = sql_fetch_array($qry))
    {
        $today = $g4[time_ymd];
        $flag = true;

        switch ($row[ls_repeat]) {
            case "week":
                if (date("N", $g4[server_time]) != $row[ls_week]) $flag = false;
                break;
            case "none":
                if ($row[ls_lunar]) {
                    $row[ls_sdate] = mw_lunar($row[ls_sdate]);
                    $row[ls_edate] = mw_lunar($row[ls_edate]);
                }
                if ($today < $row[ls_sdate] || $today > $row[ls_edate]) $flag = false;
                break;
            case "month":
                $day = date("d", strtotime("$today 00:00:00"));
                $ls_sdate = date("d", strtotime("$row[ls_sdate] 00:00:00"));
                $ls_edate = date("d", strtotime("$row[ls_edate] 00:00:00"));
                if ($row[ls_lunar]) {
                    $day = mw_lunar($day);
                    $ls_sdate = mw_lunar($ls_sdate);
                    $ls_edate = mw_lunar($ls_edate);
                }
                if ($day < $ls_sdate || $day > $ls_edate) $flag = false;
                break;
            case "year":
                $year_sdate = substr($row[ls_sdate], 0, 4);
                $year_edate = substr($row[ls_edate], 0, 4);
                $year_gap = $year_edate - $year_sdate;
                if ($year_gap) {
                    $row[ls_sdate] = date("Y", strtotime("-{$year_gap} year", $g4[server_time])) . '-' . substr($row[ls_sdate], 5, 5);
                    $row[ls_edate] = date("Y", $g4[server_time]) . '-' . substr($row[ls_edate], 5, 5);
                }
                else {
                    $row[ls_sdate] = date("Y", $g4[server_time]) . '-' . substr($row[ls_sdate], 5, 5);
                    $row[ls_edate] = date("Y", $g4[server_time]) . '-' . substr($row[ls_edate], 5, 5);
                }
                //$row[ls_edate] = date("Y", $g4[server_time]) . '-' . substr($row[ls_edate], 5, 5) . ' 00:00:00';
                //$row[ls_edate] = date("Y-m-d", strtotime("+$year_gap year", strtotime($row[ls_edate])));

                if ($row[ls_lunar]) {
                    $row[ls_sdate] = mw_lunar($row[ls_sdate]);
                    $row[ls_edate] = mw_lunar($row[ls_edate]);
                }

                $day = date("Y-m-d", strtotime("$today 00:00:00"));
                $ls_sdate = date("Y-m-d", strtotime("$row[ls_sdate] 00:00:00"));
                $ls_edate = date("Y-m-d", strtotime("$row[ls_edate] 00:00:00"));

                if ($row[ls_lieu]) {
                    for ($i=$ls_sdate; $i<$ls_edate; $i=date("Y-m-d H:i:s", strtotime("+1 day", strtotime($i)))) {
                        if (date("w", strtotime($i)) == "0") {
                            $ls_edate = date("Y-m-d", strtotime("+1 day", strtotime($row[ls_edate])));
                            $row[ls_edate] = $ls_edate;
                            break;
                        }
                    }
                }

                if ($ls_sdate > $ls_edate) {
                    if ($day < $ls_sdate && $day > $ls_edate) { $flag = false; }
                } else {
                    if ($day < $ls_sdate || $day > $ls_edate) { $flag = false; }
                }
                break;
            case "main":
                break;
        }
        if (!$flag) continue;

        if (!$row[ls_url]) $row[ls_url] = $g4[path];
        if (!$row[ls_target]) $row[ls_target] = "_self";
        
        if ($row[ls_logo_file]) {
            $mw_logo = "$mw_logo_planner[logo_path]/$row[ls_logo_file]";
            if (file_exists($mw_logo)) {
                $mw_logo_img = "<span id=\"logo-planner\">{$admin_link}<a href=\"$row[ls_url]\" target=\"$row[ls_target]\"><img src=\"$mw_logo\" border=\"0\"/></a></span>";
            }
            break;
        }
    }
    if (!$mw_logo_img) {
        $mw_logo = "$mw_logo_planner[path]/nologo.png";
        $mw_logo_img = "<span id=\"logo-planner\">{$admin_link}<a href=\"$g4[path]\" target=\"_self\"><img src=\"$mw_logo\" border=\"0\"/></a></span>";
    }

    return $mw_logo_img;
}


