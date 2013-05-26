var renderCommitsByDateChart;

renderCommitsByDateChart = function(data) {
  var commitsByDateChart, datapoint, datapoints, options, _i, _len;

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
  commitsByDateChart = new CanvasJS.Chart("commitsByDate", options);
  return commitsByDateChart.render();
};
