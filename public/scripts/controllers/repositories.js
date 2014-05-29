(function() {
  'use strict';
  angular.module('gitPrettyStats').controller('RepositoriesController', function($scope, repositories) {
    return $scope.repositories = repositories.data;
  });

}).call(this);

/*
//@ sourceMappingURL=repositories.js.map
*/