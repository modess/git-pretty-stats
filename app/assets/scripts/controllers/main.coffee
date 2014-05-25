'use strict'

angular.module('gitPrettyStats')
  .controller 'MainController', ($scope, repositories) ->
    $scope.repositories = repositories.data
