ListCtrl = ($scope, $http) ->
  $http(
    method: 'GET'
    url: base_url + '/repository/all'
  ).then( (response) ->
    $scope.repositories = response.data
  )
