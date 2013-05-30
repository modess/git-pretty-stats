var renderCommitsByContributorsChart;

renderCommitsByContributorsChart = function(data) {
  var chart, contributor, contributorDiv, datapoint, datapoints, i, options, _i, _j, _len, _len1, _ref, _results;

  i = 0;
  _results = [];
  for (_i = 0, _len = data.length; _i < _len; _i++) {
    contributor = data[_i];
    contributorDiv = $('<li class="span6"><div id="contributor-' + i + '" class="thumbnail"></div></li>');
    contributorDiv.find('.thumbnail').append($('<h4>' + contributor.contributor + '</h4>'));
    contributorDiv.find('.thumbnail').append($('<div id="chart-' + i + '" style="height: 200px; width: 100%"></div>'));
    $("#contributors .thumbnails").append(contributorDiv);
    datapoints = [];
    _ref = contributor.data;
    for (_j = 0, _len1 = _ref.length; _j < _len1; _j++) {
      datapoint = _ref[_j];
      datapoint.x = eval(datapoint.x);
      datapoints.push(datapoint);
    }
    options = {
      zoomEnabled: true,
      panEnabled: true,
      axisY: {
        labelFontSize: 14
      },
      axisX: {
        valueFormatString: "YYYY-MM-DD",
        labelFontSize: 12
      },
      data: [
        {
          markerColor: "circle",
          markerBorderColor: "white",
          color: window.primary_color,
          dataPoints: datapoints
        }
      ]
    };
    chart = new CanvasJS.Chart("chart-" + i, options);
    chart.render();
    _results.push(i++);
  }
  return _results;
};
