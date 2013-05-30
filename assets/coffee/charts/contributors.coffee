renderCommitsByContributorsChart = (data) ->
    i = 0
    for contributor in data
        contributorDiv = $('<li class="span6"><div id="contributor-'+i+'" class="thumbnail"></div></li>')
        contributorDiv.find('.thumbnail').append $('<h4>' + contributor.contributor + '</h4>')
        contributorDiv.find('.thumbnail').append $('<div id="chart-'+i+'" style="height: 200px; width: 100%"></div>')
        $("#contributors .thumbnails").append contributorDiv
        datapoints = []
        for datapoint in contributor.data
            datapoint.x = eval datapoint.x
            datapoints.push datapoint

        options = {
            zoomEnabled: true
            panEnabled: true
            axisY:
                labelFontSize: 14
            axisX:
                valueFormatString: "YYYY-MM-DD"
                labelFontSize: 12
            data: [
                markerColor: "circle"
                markerBorderColor: "white"
                color: window.primary_color
                dataPoints: datapoints
            ]
        }
        chart = new CanvasJS.Chart "chart-" + i, options
        chart.render()
        i++
