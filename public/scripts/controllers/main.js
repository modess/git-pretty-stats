(function() {
  'use strict';
  angular.module('gitPrettyStats').controller('MainController', function($scope, Repository) {
    return Repository.all().then(function(response) {
      return $scope.repositories = response.data;
    });
  });

}).call(this);

/*
//@ sourceMappingURL=main.js.map
*/