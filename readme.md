# Mahara Edu-Sharing TinyMCE Plugin

## Installation

- Copy the folders `js/tinymce/plugins/edusharing` and `artefact/edusharing` to the equally named locations inside the your Mahara installation (originally its `htdocs` folder), e.g.,
    ```
    rsync -re ssh artefact/edusharing root@your-host:/var/www/html/mahara/artefact/
    rsync -re ssh js/tinymce/plugins/edusharing root@your-host:/var/www/html/mahara/js/tinymce/plugins/
    ```
- In the Mahara UI:
    - Go to *Administration Menu* (wrench icon) / *Extensions* / *Plugin administration*
    - Under "Plugin type: artefact" click *Install* on the "edusharing" entry.
    - Refresh the page
    - Under "Plugin type: blocktype" click *Install* on the "edusharing/edusharing" entry.
    - Under "Plugin type: artefact" click the gear icon on the entry "edushargin"
    - Under "prefill" enter the URL of your Edu-Sharing instance + `/metadata?format=lms` and click *Prefill fields*
    - For "apphost" enter the IP address of your Mahara installation
    - For "appdomain" enter the domain name that resolves to the above IP address, if any, otherwise the IP address
    - Click *Save*
- In the Edusharing-UI:
    - Go to *Admin-Tools* / *Applications*
    - In the *URL* field, enter the URL to your mahara installation + `/artefact/edusharing/metadata.php`
    - Click *Connect*
- In `lib/web.php`:
    - add `edusharing` to TinyMCE toolbar configuratione, e.g.,
        ```php
        $toolbar = array(null, "toolbar_toggle | ...", "...", "... | edusharing");
        ```
    - add `edusharing` to TinyMCE plugins configuration, e.g.,
        ```
        plugins: "tooltoggle,...,edusharing",
        ```
    - add `",img[*],div[*]"` to `extended_valid_elements`:
        ```js
        tinyMCE.init({
            ...,
            extended_valid_elements:
                ...
                + ",img[*],div[*]"
        })
        ```
    - add the following line just before `// TinyMCE must be included first for some reason we're not sure about`
        ```php
        $javascript_array[] = $wwwroot . '/artefact/edusharing/js/edu.js';
        // TinyMCE must be included first for some reason we're not sure about
        ```
