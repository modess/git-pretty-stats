var renderCommitsByContributorsChart;

renderCommitsByContributorsChart = function(data) {
  var contributor, i, source, template, _i, _len, _results;

  i = 0;
  source = $("#contributor-template").html();
  template = Handlebars.compile(source);
  _results = [];
  for (_i = 0, _len = data.length; _i < _len; _i++) {
    contributor = data[_i];
    contributor.i = i;
    $("#contributors .thumbnails").append(template(contributor));
    $("#chart-" + i).highcharts({
      colors: window.chartColors,
      chart: {
        type: "area",
        zoomType: "x"
      },
      title: {
        text: ""
      },
      plotOptions: {
        series: {
          lineWidth: 0,
          marker: {
            enabled: false
          }
        }
      },
      xAxis: {
        categories: contributor.data.x,
        tickInterval: 30,
        labels: {
          rotation: -45,
          y: 35
        }
      },
      yAxis: {
        title: {
          text: ""
        }
      },
      series: [
        {
          name: "Commits",
          data: contributor.data.y
        }
      ]
    });
    _results.push(i++);
  }
  return _results;
};
