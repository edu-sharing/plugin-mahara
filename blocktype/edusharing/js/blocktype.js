function prependEduFrame() {
    var edusearchurl = jQuery('input[name="edusearchurl"]').val();
    jQuery('.modal-content').prepend('<iframe id="eduFrame" src="'+edusearchurl+'" height="100%" width="100%"></iframe>');
}

if(jQuery('input[name="formereduobjectUrl"]').val()) {
    jQuery('#instconf').prepend('<span onclick="prependEduFrame()">anderes Objekt auswaehlen</span>');
} else {
    prependEduFrame();
}

window.addEventListener("message", function(event) {
    if(event.data.event=="APPLY_NODE"){
        node = event.data.data;
        jQuery('input[name="eduobjectUrl"]').attr('value', node.objectUrl).trigger("change") ; //to submit hidden field value
        jQuery('input[name="eduversion"]').attr('value', node.contentVersion).trigger("change"); //to submit hidden field value
        jQuery('input[name="title"]').val(node.title || node.name);

        //from mahara
        jQuery('#instconf_title_expand').addClass('hidden');
        jQuery('#instconf_title').removeClass('hidden');

        jQuery('#eduFrame').hide().remove().empty().css('display', 'none');
    }
}, false);