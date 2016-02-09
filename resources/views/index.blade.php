<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SystemBro</title>
    <script src="/js/jquery-2.2.0.min.js"></script>
    <script src="/js/vue.js"></script>
    <script src="/js/js.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script>
    <link href="/css/bulma.min.css" rel="stylesheet">
    <link href="/css/css.css" rel="stylesheet">

</head>
<body>

<div class="container">
    <h1 class="title">Systems</h1>

    <div class="serverStats">
        <div class="server">
            <div class="message">
                <div class="message-header">
                    Hello World
                </div>
                <div class="message-body">
                    <div class="columns">
                        <div class="column stat">
                            <div class="statTitle">First column</div>
                            <div class="statValue">First Val</div>
                        </div>
                        <div class="column stat">
                            <div class="statTitle">First column</div>
                            <div class="statValue">First Val</div>
                        </div>
                        <div class="column stat">
                            <div class="statTitle">First column</div>
                            <div class="statValue">First Val</div>
                        </div>
                        <div class="column stat">
                            <div class="statTitle">First column</div>
                            <div class="statValue">First Val</div>
                        </div>
                        <div class="column stat">
                            <div class="statTitle">First column</div>
                            <div class="statValue">First Val</div>
                        </div>
                        <div class="column stat">
                            <div class="statTitle">First column</div>
                            <div class="statValue">First Val</div>
                        </div>
                    </div>
                    <div class="chart">
                        <canvas id="myChart" width="400" height="75"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h1 class="title">Analytics</h1>
    <div class="analytics">
        <div class="box geo">
            <div class="box-title">
                Geographical
            </div>
            <div class="box-content">
                <ul class="countries">
                    <li class="country"> United States
                        <ul class="cities">
                            <li>New York</li>
                            <li>Eudj dsnfsm emnf</li>
                            <li>emfwf wefnw eff</li>
                            <li>kwef kwef</li>
                            <li>wefkjf wef</li>
                            <li>weffwefkj ewf</li>
                            <li>...</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <div class="box">
            <div class="box-title">
                Browser Stats
            </div>
            <div class="box-content">
                hello
            </div>
        </div>


        <div class="box">
            <div class="box-title">
                HTTP Resp. Codes
            </div>
            <div class="box-content">
                hello
            </div>
        </div>

    </div>
    <h1 class="title">Error Log</h1>
    <div class="errorLog">

    </div>

</div>


</body>
</html>