(function () {
  'use strict';

  angular
    .module('gitPrettyStats')
    .directive('gpsRepositoryList', function() {
      return {
        restrict: 'E',
        templateUrl: 'repository_list.html',
        scope: {
          'repositories': '=',
          'inline': '@'
        }
      };
    });

})();
