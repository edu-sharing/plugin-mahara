<?php
define('INTERNAL', 1);
define('PUBLIC', 1);
require(__DIR__. '/../../../../init.php');
require(__DIR__. '/../../../../artefact/edusharing/lib/Edusharing.php');
$edusharing = new Edusharing();
?>
<html>
<head>
    <script src="../../../jquery/jquery.js"></script>
    <script>
        <?php include 'dialog.js'?>
    </script>
    <link rel="stylesheet" href="<?php echo get_config('wwwroot').'/theme/modern/style/style.css'?>">
    <link rel="stylesheet" href="<?php echo get_config('wwwroot').'/js/tinymce/plugins/edusharing/style.css'?>">
</head>
<body>
<iframe id="eduFrame" src="<?php echo get_config_plugin('artefact', 'edusharing', 'repourl')?>/components/search?reurl=IFRAME&applyDirectories=true&ticket=<?php echo $edusharing->getTicket()?>" style="border: none;" height="100%" width="100%">edu</iframe>
<img id="eduPreview" src="" style="float:right; width: 200px;display:none;">
<form id="eduForm" style="display:none">
    <span class="eduLabel"><?php echo get_string('eduversionshow_title', 'blocktype.edusharing/edusharing')?></span>
    <input type="text" id="eduFormTitle">
    <div id="eduFormDimensions" style="display:none">
        <div style="float: left; margin-right:30px;"><label for="eduFormWidth" class="eduLabel"><?php echo get_string('eduversionshow_width', 'blocktype.edusharing/edusharing')?></label><input maxlength="5" type="text" id="eduFormWidth">px</div>
        <div><label for="eduFormHeight" class="eduLabel"><?php echo get_string('eduversionshow_height', 'blocktype.edusharing/edusharing')?></label><input maxlength="5" type="text" id="eduFormHeight">px</div>
        <input type="hidden" id="eduFormRatio">
        <div style="clear: both"></div>
    </div>
    <div id="eduFormVersion">
        <span class="eduLabel"><?php echo get_string('eduversionshow', 'blocktype.edusharing/edusharing')?></span>
        <fieldset>
            <input type="radio" id="eduFormVersionLatest" name="eduFormVersion" value="-1" checked>
            <label for="eduFormVersionLatest"> <?php echo get_string('eduversionshow_last', 'blocktype.edusharing/edusharing')?></label>
            <input type="radio" id="eduFormVersionCurrent" name="eduFormVersion" value="">
            <label for="eduFormVersionCurrent"> <?php echo get_string('eduversionshow_current', 'blocktype.edusharing/edusharing')?> (<span id="eduFormVersionCurrentShow"></span>)</label>
        </fieldset>
    </div>
    <div id="eduFormAlignment">
        <span class="eduLabel"><?php echo get_string('eduversionshow_alignment', 'blocktype.edusharing/edusharing')?></span>
        <fieldset>
            <input type="radio" id="eduFormAlignmentInline" name="eduFormAlignment" value="inline" checked>
            <label for="eduFormAlignmentInline"> <?php echo get_string('eduversionshow_alignment_inline', 'blocktype.edusharing/edusharing')?></label>
            <input type="radio" id="eduFormAlignmentLeft" name="eduFormAlignment" value="left">
            <label for="eduFormAlignmentLeft"> <?php echo get_string('eduversionshow_alignment_left', 'blocktype.edusharing/edusharing')?></label>
            <input type="radio" id="eduFormAlignmentRight" name="eduFormAlignment" value="right">
            <label for="eduFormAlignmentRight"> <?php echo get_string('eduversionshow_alignment_right', 'blocktype.edusharing/edusharing')?></label>
            <input type="radio" id="eduFormAlignmentBlock" name="eduFormAlignment" value="block">
            <label for="eduFormAlignmentBlock"> <?php echo get_string('eduversionshow_alignment_block', 'blocktype.edusharing/edusharing')?></label>
        </fieldset>
    </div>
    <div id="eduFormDirectoryHint" style="display:none">
        <p><?php echo get_string('edudirectory1', 'blocktype.edusharing/edusharing')?></p>
        <p><?php echo get_string('edudirectory2', 'blocktype.edusharing/edusharing')?></p>
    </div>
    <input type="hidden" id="eduFormMediatype">
    <input type="hidden" id="eduFormObjectUrl">
    <input type="hidden" id="eduFormObjectId">
    <input type="hidden" id="eduFormContentVersion">
    <input type="hidden" id="eduNode">
</form>
</body>
</html>

<!-- <button onclick="top.tinymce.activeEditor.windowManager.getWindows()[0].close();">Close window</button> -->
