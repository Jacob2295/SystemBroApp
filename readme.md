# SystemBro
##### Your server bro watches your servers back and lets you know what's going on (see [agent](https://github.com/jwdeitch/SystemBroAgent))


![App](https://s3-us-west-2.amazonaws.com/8201393personal/s/lkuw7.jpg)


Each agent reports statistics every minute to this application; these stats include server resource usage and access log entries. The machine name distinguishes individual machines. Each result is stored in a mongoDB collection, though beforehand each access log line is parsed for location and platform information. The bandwidth usage is recorded as bytes sent over the network interface in TX(outbound) and RX(inbound), though these values can spontaneously zero. To remedy this, we can add the local maximums between any arbitrary time interval.

![bandwidth](https://s3-us-west-2.amazonaws.com/8201393personal/s/rvbv5.png)

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


This app can circumvent adblockers (Google Analytics / Piwik etc.. can sometimes be blocked by the client). You can update the GeoLocation database by running ```php artisan maxmind:update``` from the projects home directory. The web interface refreshes every minute automatically.


You can add servers that the app should accept queries for:
![addServer](https://s3-us-west-2.amazonaws.com/8201393personal/s/zqhly.jpg)


You can then select from different servers you have collected from:

![selectServer](https://s3-us-west-2.amazonaws.com/8201393personal/s/fbjnz.jpg)

#### TODO
- [x] detect system (agent)
- [x] Better multiserver handling
- [ ] Dockerize
- [ ] Convert agent to Go
- [ ] Refactor backend


#### License
do whatever you want 2016

This product includes GeoLite2 data created by MaxMind, available from
[http://www.maxmind.com](http://www.maxmind.com).
