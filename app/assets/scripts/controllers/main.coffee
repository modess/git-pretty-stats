'use strict'

angular.module('gitPrettyStats')
  .controller 'MainController', ($scope, Repository) ->
    Repository.all().then((response) ->
      $scope.repositories = response.data
    )
