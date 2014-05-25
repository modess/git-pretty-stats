(function() {
  'use strict';
  angular.module('gitPrettyStats').controller('RepositoryController', function($scope, repo) {
    var charts;
    $scope.repository = repo.data.repository;
    $scope.repositories = repo.data.repositories;
    charts = repo.data.repository.data.charts;
    $("#tab-commit-activity").trigger('click');
    $("#tab-commit-contributors").trigger('click');
    return $("#tab-statistics").trigger('click');
  });

}).call(this);

/*
//@ sourceMappingURL=repository.js.map
*/