(function () {
  'use strict';

  angular
    .module('gitPrettyStats', [
      'gitPrettyStats.templates',
      'ui.router',
      'snap',
      'chieffancypants.loadingBar'
    ])
    .run(function($rootScope, $state, $stateParams, snapRemote) {
      $rootScope.$state = $state;
      $rootScope.$stateParams = $stateParams;

      $rootScope.$on('$locationChangeStart', function() {
        snapRemote.close();
      });
    })
    .config(function($stateProvider, $urlRouterProvider, snapRemoteProvider) {
      snapRemoteProvider.globalOptions.touchToDrag = false;
      $urlRouterProvider.otherwise("/repositories");

      $stateProvider
        .state('repositories', {
          url: '/repositories',
          templateUrl: 'main.html',
          controller: 'RepositoriesController',
          resolve: {
            repositories: function(Repository) {
              return Repository.all();
            }
          }
        })
        .state('repository', {
          parent: 'repositories',
          url: '/:name',
          templateUrl: 'repository.html',
          controller: 'RepositoryController',
          resolve: {
            repo: function($stateParams, Repository) {
              return Repository.get($stateParams.name);
            }
          }
        });
    });

}());
