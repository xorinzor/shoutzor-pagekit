# Shoutzor Module
![Shoutzor-logo](./shoutzor-logo.png)

The Shoutzor Module - Requires the Shoutzor Theme to function as intended.

Shoutzor is a system designed to have music playing at events like a LAN-Party.
Comes with an AutoDJ when no music is requested to ensure music keeps playing.

Shoutzor (optionally) uses [echoprint-codegen](https://github.com/echonest/echoprint-codegen) and [echoprint-server](https://github.com/echonest/echoprint-server) for music fingerprinting to prevent double uploads.<br />
Follow the instructions on [this page](http://echoprint.me/start) and the [github readme](https://github.com/echonest/echoprint-codegen) as for how to install echoprint-codegen on your server, then set the correct path in the shoutzor settings.

make sure to install the `x264*`, `swh-plugins` and `gstreamer1.0*` packages;<br />
when finished, install `liquidsoap` and `liquidsoap-plugin-all`

@TODO
- Implement Albums and Artists
- Upload page: use Media::STATUS templates for upload response statuses
- Redirect users that are not authenticated to the login screen
- Implement JS API methods
- Have the admin panel use the JS API
- Implement Youtube video search & request functionality
- Check if Media is still on STATUS_PROCESSING after > 15 minutes after uploading (this would safely indicate something has gone wrong)
- Add Queue list with prediction when a song is going to play based on when the current song started playing
