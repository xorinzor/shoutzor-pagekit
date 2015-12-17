#!/bin/bash

file="../images/placeholder.ogg"
logo="../images/shoutzor-christmas-logo-small.png"
transparency=255 #0 = fully transparent, 255 = fully opaque
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

vlc "$file" \
--intf dummy vcd:// \
--loop \
--quiet \
--width 1920 --height 1080 \
--sout-theora-quality=$videoquality \
--sout-vorbis-quality=$audioquality \
--sout "#transcode{sfilter=logo{file='$logo',x=5,y=5,transparency=$transparency},deinterlace,hq,threads=$threads,vcodec=$vcodec,acodec=$acodec,ab=192,channels=2,width=$width,height=$height}:std{access=shout,mux=ogg,dst=source:$password@$destination:$port/vlc.ogg}" --sout-keep