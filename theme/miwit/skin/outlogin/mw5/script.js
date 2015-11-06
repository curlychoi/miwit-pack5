
$(document).ready(function () {
    $(".auto").click(function () {
        var msg = "";
        msg+= "자동로그인을 사용하시면 다음부터 회원아이디와 패스워드를 입력하실 필요가 없습니다.\n\n";
        msg+= "공공장소에서는 개인정보가 유출될 수 있으니 사용을 자제하여 주십시오.\n\n";
        msg+= "자동로그인을 사용하시겠습니까?";

        if (!$("#auto_login").val() && confirm(msg)) {
            $("#auto_login").val("1");
            $(this).addClass("checked");
        }
        else {
            $("#auto_login").val("");
            $(this).removeClass("checked");
        }
    });

    $("form[name=foutlogin]").submit(function () {
        if (!$("input[name=mb_id]").val()) {
            alert("회원아이디를 입력하십시오.");
            $("input[name=mb_id]").focus();
            return false;
        }
        if (!$("input[name=mb_password]").val()) {
            alert("패스워드를 입력하십시오.");
            $("input[name=mb_password]").focus();
            return false;
        }
        return true;
    });
});

