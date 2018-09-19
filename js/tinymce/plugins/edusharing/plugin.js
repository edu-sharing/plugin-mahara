tinymce.PluginManager.add('edusharing', function(editor, url) {

    //tinymce copies tags/class on pressing enter, we prevent that here
    editor.on('keyup',function(e) {
        if (13 === e.keyCode) {
            var node = editor.selection.getNode().parentElement;
            if(node.classList.contains('edusharingObject') && node.id == false)
                node.remove();
            editor.insertContent ('<p></p>');
        }
    })

    // Add a button that opens a window
    editor.addButton('edusharing', {
        image: config.wwwroot+'/artefact/edusharing/img/edusharing.png',
        onclick: function() {

            if(editor.selection.getNode().classList.contains('edusharingObject') || editor.selection.getNode().parentNode.classList.contains('edusharingObject'))
                console.log('edit mode not implemented');
            else
            // Open window
            editor.windowManager.open({
                title: 'Material aus edu-sharing einfügen',
                url: url + '/dialog.php',
                width: 950,
                height: 600,
                buttons: [
                    {
                        text: 'Insert',
                        onclick: function() {
                            // Top most window object
                            var win = editor.windowManager.getWindows()[0];
                            var node = JSON.parse(win.getContentWindow().document.getElementById('eduNode').value);

                            // Insert the contents of the dialog.html textarea into the editor
                            var title = node.title || node.name;
                            var version = win.getContentWindow().document.getElementsByName('eduFormVersion');
                            for (var i = 0, length = version.length; i < length; i++) {
                                if (version[i].checked) {
                                    version = version[i].value;
                                    break;
                                }
                            }
                            var alignments = win.getContentWindow().document.getElementsByName('eduFormAlignment');
                            for (var i = 0, length = alignments.length; i < length; i++) {
                                if (alignments[i].checked) {
                                    var alignment = alignments[i].value;
                                    switch(alignments[i].value) {
                                        case 'left':
                                            var style='style="float:left"';
                                            break;
                                        case 'right':
                                            var style='style="float:left"';
                                            break;
                                        case 'block':
                                            var style='style="display:block"';
                                            break;
                                        default:
                                            var style='';
                                    }
                                    break;
                                }
                            }
console.log(node.mediatype);
                            if(node.mediatype.indexOf('image') !== -1 || node.mediatype.indexOf('video') !== -1 || node.mediatype.indexOf('h5p') !== -1 || node.mediatype.indexOf('folder') !== -1) {
                                editor.insertContent ('<img '+style+' src="'+node.preview.url+'" ' +
                                    'data-objectId="'+node.ref.id+'"' +
                                    'data-objectUrl="'+node.objectUrl+'" ' +
                                    'data-title="'+title+'" ' +
                                    'data-preview="'+ node.preview.url+'" ' +
                                    'width="'+win.getContentWindow().document.getElementById('eduFormWidth').value+'" ' +
                                    'height="'+win.getContentWindow().document.getElementById('eduFormHeight').value+'" ' +
                                    'data-mediatype="'+node.mediatype+'" ' +
                                    'data-mimetype="'+node.mimetype+'" ' +
                                    'data-contentVersion="'+node.contentVersion+'" ' +
                                    'data-version="'+version+'" ' +
                                    'data-alignment="'+alignment+'" ' +
                                    'alt="'+title+'" ' +
                                    'class="edusharingObject" ' +
                                    '/>');
                            } else {
                                editor.insertContent ('<span '+ style +
                                    'data-objectId="'+node.ref.id+'"' +
                                    'data-objectUrl="'+node.objectUrl+'" ' +
                                    'data-title="'+title+'" ' +
                                    'data-preview="'+ node.preview.url+'" ' +
                                    'data-mediatype="'+node.mediatype+'" ' +
                                    'data-mimetype="'+node.mimetype+'" ' +
                                    'data-contentVersion="'+node.contentVersion+'" ' +
                                    'data-version="'+version+'" ' +
                                    'data-alignment="'+alignment+'" ' +
                                    'alt="'+title+'" ' +
                                    'class="edusharingObject" >' +
                                    title + '</span>');
                            }

                            // Close the window
                            win.close();
                        }
                    },

                    {text: 'Close', onclick: 'close'}
                ]
            });
        }
    });
/*
    // Adds a menu item to the tools menu
    editor.addMenuItem('edusharing', {
        text: 'edu-sharing',
        context: 'tools',
        icon: 'image',
        onclick: function() {
            // Open window with a specific url
            editor.windowManager.open({
                title: 'TinyMCE site',
                url: 'https://www.tinymce.com',
                width: 800,
                height: 600,
                buttons: [{
                    text: 'Close',
                    onclick: 'close'
                }]
            });
        }
    });*/

    return {
        getMetadata: function () {
            return  {
                name: "edu-sharing",
                url: "https://edu-sharing.com"
            };
        }
    };
});