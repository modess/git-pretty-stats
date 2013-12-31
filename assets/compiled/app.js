var IndexCtrl, renderCommitsByContributorsChart, renderCommitsByDateChart, renderCommitsByDayChart, renderCommitsByHourChart, renderStatistics;

$(function() {
  window.chartColors = ['#3498DB', '#2ECC71', '#9B59B6', '#E74C3C', '#1ABC9C', '#F39C12', '#95A5A6'];
  return $('#tabs a').click(function(e) {
    e.preventDefault();
    return $(this).tab('show');
  });
});

angular.module("SharedServices", []).config(function($httpProvider) {
  var spinnerFunction;
  $httpProvider.responseInterceptors.push("myHttpInterceptor");
  spinnerFunction = function(data, headersGetter) {};
  $('#loader').modal({
    show: true
  });
  return $httpProvider.defaults.transformRequest.push(spinnerFunction);
}).factory("myHttpInterceptor", function($q, $window) {
  return function(promise) {
    promise.then((function(response) {}, $('#loader').modal('hide')), function(response) {});
    return $('#loader').modal('hide');
  };
});

angular.module("main", ["SharedServices", "ngResource"]).config(function($interpolateProvider) {
  return $interpolateProvider.startSymbol('{[').endSymbol(']}');
}).config(function($routeProvider) {
  return $routeProvider.when('/index', {
    controller: IndexCtrl
  }).otherwise({
    redirectTo: '/index'
  });
});

IndexCtrl = function() {
  $('#loader').modal({
    show: true
  });
  return $.ajax({
    url: $('#stats-endpoint').attr('href'),
    success: function(data) {
      if (typeof data.charts.date === "undefined" && data.charts.date === null) {
        $("#loader h3").html("Shit...");
        $("#loader p").html("Something went wrong");
        return false;
      }
      $('#loader').modal('hide');
      renderStatistics(data.statistics);
      $("a[href='#commits']").trigger('click');
      renderCommitsByDateChart(data.charts.date);
      renderCommitsByHourChart(data.charts.hour);
      renderCommitsByDayChart(data.charts.day);
      $("a[href='#contributors']").trigger('click');
      renderCommitsByContributorsChart(data.charts.contributor);
      return $("a[href='#statistics']").trigger('click');
    }
  });
};

renderStatistics = function(data) {
  var source, statistic, template, _i, _len, _results;
  source = $("#statistics-template").html();
  template = Handlebars.compile(source);
  _results = [];
  for (_i = 0, _len = data.length; _i < _len; _i++) {
    statistic = data[_i];
    _results.push($("#statistics .thumbnails").append(template(statistic)));
  }
  return _results;
};

renderCommitsByDateChart = function(data) {
  return $("#commitsByDate").highcharts({
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
      categories: data.x,
      tickInterval: parseInt(data.x.length / 20),
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
        data: data.y
      }
    ]
  });
};

renderCommitsByHourChart = function(data) {
  return $("#commitsByHour").highcharts({
    colors: window.chartColors,
    chart: {
      type: "bar"
    },
    title: {
      text: ""
    },
    xAxis: {
      categories: data.x
    },
    yAxis: {
      title: {
        text: ""
      }
    },
    series: [
      {
        name: "Commits",
        data: data.y
      }
    ]
  });
};

renderCommitsByDayChart = function(data) {
  return $("#commitsByDay").highcharts({
    colors: window.chartColors,
    chart: {
      type: "pie"
    },
    title: {
      text: ""
    },
    yAxis: {
      title: {
        text: ""
      }
    },
    series: [
      {
        name: "Commits",
        data: data
      }
    ]
  });
};

renderCommitsByContributorsChart = function(data) {
  var contributor, i, source, template, _i, _len, _results;
  $("#contributors h3").html(data.length + ' ' + $("#contributors h3").html());
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
