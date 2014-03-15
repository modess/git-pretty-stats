angular.module('gitPrettyStats').controller('RepositoryController', ['$scope', '$http', ($scope, $http) ->
  $scope.name = $("input[name='name']").val()
  $('#loader').modal { show: true }

  $http(
    method: 'GET'
    url: base_url + '/repository/' + $scope.name + '/data'
  ).then((response) ->
    $scope.repository   = response.data.repository
    $scope.repositories = response.data.repositories

    charts = response.data.repository.data.charts

    $("a[href='#commits']").trigger 'click'
    renderCommitsByDateChart charts.date
    renderCommitsByHourChart charts.hour
    renderCommitsByDayChart charts.day

    $("a[href='#contributors']").trigger 'click'
    renderCommitsByContributorsChart charts.contributor

    $("a[href='#statistics']").trigger 'click'

    $('#loader').modal 'hide'
  )
])
