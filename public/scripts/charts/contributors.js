(function() {
  var renderCommitsByContributorsChart;

  renderCommitsByContributorsChart = function(data) {
    var contributor, i, source, template, _i, _len, _results;
    $("#contributors h3").html(data.length + ' ' + $("#contributors h3").html());
    i = 0;
    source = '<div class="col-md-6">' + '<div id="contributor-{{ i }}" class="thumbnail">' + '<h4>{{ name }}<br /><small>{{ email }}</small></h4>' + '<h5>{{ commits }}</h5>' + '<div id="chart-{{ i }}" style="height: 200px; width: 100%"></div>' + '</div>' + '</div>';
    template = Handlebars.compile(source);
    _results = [];
    for (_i = 0, _len = data.length; _i < _len; _i++) {
      contributor = data[_i];
      contributor.i = i;
      $("#contributors .thumbnails").append(template(contributor));
      $("#chart-" + i).highcharts({
        colors: window.chartColors,
        chart: {
          type: "areaspline",
          zoomType: "x"
        },
        title: {
          text: ""
        },
        plotOptions: {
          series: {
            lineWidth: 1,
            marker: {
              enabled: false
            }
          }
        },
        xAxis: {
          categories: contributor.data.x,
          tickInterval: parseInt(contributor.data.x.length / 10),
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

}).call(this);

/*
//@ sourceMappingURL=contributors.js.map
*/