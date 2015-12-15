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

set("decoder.file_extensions.gstreamer",["ogg"])

offline_source = single("../images/placeholder.ogg")

#input from the shoutzor script
#live_source = input.harbor(stream_input_mount, port=stream_input_port, password=stream_input_password)
live_source = input.http(autostart=true,"http://localhost:8000/vlc.ogg")

video_input = fallback(track_sensitive=false, [live_source, offline_source])

video_output = video.add_image(
       width=stream_video_logo_width, height=stream_video_logo_height,
       x=5, y=5,
       file=stream_video_logo,
       drop_audio(video_input))

result = mux_video(video=video_output, drop_video(video_input))
result = mksafe(result)

clock.assign_new(sync=false,[result])

#stream output to the public
output.icecast(
  format="video/mpegts",
  %gstreamer(audio="lamemp3enc", video="x264enc", muxer="mpegtsmux", channels=2),
  mount="/#{stream_output_mount}",
  id="shoutzorOutput",
  icy_metadata="false",
  fallible=false,
  host=stream_output_host, port=stream_output_port, password=stream_output_password,
  result)