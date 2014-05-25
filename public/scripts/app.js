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
    }).otherwise({
      redirectTo: '/'
    });
  }).config(function(cfpLoadingBarProvider) {
    return cfpLoadingBarProvider.includeSpinner = false;
  });

}).call(this);

/*
//@ sourceMappingURL=app.js.map
*/