(function() {
  'use strict';
  angular.module('gitPrettyStats', ['ngCookies', 'ngResource', 'ngSanitize', 'ngRoute', 'chieffancypants.loadingBar']).config(function($routeProvider) {
    return $routeProvider.when('/', {
      templateUrl: 'views/main.html',
      controller: 'MainController',
      resolve: {
        repositories: function(Repository) {
          return Repository.all();
        }
      }
    }).when('/repository/:name', {
      templateUrl: 'views/repository.html',
      controller: 'RepositoryController',
      resolve: {
        repo: function($route, Repository) {
          return Repository.get($route.current.params.name);
        }
      }
    }).otherwise({
      redirectTo: '/'
    });
  });

}).call(this);

/*
//@ sourceMappingURL=app.js.map
*/