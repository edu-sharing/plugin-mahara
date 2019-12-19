(() => {
    /**
     * Allows to send request-response type messages to a dialog.
     * 
     * Register the `onMessage` function when creating the dialog. Then use `send` to send messages and receive
     * responses via the returned promise.
     */
    class MessageHandler {
        /**
         * @access private
         */
        static messageId = 0;

        /**
         * Maps messageIds to resolve / reject functions of the promise being awaited.
         * @access private
         * @type {Object.<string, { resolve: (value?: any) => void, reject: (reason?: any) => void }>}
         */
        promiseFunctionsMap = {};

        onMessage(dialogApi, details) {
            const message = details.content;
            if (typeof message.id !== 'number') {
                throw new Error('Missing response id');
            }
            const { resolve, reject } = this.promiseFunctionsMap[message.id];
            if (!resolve || !reject) {
                throw new Error(`Didn\'t find message with id=${id} in map`);
            }
            if (message.error) {
                reject(message.error);
            } else {
                resolve(message.data);
            }
            delete this.promiseFunctionsMap[message.id];
        }

        /**
         * Send a message to the registered dialog.
         * @param {DialogInstanceApi} dialog - the dialog to send the message to
         * @param {string} action - indentifier for the dialog to subscribe to
         * @param {any} data - any data to include in the message
         * @returns {Promise<any>} response data
         */
        send(dialog, action, data) {
            const id = MessageHandler.messageId++;
            return new Promise((resolve, reject) => {
                dialog.sendMessage({ action, data, id });
                this.promiseFunctionsMap[id] = { resolve, reject };
            });
        }
    }

    function addEdusharingIcon(editor) {
        editor.ui.registry.addIcon(
            'edusharing',
            `
                <svg  version="1.1" id="Layer_1" xmlns="&ns_svg;" xmlns:xlink="&ns_xlink;" width="19.938" height="19.771"
                    viewBox = "0 0 19.938 19.771" overflow = "visible" enable - background="new 0 0 19.938 19.771" xml: space = "preserve" >
                        <polygon fill="#3162A7" points="2.748,19.771 0.027,15.06 2.748,10.348 8.188,10.348 10.908,15.06 8.188,19.771 " />
                        <polygon fill="#7F91C3" points="11.776,14.54 9.056,9.829 11.776,5.117 17.218,5.117 19.938,9.829 17.218,14.54 " />
                        <polygon fill="#C1C6E3" points="2.721,9.423 0,4.712 2.721,0 8.161,0 10.882,4.712 8.161,9.423 " />
                </svg >
            `
        );
    }

    function registerEnterKeyupEdusharing(editor) {
        //tinymce copies tags/class on pressing enter, we prevent that here
        editor.on('keyup', function (e) {
            if (13 === e.keyCode) {
                var node = editor.selection.getNode().parentElement;
                if (node.classList.contains('edusharingObject') && node.id == false) {
                    editor.insertContent('</div>');
                    node.remove();
                }
                editor.insertContent('<p></p>');
            }
        });
    }

    function addEdusharingButton(editor, url) {
        const messageHandler = new MessageHandler();
        editor.ui.registry.addButton('edusharing', {
            icon: 'edusharing',
            onAction: function () {
                if (editor.selection.getNode().classList.contains('edusharingObject')
                    || editor.selection.getNode().parentNode.classList.contains('edusharingObject')
                ) {
                    console.log('edit mode not implemented');
                    return;
                }
                // Open window
                editor.windowManager.openUrl({
                    title: 'Material aus edu-sharing einfügen',
                    url: url + '/dialog.php',
                    width: 950,
                    height: 600,
                    buttons: [
                        {
                            type: 'custom',
                            primary: true,
                            text: 'Insert',
                            name: 'insert'
                        },
                        {
                            type: 'cancel',
                            text: 'Close',
                            onclick: 'close'
                        }
                    ],
                    onAction: async (dialog, details) => {
                        switch (details.name) {
                            case 'insert':
                                const response = await messageHandler.send(dialog, 'insert');
                                insertEdusharing(editor, response);
                                dialog.close();
                                break;
                            default:
                                throw new Error('not implemented');
                        }
                    },
                    onMessage: messageHandler.onMessage.bind(messageHandler)
                });
            }
        });
    }

    function insertEdusharing(editor, data) {
        // Insert the contents of the dialog.html textarea into the editor
        const title = data.node.title || data.node.name;
        const style = (() => {
            switch (data.alignment) {
                case 'left': return 'style="float:left"';
                case 'right': return 'style="float:right"';
                case 'block': return 'style="display:block"';
                default: return '';
            }
        })();

        let srcUrl = data.node.preview.url;
        const resourceId = Math.floor(Math.random() * 10000) + 1000;
        srcUrl += '&objectId=' + data.node.ref.id;
        srcUrl += '&objectUrl=' + data.node.objectUrl;
        srcUrl += '&title=' + data.title;
        srcUrl += '&mediatype=' + data.node.mediatype;
        srcUrl += '&mimetype=' + data.node.mimetype;
        srcUrl += '&contentVersion=' + data.node.contentVersion;
        srcUrl += '&version=' + data.version;
        srcUrl += '&resourceId=' + parseInt(resourceId, 10);
        srcUrl += '&alignment=' + data.alignment;
        srcUrl += '&alt=' + data.title;

        if (data.node.mediatype.indexOf('image') !== -1
            || data.node.mediatype.indexOf('video') !== -1
            || data.node.mediatype.indexOf('h5p') !== -1
            || data.node.mediatype.indexOf('folder') !== -1
        ) {
            srcUrl += '&width=' + data.width;
            srcUrl += '&height=' + data.height;
            editor.insertContent('<img ' + style + ' src="' + srcUrl + '" ' + 'class="edusharingObject" ' + '/>');
        } else {
            editor.insertContent('<a ' + style + 'class="edusharingObject" href="' + srcUrl + '">' + title + '</a>');
        }
    }

    tinymce.PluginManager.add('edusharing', function (editor, url) {
        addEdusharingIcon(editor);
        registerEnterKeyupEdusharing(editor);
        // Do not include button in forums since we can't set a usage there
        if (!window.location.href.includes('/forum/')) {
            addEdusharingButton(editor, url);
        }
        return {
            getMetadata: function () {
                return {
                    name: "edu-sharing",
                    url: "https://edu-sharing.com"
                };
            }
        };
    });
})();
