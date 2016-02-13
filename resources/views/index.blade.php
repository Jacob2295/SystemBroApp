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
                        <div class="statistic">
                            <div class="value">
                                @{{collectionItem.formatted.bandwidth.month}}
                            </div>
                            <div class="label">
                                bandwidth usage this month
                            </div>
                        </div>
                    </div>
                </div>


                <div class="ui grid">
                    <div class="twelve wide column">
                        <table class="ui very compact single line selectable table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Another Status</th>
                                <th>Notes</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>John</td>
                                <td>Approved</td>
                                <td>Approved</td>
                                <td>None</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="four wide column">
                        wefjnwefkjwnefkjwenfnc mcwemn wenfwe febwnf
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

</div>


</body>
</html>