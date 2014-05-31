(function() {
  'use strict';
  angular.module('gitPrettyStats', ['ui.router', 'chieffancypants.loadingBar']).config(function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise("/repositories");
    return $stateProvider.state('repositories', {
      url: '/repositories',
      templateUrl: 'views/main.html',
      controller: 'MainController',
      resolve: {
        repositories: function(Repository) {
          return Repository.all();
        }
      }
    }).state('repository', {
      url: '/repository/:name',
      templateUrl: 'views/repository.html',
      controller: 'RepositoryController',
      resolve: {
        repositories: function(Repository) {
          return Repository.all();
        },
        repo: function($stateParams, Repository) {
          return Repository.get($stateParams.name);
        }
      }
    });
  });

}).call(this);

/*
//@ sourceMappingURL=app.js.map
*/