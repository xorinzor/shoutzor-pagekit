%include "config.liq"

set("log.file.path",                log_file_path_shoutzor)
set("log.stdout",                   log_stdout_enabled_shoutzor)
set("server.telnet",                enable_telnet_shoutzor)
set("server.socket",                enable_socket_shoutzor)
set("server.socket.path",           socket_path_shoutzor)
set("server.socket.permissions",    socket_file_permission)

set("audio.converter.samplerate.libsamplerate.quality", "best")

set("frame.video.width", stream_video_width)
set("frame.video.height", stream_video_height)
set("frame.video.samplerate", stream_video_framerate)

# Add a skip function to a source
# when it does not have one
# by default
def add_skip_command(s) =

 # A command to skip
 def skip(_) =
   source.skip(s)
   "Done!"
 end

 # Register the command:
 server.register(namespace="#{source.id(s)}",
                 usage="skip",
                 description="Skip the current song.",
                 "skip",skip)

end #end:add_skip_commands

# Mixes two streams, with faded transitions between the state when only the
# normal stream is available and when the special stream gets added on top of
# it.
# @category Source / Track Processing
# @param ~delay   Delay before starting the special source.
# @param ~p       Portion of amplitude of the normal source in the mix.
# @param ~normal  The normal source, which could be called the carrier too.
# @param ~special The special source.
def smooth_add(~delay=0.5,~p=0.2,~normal,~special)
  d = delay
  fade.final = fade.final(duration=d*2.)
  fade.initial = fade.initial(duration=d*2.)
  q = 1. - p
  c = amplify
  fallback(track_sensitive=false,
           [special,normal],
           transitions=[
             fun(normal,special)->
               add(normalize=false,
                   [c(p,normal),
                    c(q,fade.final(type="sin",normal)),
                    sequence([blank(duration=d),c(q,special)])]),
             fun(special,normal)->
               add(normalize=false,
                   [c(p,normal),
                    c(q,fade.initial(type="sin",normal))])
           ])
end #end:smooth_add

def append_type(m) =
	[("type", "jingles")]
end

def blank_handler()
  system("curl #{shoutzor_blank_handler_url}")
end

def metadatahandler(m)
	system("curl #{shoutzor_nexttrack_handler_url} > /dev/null &")
end

#In case nothing works anymore, say this
unfallibleSource = single("say: #{shoutzor_error_text}")

#buffer for GStreamer clock ?
video_output = buffer(buffer=0.1,mksafe(video_output))

#jingles
jingles = playlist(conservative=true, mode="randomize", shoutzor_jingles_playlist_path)

#tell the script this source contains the jingles
jingles = map_metadata(append_type, jingles)

#Requester input
input = request.queue(id="shoutzorqueue")

# Attach a skip command to the source input:
add_skip_command(input)

input = on_metadata(metadatahandler, input)

#add Jingles to the main source
input = fallback([delay(840., jingles), input])

#apply crossfade of 5 seconds
input = smart_crossfade(start_next=7., fade_in=5., fade_out=5., conservative=true, input)

#give control over the channels
input = mix(id="sound", [input, unfallibleSource])

#when no audio is detected for longer as 10 seconds, the blank_handler will kick in
input = on_blank(max_blank=10., blank_handler, input)

#create base video layer from static image
video_base = single("../no-video-signal.jpg")

#get video input (if any) from the request list
video_request = drop_audio(input)

#merge both into a video output
video_output = fallback(track_sensitive=false, [video_base, video_request])

#add shoutzor logo to the video output
video_output = video.add_image(
       width=stream_video_width,
       height=stream_video_height,
       x=10,
       y=10,
       file=stream_video_logo,
       video_output)

clock.assign_new(sync=false,[video_output])

result = mux_audio(audio=input, video_output)

#stream output to the wrapper (THIS IS NOT YET TO THE PUBLIC STREAM, CHECK WRAPPER.LIQ)
output.icecast(
  #%mp3(bitrate=stream_bitrate),
  %gstreamer(video="x264enc speed-preset=1",audio="lamemp3enc"),
  mount="/" ^ stream_input_mount,
  id="toWrapper",
  host="localhost", port=stream_input_port, password=stream_input_password,
  input)