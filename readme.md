TinyMCE plugin
- add edu sharing plugin folder to js/tinymce/plugins/ 
- in lib/web.php
    - add edusharing to TinyMCE toolbar configuration
    - add edusharing to TinyMCE plugins configuration
    - add ",img[*],div[*]" to "extended_valid_elements" //prüfen

edu-sharing filter
- in lib/web.php add "$javascript_array[] = $jsroot . 'edu.js';" just before "// TinyMCE must be included first for some reason we're not sure about"