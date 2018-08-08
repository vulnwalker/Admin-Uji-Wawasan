$(function() {
    var urlAjax = "pages.php?page=manageServer";
    function getRandomData(memoryUsage) {
        var res = [];
            res.push([0, memoryUsage])
            res.push([1, memoryUsage])
        return res;
    }
    var updateInterval = 300000;
    var plot = $.plot("#memoryUsage", [ getRandomData(80) ], {
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
            max: 100,
            tickColor: 'rgba(0, 0, 0, 0.06)', font: {color: 'rgba(0, 0, 0, 0.4)'}},
        xaxis: { show: false },
        colors: [getUIColor('default'),getUIColor('gray')]
    });
    function update() {
      $.ajax({
        type:'POST',
        data : {
                  idServer : $("#idServer").val()
                },
        url: urlAjax+'&API=memoryUsage',
        success: function(data) {
          var resp = eval('(' + data + ')');
            plot.setData([getRandomData(resp.content.memoryUsage)]);
            plot.draw();
            setTimeout(update, updateInterval);
          }
        });
    }
    update();
});
