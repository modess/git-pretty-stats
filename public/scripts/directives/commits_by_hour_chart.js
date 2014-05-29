(function() {
  'use strict';
  angular.module('gitPrettyStats').directive('gpsCommitsByHourChart', function($timeout) {
    return {
      restrict: 'E',
      replace: true,
      template: '<div id="chart-commits-by-hour" class="chart hour" style="width:100%"></div>',
      link: function(scope, iElement, iAttrs) {
        var data;
        data = scope.charts.hour;
        return $timeout(function() {
          return angular.element("#chart-commits-by-hour").highcharts({
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
        }, 50);
      }
    };
  });

}).call(this);

/*
//@ sourceMappingURL=commits_by_hour_chart.js.map
*/