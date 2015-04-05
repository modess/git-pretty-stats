(function () {
  'use strict';

  angular
    .module('gitPrettyStats')
    .directive('gpsCommitsByHourChart', function($timeout) {
      return {
        restrict: 'E',
        replace: true,
        template: '<div id="chart-commits-by-hour" class="chart hour" style="width:100%"></div>',

        link: function(scope) {
          var data = scope.charts.hour;

          $timeout(function() {
            $("#chart-commits-by-hour").highcharts({
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

})();
