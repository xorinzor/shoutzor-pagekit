# Shoutzor Module
![Shoutzor-logo](./shoutzor-logo.png)

The Shoutzor Module - Requires the Shoutzor Theme to function as intended.

Shoutzor is a system designed to have music playing at events like a LAN-Party.
Comes with an AutoDJ when no music is requested to ensure music keeps playing.

Shoutzor (optionally) uses [echoprint-codegen](https://github.com/echonest/echoprint-codegen) and [echoprint-server](https://github.com/echonest/echoprint-server) for music fingerprinting to prevent double uploads.<br />
Follow the instructions on [this page](http://echoprint.me/start) and the [github readme](https://github.com/echonest/echoprint-codegen) as for how to install echoprint-codegen on your server, then set the correct path in the shoutzor settings.

Also make sure to install the `x264*` and `swh-plugins` packages;<br />
then run:<br />
`cd ~`<br />
`sudo apt-get build-dep vlc`<br>
`sudo apt-get source vlc`<br>
`cd vlc-&lt;VERSION&gt;`<br>
`sudo ./configure`<br>
`sudo make`<br>
`sudo make install`<br />
<br />
`cd ..`<br />
`sudo apt-get build-dep liquidsoap`<br>
`sudo apt-get source liquidsoap`<br>
`cd liquidsoap-&lt;VERSION&gt;`<br>
`sudo ./configure`<br>
`sudo make`<br>
`sudo make install`<br />
<br />
`sudo apt-get install gstreamer1.0*`

# Credits

The official author of the visualizer is [Felix Turner](https://www.airtightinteractive.com/about/).