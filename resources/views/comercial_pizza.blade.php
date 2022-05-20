<div class="row">
    <div class="col"></div>
    <div class="col">
        <div id="chart"></div>
    </div>
    <div class="col"></div>
</div>

<input type="hidden" id="chartLabels" value="{{ $chartLabels }}"></input>
<input type="hidden" id="chartSeries" value="{{ $chartSeries }}"></input>

<script type="text/javascript">
    var listseries = JSON.parse($('#chartSeries').val());
    var chartSeries = listseries.map(function(xserie) {
        return parseFloat(xserie);
    });
    var options = {
        series: chartSeries,
        chart: {
            width: 680,
            type: 'pie',
        },
        labels: JSON.parse($('#chartLabels').val()),
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 320
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>