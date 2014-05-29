(function() {
  'use strict';
  angular.module('gitPrettyStats').directive('gpsCommitsByDayChart', function($timeout) {
    return {
      restrict: 'E',
      replace: true,
      template: '<div id="chart-commits-by-day" class="chart" style="width:100%"></div>',
      link: function(scope, iElement, iAttrs) {
        var data;
        data = scope.charts.day;
        return $timeout(function() {
          return angular.element("#chart-commits-by-day").highcharts({
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

}).call(this);

/*
//@ sourceMappingURL=commits_by_day_chart.js.map
*/