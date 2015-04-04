'use strict';

angular
  .module('gitPrettyStats')
  .directive('gpsCommitsByDayChart', function($timeout) {
    return {
      restrict: 'E',
      replace: true,
      template: '<div id="chart-commits-by-day" class="chart" style="width:100%"></div>',

      link: function(scope) {
        var data = scope.charts.day;

        $timeout(function() {
          $("#chart-commits-by-day").highcharts({
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
        }, 50);
      }
    };
  });
