$(document).ready(function () {
    Chart.defaults.global.animation = false;
    $('.sidebar').height($('.main').height() + 1);

    var vue = new Vue({
        el: '.container',
        data: {
            collectionItem: [],
            collectionItems: [],
            allowedServers: [],
            collectionInView: 0
        },
        watch: {
            'collectionItem': function (collectionItem) {
                var ctx = $("#cpuAndMem").get(0).getContext("2d");
                Chart.defaults.global.responsive = true;
                Chart.defaults.global.scaleFontSize = 0;

                var labels = [];
                var cpuData = [];
                var memData = [];

                var httpResponseCodes = [];

                collectionItem.historicalRecords.forEach(function (historicalRecord) {
                    labels.push(jQuery.timeago(historicalRecord.createdAt * 1000));
                    cpuData.push(historicalRecord.cpu);
                    memData.push(historicalRecord.memory);
                });

                collectionItem.analytics.HttpResponseCodeCount.forEach(function (responseCount) {

                    switch (responseCount._id) {
                        case '500':
                            color = "#F7464A";
                            highlight = "#FF5A5E";
                            break;
                        case '200':
                            color = '#dffdcf';
                            highlight = '#b6ceab';
                            break;
                        case '400':
                            color = '#AAB3AB';
                            highlight = '#646a64';
                            break;
                        default:
                            color = '#FBF2DF';
                            highlight = '#F8ECC2';
                    }

                    httpResponseCodes.push({
                        value: responseCount.count,
                        color: color,
                        highlight: highlight,
                        label: responseCount._id
                    });
                });

                var piCtx = $("#httpCodes").get(0).getContext("2d");
                var myPieChart = new Chart(piCtx).Pie(httpResponseCodes, {tooltipTemplate: "<%= value %>"});

                document.getElementById('js-legend').innerHTML = myPieChart.generateLegend();

                var data = {
                    labels: labels.reverse(),
                    datasets: [
                        {
                            label: "My First dataset",
                            fillColor: "rgba(220,220,220,0.2)",
                            strokeColor: "rgba(220,220,220,1)",
                            pointColor: "rgba(220,220,220,1)",
                            pointStrokeColor: "#fff",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(220,220,220,1)",
                            data: cpuData.reverse()
                        },
                        {
                            label: "My Second dataset",
                            fillColor: "rgba(151,187,205,0.2)",
                            strokeColor: "rgba(151,187,205,1)",
                            pointColor: "rgba(151,187,205,1)",
                            pointStrokeColor: "#fff",
                            pointHighlightFill: "#fff",
                            pointHighlightStroke: "rgba(151,187,205,1)",
                            data: memData.reverse()
                        }
                    ]
                };
                var myLineChart = new Chart(ctx).Line(data);

                $('.ui.dropdown').dropdown({
                    onChange: function (val) {
                        vue.collectionItems.forEach(function (server, index) {
                            if (server._id == val) {
                                vue.$set('collectionItem', vue.collectionItems[index]);
                                vue.$set('collectionInView', index);
                            }
                        });
                    }
                });
            }
        },
        methods: {
            getData: function () {
                $.get('/retrieve').done(function (data) {
                    vue.$set('allowedServers', data.allowedServers);
                    vue.$set('collectionItem', data.servers[vue.collectionInView]);
                    vue.$set('collectionItems', data.servers);
                });
            }
        }
    });
    vue.getData();
    window.setInterval(function () {
        vue.getData();
        console.log('Refreshed data');
    }, 30000);

});