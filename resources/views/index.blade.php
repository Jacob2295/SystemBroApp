<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SystemBro</title>
    <script src="/js/jquery-2.2.0.min.js"></script>
    <script src="/js/vue.js"></script>
    <script src="/js/js.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
    <script src="/dist/semantic.min.js"></script>
    <link href="/dist/semantic.min.css" rel="stylesheet">
    <link href="/css/css.css" rel="stylesheet">

</head>
<body>
<div class="ui container">
    <div class="systemProfile">

        <div class="sidebar">
            qdqwdqwdqwd
        </div>

        <div class="main collectionItem" v-for="collectionItem in collectionItems">
            <div class="title">@{{collectionItem._id}}</div>
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
                                <div class="ui mini statistics">
                                    <div class="statistic">
                                        <div class="label">
                                            day
                                        </div>
                                        <div class="value">
                                            @{{collectionItem.formatted.bandwidth.day}}
                                        </div>
                                    </div>
                                    <div class="statistic">
                                        <div class="label">
                                            week
                                        </div>
                                        <div class="value">
                                            @{{collectionItem.formatted.bandwidth.week}}
                                        </div>
                                    </div>
                                    <div class="statistic">
                                        <div class="label">
                                            month
                                        </div>
                                        <div class="value">
                                            @{{collectionItem.formatted.bandwidth.month}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

</div>


</body>
</html>