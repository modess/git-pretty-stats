(function() {
  'use strict';
  angular.module('gitPrettyStats', ['ui.router', 'snap', 'chieffancypants.loadingBar']).run(function($rootScope, snapRemote) {
    return $rootScope.$on('$locationChangeStart', function() {
      return snapRemote.close();
    });
  }).config(function($stateProvider, $urlRouterProvider, snapRemoteProvider) {
    snapRemoteProvider.globalOptions.touchToDrag = false;
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
      parent: 'repositories',
      url: '/:name',
      templateUrl: 'views/repository.html',
      controller: 'RepositoryController',
      resolve: {
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