<?
if (!defined('_GNUBOARD_')) exit;

function cheditor1($id, $width='100%', $height='250')
{
    global $g4;

    return "
    <script type='text/javascript'>
    var ed_{$id} = new cheditor();
    ed_{$id}.config.editorHeight = '{$height}';
    ed_{$id}.config.editorWidth = '{$width}';
    ed_{$id}.inputForm = 'tx_{$id}';
    </script>";
    //ed_{$id}.config.editorPath = '{$g4[cheditor4_path]}';
    //ed_{$id}.config.imgReSize = false;
    //ed_{$id}.config.fullHTMLSource = false;
}

function cheditor2($id, $content='')
{
    global $g4;

    return "
    <textarea name='{$id}' id='tx_{$id}'>{$content}</textarea>
    <script type='text/javascript'> ed_{$id}.run(); </script>";
}
 
function cheditor3($id)
{
    return "document.getElementById('tx_{$id}').value = ed_{$id}.outputBodyHTML();";
}
?>
