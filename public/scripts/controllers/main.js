(function() {
  'use strict';
  angular.module('gitPrettyStats').controller('MainController', function($scope, repositories) {
    return $scope.repositories = repositories.data;
  });

}).call(this);

/*
//@ sourceMappingURL=main.js.map
*/