# pagekit-shoutzor-module

![shoutzor-logo](./xorinzor/shoutzor/shoutzor-logo.png)

The Shoutzor module and required theme for Pagekit.

**Not finished! Still under development**

**Keep in mind this module and theme are not designed to work nice with other modules, they're intended as a standalone app.**

How to install:

1. Copy the "xorinzor" directory into your pagekit's "packages" directory
2. Enable the "Shoutzor" module and set your theme to the "shoutzor-theme" theme.
3. Go to your site's pages and set the shoutzor dashboard page to be your homepage
4. Move any pages you want to be shown in the sidebar to the "main" menu parent
5. Configure your "main" menu parent to be the "main" menu.
6. The website's front-end now is configured, next set-up the back-end
7. Copy `json.xsl` from the `shoutzor-requirements` directory to your `/etc/icecast2/web/` directory and set-up a symlink to `/usr/share/icecast2/web/json.xsl` (also make sure to configure the permissions correctly)
8. Follow the steps in `/shoutzor/readme.md` to install liquidsoap
9. Visit your admin panel to start shoutzor, everything should be up and running now! (TODO: specify what tab specifically)
10. optionally visit the ShoutzorVisualizer repository, download the jar, run it on a computer with a beamer for visual effects at your location
