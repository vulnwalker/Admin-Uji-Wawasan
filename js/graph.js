$(function() {

    //Interacting with Data Points example

    var memoryUsage = [], cpuUsage = [], diskUsage = [];

    // for (var i = 0; i < 354; i += 31) {
    //     memoryUsage.push([i, Math.random(i)]);
    //     cpuUsage.push([i, Math.random(i)]);
    //     diskUsage.push([i, Math.random(i)]);
    // }
    memoryUsage.push([0, 50]);
    cpuUsage.push([0, 60]);
    diskUsage.push([0, 80]);
    memoryUsage.push([1, 60]);
    cpuUsage.push([1, 40]);
    diskUsage.push([1, 30]);
    memoryUsage.push([2, 20]);
    cpuUsage.push([2, 70]);
    diskUsage.push([2, 60]);

    var plot = $.plot($('#data-example-1'),
        [{ data: memoryUsage, label: 'Memory Usage',color:'red' }, { data: cpuUsage, label: 'CPU Usage',color:'blue' }, { data: diskUsage, label: 'Disk Usage',color:'green' }], {
            series: {
                shadowSize: 0,
                lines: {
                    show: true,
                    lineWidth: 2
                },
                points: { show: true }
            },
            grid: {
                labelMargin: 10,
                hoverable: true,
                clickable: true,
                borderWidth: 1,
                borderColor: 'rgba(82, 167, 224, 0.06)'
            },
            legend: {
                backgroundColor: '#fff'
            },
            yaxis: { tickColor: 'rgba(0, 0, 0, 0.06)', font: {color: 'rgba(0, 0, 0, 0.4)'}},
            xaxis: { tickColor: 'rgba(0, 0, 0, 0.06)', font: {color: 'rgba(0, 0, 0, 0.4)'}},
            colors: [getUIColor('default'), getUIColor('gray'), getUIColor('blue')],
            tooltip: true,
            tooltipOpts: {
                content: 'x: %x, y: %y'
            }
        });

    var previousPoint = null;
    $('#data-example-1').bind('plothover', function (event, pos, item) {
        $('#x').text(pos.x.toFixed(2));
        $('#y').text(pos.y.toFixed(2));
    });

    $('#data-example-1').bind('plotclick', function (event, pos, item) {
        if (item) {
            $('#clickdata').text('You clicked point ' + item.dataIndex + ' in ' + item.series.label + '.');
            plot.highlight(item.series, item.datapoint);
        }
    });


});
