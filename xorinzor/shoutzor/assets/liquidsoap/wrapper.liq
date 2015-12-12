%include "config.liq"

set("log.file.path",                log_file_path_wrapper)
set("log.stdout",                   log_stdout_enabled_wrapper)
set("server.telnet",                enable_telnet_wrapper)
set("server.socket",                enable_socket_wrapper)
set("server.socket.path",           socket_path_wrapper)
set("server.socket.permissions",    socket_file_permission)

set("frame.video.width", stream_video_width)
set("frame.video.height", stream_video_height)
set("frame.video.samplerate", stream_video_framerate)

#In case nothing works anymore, say this
unfallibleSource = single("say: " ^ shoutzor_error_text)

#give control over the channels
input = mix(id="nothingness", [unfallibleSource])

#create base video layer from static image
video_base = single("../no-video-signal.jpg")

#input from the shoutzor script
live = input.harbor(stream_input_mount, port=stream_input_port, password=stream_input_password)

output = fallback(track_sensitive=false, [live, video_base])

#stream output to the public
output.icecast(
  #%mp3(bitrate=stream_bitrate),
  %gstreamer(video="x264enc speed-preset=1",audio="lamemp3enc"),
  mount="/" ^ stream_output_mount,
  id="shoutzorOutput",
  host=stream_output_host, port=stream_output_port, password=stream_output_password,
  output)