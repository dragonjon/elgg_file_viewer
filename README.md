Elgg File Viewer
================

An extension to the file plugin that integrates third-party services and providers to render files inline:

* Microsoft Office files (Office Web Viewer integration)
* PDFs and other common file types (Google Docs Viewer integration)
* Video/Audio (Video.js Player)
* Text/Code (Prism.js integration)

## Notes ##

### Terms of Use

Please make sure you comply with the Terms of Service for individual service providers
* Office Web Viewer - http://office.microsoft.com/en-us/web-apps/view-office-documents-online-FX102727753.aspx
* Google Docs Viewer - https://docs.google.com/viewer
* Video.js - http://videojs.com/
* Prism.js - http://prismjs.com/
* ffmpeg - http://www.ffmpeg.org/

### FFMpeg

You can enable ffmpeg conversion of uploaded video and audio files to convert them to web compatible formats thus ensuring that Video.js works properly.
To read more about ffmpeg go to http://www.ffmpeg.org/. You will need ffmpeg library installed on your server for this to work. It is usually located at /usr/local/bin/ffmpeg. Google the rest.


## Acknowledgements ##

* Initial development of the plugin was commissioned and sponsored by ArckInteractive (www.arckinteractive.com)


## Screenshots ##

![alt text](https://raw.github.com/hypeJunction/elgg_file_viewer/master/screenshots/pdf.png "PDF")
![alt text](https://raw.github.com/hypeJunction/elgg_file_viewer/master/screenshots/powerpoint.png "Powerpoint")
![alt text](https://raw.github.com/hypeJunction/elgg_file_viewer/master/screenshots/video.jpg "Video")