(function () {
  'use strict';

  angular
  .module('gitPrettyStats')
  .controller('RepositoryController', function($scope, repositories, repo) {
    $scope.repository   = repo.data;
    $scope.repositories = repositories.data;
    $scope.charts       = $scope.repository.data.charts;
  });

}());
