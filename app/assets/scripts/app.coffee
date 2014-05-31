'use strict'

angular.module('gitPrettyStats', [
  'ui.router',
  'chieffancypants.loadingBar'
])
  .config ($stateProvider, $urlRouterProvider) ->
    $urlRouterProvider.otherwise "/repositories"

    $stateProvider
      .state 'repositories',
        url: '/repositories'
        templateUrl: 'views/main.html'
        controller: 'MainController'
        resolve:
          repositories: (Repository) ->
            Repository.all()
      .state 'repository',
        url: '/repository/:name'
        templateUrl: 'views/repository.html'
        controller: 'RepositoryController'
        resolve:
          repositories: (Repository) ->
            Repository.all()
          repo: ($stateParams, Repository) ->
            Repository.get($stateParams.name)
