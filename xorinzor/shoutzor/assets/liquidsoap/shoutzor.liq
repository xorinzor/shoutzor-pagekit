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

#Requester input
input = request.queue(id="shoutzorqueue")
#input = playlist("playlist.pls")

# Attach a skip command to the source input:
add_skip_command(input)

#Attach a on_metadata handler
video_input = on_metadata(metadatahandler, drop_audio(input))

#apply crossfade of 5 seconds
#audio_input = smart_crossfade(start_next=7., fade_in=5., fade_out=5., conservative=true, drop_video(input))
audio_input = drop_video(input)

#give control over the channels
audio_input = mix(id="sound", [audio_input])

#when no audio is detected for longer as 10 seconds, the blank_handler will kick in
audio_input = on_blank(max_blank=10., blank_handler, audio_input)

#merge both into a video output
video_result = add([video_fallback, video_input])

result = mux_audio(audio=audio_input, drop_audio(video_result))

clock.assign_new(sync=false, [result])

#stream output to the wrapper (THIS IS NOT YET TO THE PUBLIC STREAM, CHECK WRAPPER.LIQ)
output.icecast(
  format="video/mpegts",
  %gstreamer(audio="lamemp3enc", video="x264enc", muxer="mpegtsmux", channels=2),
  mount="/#{stream_input_mount}",
  icy_metadata="false",
  id="toWrapper",
  host="localhost", port=stream_input_port, password=stream_input_password,
  result)