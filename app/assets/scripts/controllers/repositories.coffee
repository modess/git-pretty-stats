'use strict'

angular.module('gitPrettyStats')
  .controller 'RepositoriesController', ($scope, repositories) ->
    $scope.repositories = repositories.data
