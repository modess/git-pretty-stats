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
    chart = new CanvasJS.Chart "commitsByDate", options
    chart.render()

renderCommitsByHourChart = (data) ->
    options = {
        data: [
            markerColor: "circle"
            markerBorderColor: "white"
            color: window.primary_color
            dataPoints: data
        ]
    }
    chart = new CanvasJS.Chart "commitsByHour", options
    chart.render()

renderCommitsByDayChart = (data) ->
    options = {
        data: [
            markerColor: "circle"
            markerBorderColor: "white"
            color: window.primary_color
            dataPoints: data
        ]
    }
    chart = new CanvasJS.Chart "commitsByDay", options
    chart.render()

