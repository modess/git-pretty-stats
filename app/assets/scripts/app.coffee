'use strict'

angular.module('gitPrettyStats', [
  'ngCookies',
  'ngResource',
  'ngSanitize',
  'ngRoute',
  'chieffancypants.loadingBar'
])
  .config ($routeProvider) ->
    $routeProvider
      .when '/',
        templateUrl: 'views/main.html'
        controller: 'MainController'
        resolve:
          repositories: (Repository) ->
            Repository.all()

      .when '/repository/:name',
        templateUrl: 'views/repository.html'
        controller: 'RepositoryController'
        resolve:
          repo: ($route, Repository) ->
            Repository.get($route.current.params.name)

      .otherwise
        redirectTo: '/'
