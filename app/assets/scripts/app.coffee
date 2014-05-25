'use strict'

angular.module('gitPrettyStats', [
  'ngCookies',
  'ngResource',
  'ngSanitize',
  'ngRoute'
])
  .config ($routeProvider) ->
    $routeProvider
      .when '/',
        templateUrl: 'views/main.html'
        controller: 'MainController'
        resolve:
          repositories: (Repository) ->
            Repository.all()
      .otherwise
        redirectTo: '/'
