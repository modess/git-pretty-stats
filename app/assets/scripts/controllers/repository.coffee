'use strict'

angular.module('gitPrettyStats')
  .controller 'RepositoryController', ($scope, repo) ->
    $scope.repository   = repo.data.repository
    $scope.repositories = repo.data.repositories

    charts = repo.data.repository.data.charts

    $("#tab-commit-activity").trigger 'click'
    # renderCommitsByDateChart charts.date
    # renderCommitsByHourChart charts.hour
    # renderCommitsByDayChart charts.day

    $("#tab-commit-contributors").trigger 'click'
    # renderCommitsByContributorsChart charts.contributor

    $("#tab-statistics").trigger 'click'
