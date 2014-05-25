(function() {
  'use strict';
  angular.module('gitPrettyStats').controller('RepositoryController', function($scope, repo) {
    $scope.repository = repo.data.repository;
    $scope.repositories = repo.data.repositories;
    return $scope.charts = repo.data.repository.data.charts;
  });

}).call(this);

/*
//@ sourceMappingURL=repository.js.map
*/