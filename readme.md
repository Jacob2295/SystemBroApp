# SystemBro
##### Your server bro watches your servers back and lets you know what's going on (see [agent](https://github.com/jwdeitch/SystemBroAgent))


![App](https://s3-us-west-2.amazonaws.com/8201393personal/s/van3f.png)


Each agent reports statistics every minute to this application; these stats include server resource usage and access log entries. The machine name distinguishes individual machines. Each result is stored in a mongoDB collection, though beforehand each access log line is parsed for location and platform information. The bandwidth usage is recorded as bytes sent over the network interface in TX(outbound) and RX(inbound), though these values can spontaneously zero. To remedy this, we can add the local maximums between any arbitrary time interval.

![bandwidth](https://s3-us-west-2.amazonaws.com/8201393personal/s/rvbv5.png)

### Technologies
- [Vuejs](http://vuejs.org/)
- [jQuery](https://jquery.com/)
- [Lumen](https://lumen.laravel.com/)
- [Chartjs](http://www.chartjs.org/)
- [Semantic-UI](http://semantic-ui.com/)
- [TimeAgo](http://timeago.yarp.com/)
- MongoDB
- PHP 5.6.11
- Apache 2.4.12
- Ubuntu 14.04
- Auto refreshing web interface


One more thing to note is that this app can circumvent addblockers (Google Analytics / Piwik etc.. can sometimes be blocked by the client). You can update the GeoLocation database buy running ```php artisan maxmind:update``` from the projects home directory.

#### TODO
- [ ] Refactor backend
- [ ] Adaptive log parsing (nginx/lighttpd etc...)
- [ ] Convert agent to Go
- [ ] Better multiserver handling


#### License
do whatever you want 2016

This product includes GeoLite2 data created by MaxMind, available from
[http://www.maxmind.com](http://www.maxmind.com).
