var renderCommitsByDateChart, renderCommitsByDayChart, renderCommitsByHourChart;

renderCommitsByDateChart = function(data) {
  var chart, datapoint, datapoints, options, _i, _len;

  datapoints = [];
  for (_i = 0, _len = data.length; _i < _len; _i++) {
    datapoint = data[_i];
    datapoint.x = eval(datapoint.x);
    datapoints.push(datapoint);
  }
  options = {
    zoomEnabled: true,
    panEnabled: true,
    axisX: {
      valueFormatString: "YYYY-MM-DD"
    },
    data: [
      {
        markerColor: "circle",
        markerBorderColor: "white",
        color: window.primary_color,
        type: "splineArea",
        dataPoints: datapoints
      }
    ]
  };
  chart = new CanvasJS.Chart("commitsByDate", options);
  return chart.render();
};

renderCommitsByHourChart = function(data) {
  var chart, options;

  options = {
    data: [
      {
        markerColor: "circle",
        markerBorderColor: "white",
        color: window.primary_color,
        dataPoints: data
      }
    ]
  };
  chart = new CanvasJS.Chart("commitsByHour", options);
  return chart.render();
};

renderCommitsByDayChart = function(data) {
  var chart, options;

  options = {
    data: [
      {
        markerColor: "circle",
        markerBorderColor: "white",
        color: window.primary_color,
        dataPoints: data
      }
    ]
  };
  chart = new CanvasJS.Chart("commitsByDay", options);
  return chart.render();
};
