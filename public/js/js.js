$(document).ready(function() {
    Chart.defaults.global.animation = false;
    $('.sidebar').height($('.main').height()+1);

    var vue = new Vue({
        el: '.container',
        data: {
            collectionItems: []
        },
        watch: {
            'collectionItems' : function(collectionItems) {
                var ctx = $("#cpuAndMem").get(0).getContext("2d");
                Chart.defaults.global.responsive = true;

                var labels = [];
                var cpuData = [];
                var memData = [];

                collectionItems.forEach(function(collectionItem) {
                    collectionItem.historicalRecords.forEach(function(historicalRecord) {
                       labels.push(jQuery.timeago(historicalRecord.createdAt * 1000));
                       cpuData.push(historicalRecord.cpu);
                       memData.push(historicalRecord.memory);
                    });
                });

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
            }
        },
        methods: {
            getData: function() {
                $.get('/retrieve').done(function(data) {
                    vue.$set('collectionItems',data)
                });
            }
        }
    });
    vue.getData();
    window.setInterval(function(){
        vue.getData();
        console.log('Refreshed data');
    }, 30000);

});