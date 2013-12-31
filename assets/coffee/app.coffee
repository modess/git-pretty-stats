$ ->
  window.chartColors = ['#3498DB', '#2ECC71', '#9B59B6', '#E74C3C', '#1ABC9C', '#F39C12', '#95A5A6']
  $('#tabs a').click(
    (e) ->
      e.preventDefault()
      $(this).tab 'show'
  )

angular.module("main", ["ngResource", "ngRoute"])
.config ($routeProvider) ->
  $routeProvider
  .when '/list',
    templateUrl: 'assets/templates/list.html'
    controller: 'ListCtrl'
  .otherwise
    redirectTo: '/list'
