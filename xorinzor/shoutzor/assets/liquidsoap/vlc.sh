#!/bin/bash

file="big_buck_bunny_1080p_h264.mov"
#file="Traced.mp3"
#fallbackvideo="placeholder.ogg"
logo="shoutzor-christmas-logo-small.png"
transparency=255 #0 = fully transparent, 255 = fully opaque
threads=3
vcodec=theo
acodec=vorb
width=1920
height=1080

vlc "$file" \
--loop \
--quiet \
--width 1920 --height 1080 \
--sout "#transcode{sfilter=logo{file='$logo',x=5,y=5,transparency=$transparency},deinterlace,hq,threads=$threads,vcodec=$vcodec,acodec=$acodec,ab=192,channels=2,width=$width,height=$height}:std{access=shout,mux=ogg,dst=source:vledder@localhost:8000/vlc.ogg}" --sout-keep
