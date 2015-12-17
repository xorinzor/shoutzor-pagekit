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

set("decoder.file_extensions.gstreamer",["ogg"])

#Fallback video
video_fallback = single(shoutzor_fallback_video)

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

def append_type(m) =
	[("type", "jingles")]
end

def blank_handler()
  system("curl #{shoutzor_blank_handler_url}")
end

def metadatahandler(m)
	system("curl #{shoutzor_nexttrack_handler_url} > /dev/null &")
end

#jingles
jingles = playlist(conservative=true, mode="randomize", shoutzor_jingles_playlist_path)

#tell the script this source contains the jingles
jingles = map_metadata(append_type, jingles)

#Requester input
input = request.queue(id="shoutzorqueue")

# Attach a skip command to the source input:
add_skip_command(input)

#Attach a on_metadata handler
input = on_metadata(metadatahandler, input)

#add Jingles to the main source
input = fallback([delay(840., jingles), input])

#apply crossfade of 5 seconds
input = smart_crossfade(start_next=7., fade_in=5., fade_out=5., conservative=true, input)

#give control over the channels
input = mix(id="sound", [input])

#when no audio is detected for longer as 10 seconds, the blank_handler will kick in
video_request = on_blank(max_blank=10., blank_handler, input)

#merge both into a video output
result = fallback(track_sensitive=false, [video_fallback, video_request])

clock.assign_new(sync=false,[result])

#stream output to the wrapper (THIS IS NOT YET TO THE PUBLIC STREAM, CHECK WRAPPER.LIQ)
output.icecast(
  format="video/mpegts",
  %gstreamer(audio="lamemp3enc", video="x264enc", muxer="mpegtsmux", channels=2),
  mount="/" ^ stream_input_mount,
  id="toWrapper",
  host="localhost", port=stream_input_port, password=stream_input_password,
  input)