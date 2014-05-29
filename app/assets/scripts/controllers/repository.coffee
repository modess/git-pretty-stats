'use strict'

angular.module('gitPrettyStats')
  .controller 'RepositoryController', ($scope, repo) ->
    $scope.repository   = repo.data.repository
    $scope.repositories = repo.data.repositories

    $scope.charts = repo.data.repository.data.charts
