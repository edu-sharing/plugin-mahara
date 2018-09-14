jQuery(document).ready(function() {

    jQuery.ajaxSetup({ cache: false });

    var videoFormat = 'webm';
    var v = document.createElement('video');
    if(v.canPlayType && v.canPlayType('video/mp4').replace(/no/, '')) {
        videoFormat = 'mp4';
    }

    function renderEsObject(esObject) {
        var url = config.wwwroot + '/artefact/edusharing/lib/proxy.php?sesskey=' + config.sesskey;
        url += '&videoFormat='+videoFormat;
        url += '&id='+esObject.attr('id');

        esObject.html('<style scoped>.edusharing_spinner1,.edusharing_spinner2{-webkit-animation:spin 2s infinite ease-in;-moz-animation:spin 2s infinite ease-in;-ms-animation:spin 2s infinite ease-in;-o-animation:spin 2s infinite ease-in;height:50px}.edusharingObjectRaw{width:180px;color:transparent}div.mdsGroup{padding:0!important}.edusharing_spinner_inner{width:30px;height:50px;display:inline-block}.edusharing_spinner1{background:url('+config.wwwroot+'/artefact/edusharing/theme/raw/static/img/hex1.svg) center no-repeat;background-size:50px;width:50px;animation:spin 2s infinite ease-in;-webkit-animation-delay:.1s;-moz-animation-delay:.1s;animation-delay:.1s}.edusharing_spinner2{background:url('+config.wwwroot+'/artefact/edusharing/theme/raw/static/img/hex2.svg) center no-repeat;background-size:50px;width:50px;animation:spin 2s infinite ease-in;-webkit-animation-delay:.25s;-moz-animation-delay:.25s;animation-delay:.25s}.edusharing_spinner3{background:url('+config.wwwroot+'/artefact/edusharing/theme/raw/static/img/hex3.svg) center no-repeat;background-size:50px;width:50px;height:50px;-webkit-animation:spin 2s infinite ease-in;-moz-animation:spin 2s infinite ease-in;-ms-animation:spin 2s infinite ease-in;-o-animation:spin 2s infinite ease-in;animation:spin 2s infinite ease-in;-webkit-animation-delay:.5s;-moz-animation-delay:.5s;animation-delay:.5s}@-webkit-keyframes spin{0%,100%{transform:scale(1)}50%{transform:scale(.5)}}@-moz-keyframes spin{0%,100%{transform:scale(1)}50%{transform:scale(.5)}}@-ms-keyframes spin{0%,100%{transform:scale(1)}50%{transform:scale(.5)}}@-o-keyframes spin{0%,100%{transform:scale(1)}50%{transform:scale(.5)}}@keyframes spin{0%,100%{transform:scale(1)}50%{transform:scale(.5) rotate(90deg)}}</style>'+'\n' +
            '<div class="edusharing_spinner_inner"><div class="edusharing_spinner1"></div></div>'+'\n' +
            '                 <div class="edusharing_spinner_inner"><div class="edusharing_spinner2"></div></div>'+'\n' +
            '                 <div class="edusharing_spinner_inner"><div class="edusharing_spinner3"></div></div>'+'\n' +
            '                 edu sharing object</div>');

        jQuery.get(url, function(data) {
            esObject.removeClass('edusharingObjectRaw');
            esObject.html(data).css('display', esObject.css('display')).css('float', esObject.css('float'));
            if (data.toLowerCase().indexOf('data-view="lock"') >= 0)
                setTimeout(function(){ renderEsObject(esObject);}, 1111);
        });
    }

    triggerRendering = function() {
        jQuery(".edusharingObjectRaw").each(function() {
            renderEsObject(jQuery(this));
        })
    }

    triggerRendering();
    setInterval(function() {
        triggerRendering();
    }, 1500);


    jQuery(document).on('DOMNodeInserted', '#main', function (e) {
       //triggerRendering();
    });


    jQuery("body").click(function(e) {
        if (jQuery(e.target).closest(".edusharing_metadata").length) {
            //clicked inside ".edusharing_metadata" - do nothing
        } else {
            jQuery(".edusharing_metadata").hide();
            if (jQuery(e.target).closest(".edusharing_metadata_toggle_button").length) {
                jQuery(".edusharing_metadata").hide();
                toggle_button = jQuery(e.target);
                metadata = toggle_button.parent().find(".edusharing_metadata");
                if(metadata.hasClass('open')) {
                    metadata.toggleClass('open');
                    metadata.hide();
                } else {
                    jQuery(".edusharing_metadata").removeClass('open');
                    metadata.toggleClass('open');
                    metadata.show();
                }
            } else {
                jQuery(".edusharing_metadata").removeClass('open');
            }
        }
    });
});

