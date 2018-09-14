$(document).ready(function() {
    function setDialogValues(node) {
        $('#eduNode').val(JSON.stringify(node));
        $('#eduPreview').attr('src', node.preview.url+'&crop=true&maxWidth=400').show();
        $('#eduFormTitle').val(node.title || node.name);
        if(node.properties['ccm:width'] > 0 && node.properties['ccm:height']>0) {
            $('#eduFormRatio').val(node.properties['ccm:width'] / node.properties['ccm:height']);
            $('#eduFormWidth').val(node.properties['ccm:width']);
            $('#eduFormHeight').val(node.properties['ccm:height']);
        }
        $('#eduFormMediatype').val(node.mediatype);
        $('#eduFormObjectUrl').val(node.objectUrl);
        $('#eduFormObjectId').val(node.ref.id);
        $('#eduFormContentVersion').val(node.contentVersion);
        $('#eduFormVersionCurrent').val(node.contentVersion);

        $('#eduFormVersionCurrentShow').html(node.contentVersion);
        $('#eduFrame').remove();
        $('body').css('padding', '20px');
        $('#eduForm').show();
        if(node.mediatype.indexOf('image') !== -1 || node.mediatype.indexOf('video') !== -1) {
            if(node.properties['ccm:width'] > 0 && node.properties['ccm:height'] > 0)
                $('#eduFormObjectId').val(node.properties['ccm:width'] / node.properties['ccm:height']);
            $('#eduFormDimensions').show();
        }
    }

    window.addEventListener("message", function(event) {
        if(event.data.event=="APPLY_NODE"){
            node = event.data.data;
            setDialogValues(node);
        }
    }, false);


    $('#eduFormWidth').bind('keyup', function() {
        $('#eduFormHeight').val(Math.ceil($('#eduFormWidth').val() / $('#eduFormRatio').val()));
    })

    $('#eduFormHeight').bind('keyup', function() {
        $('#eduFormWidth').val(Math.ceil($('#eduFormHeight').val() / $('#eduFormRatio').val()));
    })
});