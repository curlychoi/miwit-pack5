
function check_okname()
{
    var tmp = $("#btn-send").html();
    $("#err-msg").html("");
    $("#btn-send").html("<img src='"+board_skin_path+"/img/icon_loading.gif'>");

    $.post(okname_path, { bo_table:g4_bo_table, nam:fok.nam.value, ssn:fok.ssn.value },
        function (code)
        {
            switch (code)
            {
                case '19ban':
                    $("#err-msg").html("19세 이상 이용할 수 있습니다.");
                    break;
                case 'B000':
                    alert("인증이 완료되었습니다.");
                    location.reload();
                    break;
                case 'B001':
                    // 주민등록번호가 존재하지 않는 경우. ok-name.co.kr 에서 실명등록을 할 수 있게함.
                    // 주민번호가 없어 인증이 되지 않은 것으로 인증실패로 처리해야 합니다.
                    // 스크립트와 해당 페이지를 복사해서 사용하셔도 됩니다. 해당 페이지는 메뉴얼에 포함되어있습니다.
                    $("#err-msg").html("실명이 등록되어 있지 않습니다. <a href='javascript:KCB_okNameGuide();'>[확인]</a>");
                    break;
                case 'B002':
                    $("#err-msg").html("이름과 주민등록번호가 일치하지 않습니다.");
                    break;
                case 'B003':
                    $("#err-msg").html("주민등록번호가 올바르지 않습니다.");
                    break;
                case 'B016':
                    // 명의보호서비스에 가입된 경우 인증창으로 유도합니다.
                    $("#err-msg").html("명의보호서비스에 가입되어 있습니다. <a href='javascript:KCB_BlockedName();'>[해제]</a>");
                    break;
                default:
                    $("#err-msg").html("인증에 실패하였습니다. 관리자에게 이 코드를 문의하세요 : "+code);
                    break;
            }
            $("#btn-send").html(tmp);
        }
    );
    return false;
}

