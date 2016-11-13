# SpotPipe
Real time Piping tool for SPOT feeds

Translates a SPOT feed into GeoJson format.

## API
GET parameters:

 - feed: Required, feed id of a Shared Page (see [http://www.findmespot.com/en/index.php?cid=111](http://www.findmespot.com/en/index.php?cid=111))
 - password: Optional, if the feed is private then omitting this parameter will return an error
 - linestring: Optional, set to 1 in order to return a lineString instead of Points
 - all: Optional, set tp 1 in order to return not only the last 50 messages but all of them
 
## Usage example in QGIS
How to: [http://www.fulcrumapp.com/blog/live-data-visualization-in-qgis/](http://www.fulcrumapp.com/blog/live-data-visualization-in-qgis/)

![Example](http://screencast.com/t/6VWpEXlK3oym "Example")