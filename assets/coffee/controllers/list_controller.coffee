ListCtrl = ($scope, $http) ->
  $http(
    method: 'GET'
    url: base_url + '/repository'
  ).then( (response) ->
    $scope.repositories = response.data
  )
