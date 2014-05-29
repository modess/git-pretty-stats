'use strict'

angular.module('gitPrettyStats')
  .directive 'gpsCommitsByDayChart', ($timeout) ->
    restrict: 'E'
    replace: true
    template: '<div id="chart-commits-by-day" class="chart" style="width:100%"></div>',
    link: (scope, iElement, iAttrs) ->
      data = scope.charts.day

      $timeout(->
        angular.element("#chart-commits-by-day").highcharts
          chart:
            type: "pie"
          title:
            text: ""
          yAxis:
            title:
              text: ""
          series: [
            name: "Commits"
            data: data
          ]
      , 50)
