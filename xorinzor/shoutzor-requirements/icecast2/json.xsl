<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
    <xsl:output method="text" encoding="UTF-8" media-type="application/json"/>
    <xsl:template match="/icestats">{
      "contact":"<xsl:value-of select="admin"/>",
      "location":"<xsl:value-of select="location"/>",
      "total_listeners":"<xsl:value-of select="listeners"/>",
      "server_id": "<xsl:value-of select="server_id"/>",
      "mounts" : {<xsl:for-each select="source">
        "<xsl:value-of select="@mount" />": {
          "server_name": "<xsl:choose><xsl:when test="server_name"><xsl:value-of select="server_name" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "server_description": "<xsl:choose><xsl:when test="server_description"><xsl:value-of select="server_description" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "server_type": "<xsl:choose><xsl:when test="server_type"><xsl:value-of select="server_type" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "stream_start": "<xsl:choose><xsl:when test="stream_start"><xsl:value-of select="stream_start" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "bitrate": "<xsl:choose><xsl:when test="bitrate"><xsl:value-of select="bitrate" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "quality": "<xsl:choose><xsl:when test="quality"><xsl:value-of select="quality" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "video_quality": "<xsl:choose><xsl:when test="video_quality"><xsl:value-of select="video_quality" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "frame_size": "<xsl:choose><xsl:when test="frame_size"><xsl:value-of select="frame_size" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "frame_rate": "<xsl:choose><xsl:when test="frame_rate"><xsl:value-of select="frame_rate" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "listeners": "<xsl:choose><xsl:when test="listeners"><xsl:value-of select="listeners" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "listener_peak": "<xsl:choose><xsl:when test="listener_peak"><xsl:value-of select="listener_peak" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "genre": "<xsl:choose><xsl:when test="genre"><xsl:value-of select="genre" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "mount": "<xsl:choose><xsl:when test="@mount"><xsl:value-of select="@mount" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "server_url": "<xsl:choose><xsl:when test="server_url"><xsl:value-of select="server_url" /></xsl:when><xsl:otherwise>Undefined</xsl:otherwise></xsl:choose>",
          "artist": "<xsl:choose><xsl:when test="artist"><xsl:value-of select="artist" /></xsl:when><xsl:otherwise>Unknown</xsl:otherwise></xsl:choose>",
          "title": "<xsl:choose><xsl:when test="title"><xsl:value-of select="title" /></xsl:when><xsl:otherwise>Untitled</xsl:otherwise></xsl:choose>"
        }<xsl:if test="position() != last()">,</xsl:if></xsl:for-each>
      }
    }</xsl:template>
</xsl:stylesheet>
