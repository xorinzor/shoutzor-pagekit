#wrapper.liq config system-variables
wrapperLogFilePath = "/tmp/shoutzor/wrapper.log"
wrapperLogStdout = true
wrapperServerTelnet = false
wrapperServerSocket = true
wrapperServerSocketPath = "/tmp/shoutzor/wrapper"
wrapperServerSocketPermissions = 511

#shoutzor.liq config system-variables
shoutzorLogFilePath = "/tmp/shoutzor/shoutzor.log"
shoutzorLogStdout = true
shoutzorServerTelnet = false
shoutzorServerSocket = true
shoutzorServerSocketPath = "/tmp/shoutzor/shoutzor"
shoutzorServerSocketPermissions = 511

#global configuration variables
wrapperInputListeningMount = "/streaminput"
wrapperInputListeningPort = 1337
wrapperInputListeningPassword = "hackme"
streamBitrate = 192
wrapperOutputHost = "localhost"
wrapperOutputMount = "/shoutzor"
wrapperOutputPort = 8000
wrapperOutputPassword = "hackme"

#use ignore because of warnings
ignore(wrapperLogFilePath)
ignore(wrapperLogStdout)
ignore(wrapperServerTelnet)
ignore(wrapperServerSocket)
ignore(wrapperServerSocketPath)
ignore(wrapperServerSocketPermissions)
ignore(shoutzorLogFilePath)
ignore(shoutzorLogStdout)
ignore(shoutzorServerTelnet)
ignore(shoutzorServerSocket)
ignore(shoutzorServerSocketPath)
ignore(shoutzorServerSocketPermissions)
ignore(wrapperInputListeningMount)
ignore(wrapperInputListeningPort)
ignore(wrapperInputListeningPassword)
ignore(streamBitrate)
ignore(wrapperOutputHost)
ignore(wrapperOutputMount)
ignore(wrapperOutputPort)
ignore(wrapperOutputPassword)
