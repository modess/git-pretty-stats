ListCtrl = ($scope, $http) ->
  $http(
    method: 'GET'
    url: base_url + '/list/fetch'
  ).then( (response) ->
    $scope.repositories = response.data
  )
