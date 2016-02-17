# SystemBro
##### Your server bro watches your servers back and lets you know what's going on (see [agent](https://github.com/jwdeitch/SystemBroAgent))


![App](https://s3-us-west-2.amazonaws.com/8201393personal/s/lkuw7.jpg)


Each agent reports statistics every minute to this application; these stats include server resource usage and access log entries. The machine name distinguishes individual machines. Each result is stored in a mongoDB collection, though beforehand each access log line is parsed for location and platform information. 

**Note: The agent will clear the webserver's access.log file**

### Technologies
- [Vuejs](http://vuejs.org/)
- [jQuery](https://jquery.com/)
- [Lumen](https://lumen.laravel.com/)
- [Chartjs](http://www.chartjs.org/)
- [Semantic-UI](http://semantic-ui.com/)
- [TimeAgo](http://timeago.yarp.com/)
- [MaxMind](http://www.maxmind.com)
- MongoDB
- PHP 5.6.11
- Apache 2.4.12
- Ubuntu 14.04


This app can circumvent adblockers (Google Analytics / Piwik etc.. can sometimes be blocked by the client). You can update the GeoLocation database by running ```php artisan maxmind:update``` from the projects home directory. This will pull the latest MaxMind DB and verify it's checksum. The web interface refreshes every minute automatically.

You can add servers that the app should accept queries for:
![addServer](https://s3-us-west-2.amazonaws.com/8201393personal/s/zqhly.jpg)


You can then select from different servers you have collected from:

![selectServer](https://s3-us-west-2.amazonaws.com/8201393personal/s/fbjnz.jpg)


**Note: I strongly recommend installing this application behind an implementation of HTTP auth**

#### TODO
- [x] detect system (agent)
- [x] Better multiserver handling
- [ ] Getting started / after install page
- [ ] Password auth to access app
- [ ] Dockerize
- [ ] Convert agent to Go
- [ ] Refactor backend


This product includes GeoLite2 data created by MaxMind, available from
[http://www.maxmind.com](http://www.maxmind.com).



#### Technical notes

The bandwidth usage is recorded as bytes sent over the network interface in TX(outbound) and RX(inbound), though these values can spontaneously zero. To remedy this, we can add the local maximums between any arbitrary time interval.

![bandwidth](https://s3-us-west-2.amazonaws.com/8201393personal/s/rvbv5.png)

##### Why are machine names used to destinguish servers, and not IP addresses?
Reason being, if you're on an EC2 instance for example, then everytime you start/stop that instance, you may loose the associated IP, hence the hostname as the servers identifier - it prevents you from constantly having to update the IP whitelist




### License

The MIT License (MIT)

Copyright (c) <Taylor Otwell>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
