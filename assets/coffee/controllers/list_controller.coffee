ListCtrl = ($scope) ->
  $('#loader').modal { show: true }

  # $.ajax
  #   url: base_url + '/repositories'
  $scope.repositories = [
    {
      name: 'gps',
      branch: 'master',
      commits: 387
    }
  ]

