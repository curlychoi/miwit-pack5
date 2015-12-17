<?php
$post = null;
foreach((array)$_POST as $key => $val) {
    if (is_array($val)) {
        foreach($val as $va2) {
            $post .= "<input type='hidden' name='{$key}[]' value='{$va2}'>\n";
        }
    }
    else
        $post .= "<input type='hidden' name='{$key}' value='{$val}'>\n";
}
?>
<html>
<body>
<form name="fmove" method="post" action="../bbs/<?php echo basename($_SERVER['SCRIPT_NAME'])?>">
<?php echo $post?>
</form>
<script>
fmove.submit();
</script>
</body>
</html>
