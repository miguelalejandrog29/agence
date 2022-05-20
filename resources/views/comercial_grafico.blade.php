<div id="chart"></div>
<input type="hidden" id="fecha_labels" value="{{ $fecha_labels }}"></input>
<input type="hidden" id="chartSeries" value="{{ $chartSeries }}"></input>

<script type="text/javascript">    
    //var labelsOK = JSON.parse('{{ $fecha_labels }}'.replace(/&quot;/g,'"'));   

    var options = {
        series: JSON.parse($('#chartSeries').val()),
        chart: {
            height: 350,
            type: 'line',
        },
        stroke: {
            width: [0, 4]
        },
        title: {
            text: 'Performance Comercial'
        },        
        dataLabels: {
            enabled: true,
            enabledOnSeries: [1]
        },
        labels: JSON.parse($('#fecha_labels').val()),
        xaxis: {
            type: 'category'
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>