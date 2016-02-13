# SystemBro (see [agent](https://github.com/jwdeitch/SystemBroAgent))
##### Your server bro watches your servers back and lets you know what's going on

Each agent reports statistics every minute to this application; these stats include server resource usage and access log entries. The machine name distinguishes individual machines. Each result is stored in a mongoDB collection, though beforehand each access log line is parsed for location and paltform information. The bandwidth usage is recorded as bytes sent over the network interface in TX(outbound) and RX(inbound), though these values can spontiously zero. To remidy this, we can add the local maxmimums between any arbitrary time interval.

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

#### TODO
- [ ] Refactor backend mess
- [ ] Adaptive log parsing (nginx/lighttpd etc...)
- [ ] Convert agent to Go
- [ ] Better multiserver handeing
