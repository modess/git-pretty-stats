(function() {
  'use strict';
  angular.module('gitPrettyStats', ['ngCookies', 'ngResource', 'ngSanitize', 'ngRoute']).config(function($routeProvider) {
    return $routeProvider.when('/', {
      templateUrl: 'views/main.html',
      controller: 'MainController',
      resolve: {
        repositories: function(Repository) {
          return Repository.all();
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