renderCommitsByDateChart = (data) ->
    datapoints = []
    for datapoint in data
        datapoint.x = eval datapoint.x
        datapoints.push datapoint

    options = {
        zoomEnabled: true
        panEnabled: true
        axisX:
            valueFormatString: "YYYY-MM-DD"
        data: [
            markerColor: "circle"
            markerBorderColor: "white"
            color: window.primary_color
            type: "splineArea"
            dataPoints: datapoints
        ]
    }
    commitsByDateChart = new CanvasJS.Chart "commitsByDate", options
    commitsByDateChart.render()
