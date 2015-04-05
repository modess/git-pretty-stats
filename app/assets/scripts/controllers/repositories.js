(function () {
  'use strict';

  angular
  .module('gitPrettyStats')
  .controller('RepositoriesController', function($scope, repositories) {
    $scope.repositories = repositories.data;
  });

}());
