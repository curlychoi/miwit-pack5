<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if (defined("_MW_MOBILE_")) return;
?>
<div style="clear:both;"></div>
</div> <!-- mw_mobile -->

<div id="mw_mobile_tail">
    <a href="<?php echo G5_URL?>/" class="btn btn-primary btn-sm"><?php echo $config['cf_title']?></a>
    <a href="<?php echo $pc_url?>" class="btn btn-primary btn-sm">PC버전</a>

    <?php if ($is_member) { ?>
    <a href="<?php echo G5_BBS_URL?>/logout.php?url=login.php" class="btn btn-primary btn-sm">로그아웃</a>
    <?php } else { ?>
    <a href="<?php echo G5_BBS_URL?>/login.php?url=<?php echo urlencode($_SERVER['REQUEST_URI'])?>" class="btn btn-primary btn-sm">로그인</a>
    <?php } ?>
</div>

<!-- Latest compiled and minified JavaScript -->
<script src="<?php echo G5_PATH?>/asset/bootstrap-3.3.5/js/bootstrap.min.js"></script>

<?php
include_once(G5_THEME_PATH.'/mobile/side.php');
include_once(G5_THEME_PATH.'/tail.sub.php');
