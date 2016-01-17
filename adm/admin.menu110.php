<?php
if (!defined("_MW5_")) return;

$menu['menu110'] = array (
    array('110000', '배추빌더5', $mw5['admin_url'].'/config_form.php', 'mw5_config'),
    array('110100', '배추빌더5 기본설정', $mw5['admin_url'].'/config_form.php', 'mw5_config'),
    array('110200', 'SEO 설정', $mw5['admin_url'].'/seo_form.php', 'mw5_config'),
    array('110300', '소셜 로그인 설정', $mw5['admin_url'].'/social_form.php', 'mw5_config'),
    array('110400', '메뉴 추가설정', $mw5['admin_url'].'/menu_list.php', 'mw5_menu'),
);
