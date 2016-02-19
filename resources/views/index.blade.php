<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SystemBro</title>
    <script src="/js/jquery-2.2.0.min.js"></script>
    <script src="/js/Chart.min.js"></script>
    <script src="/js/jquery.timeago.js"></script>
    <script src="/js/vue.js"></script>
    <script src="/js/js.js"></script>
    <script src="/dist/semantic.min.js"></script>


    <link href="/dist/semantic.min.css" rel="stylesheet">
    <link href="/css/css.css" rel="stylesheet">

</head>
<body>
<div class="ui container">
    <div class="systemProfile" v-if="collectionItems.length">

        <div class="sidebar">
            qdqwdqwdqwd
        </div>

        <div class="main collectionItem">
            <button class="ui blue button addSite">
                <i class="plus icon"></i> Add
            </button>
            <div class="ui flowing popup top left transition wide hidden">
                <form class="ui form">
                    <div class="field">
                        <input type="text" name="hostname" class='hostnameField' placeholder="server hostname">
                    </div>
                    <center>
                        <button class="ui blue center basic button submitSite" type="submit">Submit</button>
                        <div class="ui middle aligned divided list">
                            <div class="item" v-for="allowed in addedServers">
                                <div class="content">
                                    @{{allowed}}
                                </div>
                            </div>
                    </center>
                </form>
            </div>
            <select name="skills" class="ui search dropdown">
                <option v-for="allowedServer in allowedServers" value="@{{allowedServer}}">@{{allowedServer}}</option>
            </select>
            (
            <div class="faint">@{{collectionItem.ip}}</div>
            )

            <div class="rightTitle">
                up @{{collectionItem.uptime}}
            </div>
            <hr>
            <div class="mainContent">

                <div class="stats">
                    <div class="ui tiny statistics">
                        <div class="statistic">
                            <div class="value">
                                @{{collectionItem.cpu1min}}
                            </div>
                            <div class="label">
                                CPU activity
                            </div>
                        </div>
                        <div class="statistic">
                            <div class="value">
                                @{{collectionItem.formatted.memPercent}}
                            </div>
                            <div class="label">
                                RAM usage
                            </div>
                        </div>
                        <div class="statistic">
                            <div class="value">
                                @{{collectionItem.formatted.diskPercent}}
                            </div>
                            <div class="label">
                                used disk space
                            </div>
                        </div>
                        <div class="statistic">
                            <div class="value">
                                @{{collectionItem.activeSsh}}
                            </div>
                            <div class="label">
                                SSH session(s)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chart">
                    <canvas id="cpuAndMem" width="400" height="45"></canvas>
                </div>
                <br>

                <div class="ui grid">
                    <div class="twelve wide column">
                        <table class="ui very compact selectable table">
                            <thead>
                            <tr>
                                <th>IP</th>
                                <th>Visited</th>
                                <th>Request</th>
                                <th>OS</th>
                                <th>Browser</th>
                                <th>Country</th>
                                <th>City</th>
                                <th>Total Visits</th>
                                <th>Last HTTP resp.</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="visitor in collectionItem.analytics.recentVisitors">
                                <td>@{{visitor._id}}</td>
                                <td>@{{visitor.time}}</td>
                                <td>@{{visitor.requestedPage}}</td>
                                <td>@{{visitor.device.platform}}</td>
                                <td>@{{visitor.device.browser}}</td>
                                <td>@{{visitor.location.country}}</td>
                                <td>@{{visitor.location.city}}</td>
                                <td>@{{visitor.count}}</td>
                                <td>@{{visitor.status}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="four wide column">
                        <div class="statUnit">
                            <div class="title">
                                Unique visits this...
                            </div>
                            <div class="body">
                                <div class="ui mini statistics">
                                    <div class="statistic">
                                        <div class="label">
                                            day
                                        </div>
                                        <div class="value">
                                            @{{collectionItem.analytics.uniqueVisits.day}}
                                        </div>
                                    </div>
                                    <div class="statistic">
                                        <div class="label">
                                            week
                                        </div>
                                        <div class="value">
                                            @{{collectionItem.analytics.uniqueVisits.week}}
                                        </div>
                                    </div>
                                    <div class="statistic">
                                        <div class="label">
                                            month
                                        </div>
                                        <div class="value">
                                            @{{collectionItem.analytics.uniqueVisits.month}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="statUnit">
                            <div class="title">
                                Total requests this...
                            </div>
                            <div class="body">
                                <div class="ui mini statistics">
                                    <div class="statistic">
                                        <div class="label">
                                            day
                                        </div>
                                        <div class="value">
                                            @{{collectionItem.analytics.totalRequestCount.day}}
                                        </div>
                                    </div>
                                    <div class="statistic">
                                        <div class="label">
                                            week
                                        </div>
                                        <div class="value">
                                            @{{collectionItem.analytics.totalRequestCount.week}}
                                        </div>
                                    </div>
                                    <div class="statistic">
                                        <div class="label">
                                            month
                                        </div>
                                        <div class="value">
                                            @{{collectionItem.analytics.totalRequestCount.month}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="statUnit">
                            <div class="title">
                                Bandwidth used this...
                            </div>
                            <div class="body">
                                <div class="ui mini horizontal statistics">
                                    <div class="statistic">
                                        <div class="value">
                                            @{{collectionItem.formatted.bandwidth.day}}
                                        </div>
                                        <div class="label">
                                            day
                                        </div>
                                    </div>
                                    <div class="statistic">
                                        <div class="value">
                                            @{{collectionItem.formatted.bandwidth.week}}
                                        </div>
                                        <div class="label">
                                            week
                                        </div>
                                    </div>
                                    <div class="statistic">
                                        <div class="value">
                                            @{{collectionItem.formatted.bandwidth.month}}
                                        </div>
                                        <div class="label">
                                            month
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="statUnit">
                            <div class="title">
                                HTTP response codes
                            </div>
                            <div class="body">
                                <div style="width: 100%; height: 100%;">
                                    <canvas id="httpCodes" style="width: 100%; height: auto;"></canvas>
                                    <div id="js-legend" class="chart-legend"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mainContent" v-else>
        <div class="firstInstall">
            <div class="ui icon massive message">
                <i class="sitemap icon"></i>
                <div class="content">
                    <div class="header">
                        Hey there!
                    </div>
                    <p>Looks like there isn't any data to show. Install the <a href="https://github.com/jwdeitch/SystemBroAgent" target="_blank">agent</a> on the machine you want to collect stats from, and you're good to go!</p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>