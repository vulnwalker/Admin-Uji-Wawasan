


$(function() {

    // We use an inline data source in the example, usually data would
    // be fetched from a server

    var data = [],
        totalPoints = 300;

    function getRandomData() {

        if (data.length > 0)
            data = data.slice(1);

        // Do a random walk

        while (data.length < totalPoints) {

            var prev = data.length > 0 ? data[data.length - 1] : 50,
                y = prev + Math.random() * 10 - 5;

            if (y < 0) {
                y = 0;
            } else if (y > 100) {
                y = 100;
            }

            data.push(y);
        }

        // Zip the generated y values with the x values

        var res = [];
        for (var i = 0; i < data.length; ++i) {
            res.push([i, data[i]])
        }

        return res;
    }

    // Set up the control widget

    var updateInterval = 30;

    var plot = $.plot("#realTime", [ getRandomData() ], {

        series: {
            lines: {
                show: true,
                lineWidth: 2,
                fill: 0.5,
                fillColor: { colors: [ { opacity: 0.01 }, { opacity: 0.08 } ] }
            },
            shadowSize: 0   // Drawing is faster without shadows
        },
        grid: {
            labelMargin: 10,
            hoverable: true,
            clickable: true,
            borderWidth: 1,
            borderColor: 'rgba(82, 167, 224, 0.06)'
        },
        yaxis: {
            min: 0,
            max: 150,
            tickColor: 'rgba(0, 0, 0, 0.06)', font: {color: 'rgba(0, 0, 0, 0.4)'}},
        xaxis: { show: false },
        colors: [getUIColor('default'),getUIColor('gray')]
    });

    function update() {

        plot.setData([getRandomData()]);

        // Since the axes don't change, we don't need to call plot.setupGrid()

        plot.draw();
        setTimeout(update, updateInterval);
    }

    update();

});

$(function() {

    // Randomly Generated Data

    var dataSet = [
        {label: "Asia", data: 1119630000, color: getUIColor('info') },
        { label: "Latin America", data: 690950000, color: getUIColor('warning') },
        { label: "Africa", data: 1012960000, color: getUIColor('danger') },
        { label: "Oceania", data: 5100000, color: getUIColor('gray') },
        { label: "Europe", data: 727080000, color: getUIColor('primary') },
        { label: "North America", data: 344120000, color: getUIColor('success') }
    ];


    var data = [],
        series = Math.floor(Math.random() * 5) + 3;

    for (var i = 0; i < series; i++) {
        data[i] = {
            label: "Series" + (i + 1),
            data: Math.floor(Math.random() * 100) + 1
        }
    }

    $.plot('#data-donut-1', dataSet, {
        series: {
            pie: {
                innerRadius: 0.5,
                show: true,
            },
        }
    });

    $.plot('#data-donut-2', dataSet, {
        series: {
            pie: {
                show: true
            },
        },
        tooltip: true,
        tooltipOpts: {
            content: "%p.0%, %s"
        },
        grid: {
            hoverable: true,
            clickable: true
        }
    });

    $.plot('#data-donut-3', dataSet, {
        series: {
            pie: {
                show: true,
                radius: 500,
                label: {
                    show: true,
                    formatter: labelFormatter,
                    threshold: 0.1
                }
            },
        },
        legend: {
            show: false
        }
    });

    function labelFormatter(label, series) {
        return "<div style='font-size:12px; text-align:center; padding:5px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }

});
