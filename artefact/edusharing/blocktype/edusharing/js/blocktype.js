if(!jQuery('input[name="eduid"]').val()) {
    var edusearchurl = jQuery('input[name="edusearchurl"]').val();
    jQuery('.modal-content').prepend('<iframe class="eduFrame" id="eduFrame" src="'+edusearchurl+'" height="100%" width="100%"></iframe>');
    jQuery("#instconf_retractable_container").after('<img class="eduPreview" style="max-width:100%;display:block;" src="">');
}

if(jQuery('input[name="previewurl"]').val())
    $("#instconf_retractable_container").after('<img class="eduPreview" style="max-width:100%;display:block;" src="'+jQuery('input[name="previewurl"]').val()+'">');

window.addEventListener("message", function(event) {
    if(event.data.event=="APPLY_NODE"){
        var node = event.data.data;
        console.log(node);
        console.log('message apply node');
        const resourceId = Math.floor(Math.random() * 10000) + 1000;
        jQuery('input[name="eduobjectUrl"]').attr('value', node.objectUrl).trigger("change") ; //to submit hidden field value
        jQuery('input[name="eduversion"]').attr('value', node.contentVersion).trigger("change"); //to submit hidden field value
        jQuery('input[name="edumimetype"]').attr('value', node.mimetype).trigger("change"); //to submit hidden field value
        jQuery('input[name="title"]').val(node.title || node.name);
        jQuery('input[name="eduwidth"]').attr('value', node.properties['ccm:width']).trigger("change");
        jQuery('input[name="eduheight"]').attr('value', node.properties['ccm:height']).trigger("change");
        jQuery('input[name="previewurl"]').attr('value', node.preview.url+ '&crop=true&width=444&maxHeight=500').trigger("change");
        jQuery('.eduPreview').attr('src', node.preview.url+ '&crop=true&width=444&maxHeight=500');
        jQuery('input[name="eduresourceid"]').attr('value', resourceId).trigger("change");

        //from mahara
        jQuery('#instconf_title_expand').addClass('hidden');
        jQuery('#instconf_title').removeClass('hidden');

        jQuery('.eduFrame').remove();
    }
}, false);