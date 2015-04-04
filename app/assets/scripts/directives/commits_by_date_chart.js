'use strict';
angular
  .module('gitPrettyStats')
  .directive('gpsCommitsByDateChart', function($timeout) {
    return {
      restrict: 'E',
      replace: true,
      template: '<div id="chart-commits-by-date" class="chart" style="width:100%"></div>',

      link: function(scope) {
        var data = scope.charts.date;

        $timeout(function() {
          $('#chart-commits-by-date').highcharts({
            chart: {
              type: 'areaspline',
              zoomType: 'x'
            },
            title: {
              text: ''
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
                text: ''
              }
            },
            series: [
              {
                name: 'Commits',
                data: data.y
              }
            ]
          });
        }, 50);
      }
    };
  });
