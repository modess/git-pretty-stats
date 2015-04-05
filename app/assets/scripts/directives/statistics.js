(function () {
  'use strict';

  angular
    .module('gitPrettyStats')
    .directive('gpsStatistics', function() {
      return {
        restrict: 'E',
        templateUrl: 'statistics.html'
      };
    });

})();
