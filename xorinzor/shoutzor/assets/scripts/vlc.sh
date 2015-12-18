#!/bin/bash

placeholder="../images/placeholder.ogg"
logo="../images/shoutzor-christmas-logo-small.png"
logo_x_pos = 5
logo_y_pos = 5
logo_transparency = 255
telnetport = 4212
telnetpassword = "hackme"
threads=4
vcodec=theo
acodec=vorb
videoquality=10 #0 lowest, 10 highest
audioquality=10 #0 lowest, 10 highest
width=1920
height=1080
destination="localhost"
port=8000
password="hackme"

vlc "$placeholder" \
--ttl 12 \
--one-instance \
--intf telnet \
--telnet-port $telnetport \
--telnet-password $telnetpassword \
--loop \
--quiet \
--sout-theora-quality=$videoquality \
--sout-vorbis-quality=$audioquality \
--sout "#transcode{sfilter=logo{file='$logo',x=$logo_x_pos,y=$logo_y_pos,transparency=$logo_transparency},deinterlace,hq,threads=$threads,vcodec=$vcodec,acodec=$acodec,ab=$bitrate,channels=2,width=$width,height=$height}:std{access=shout,mux=ogg,dst=source:$output_password@$output_destination:$output_port/$output_mount}" --sout-keep