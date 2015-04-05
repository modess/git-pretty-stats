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

(function () {
  'use strict';

  angular
    .module('gitPrettyStats')
    .directive('gpsStatistics', function() {
      return {
        restrict: 'E',
        templateUrl: 'statistics.html'
      };
    });

})();

(function () {
  'use strict';

  angular
    .module('gitPrettyStats')
    .directive('gpsContributor', function($timeout) {
      return {
        restrict: 'E',
        replace: true,
        templateUrl: 'contributor.html',
        scope: {
          contributor: '='
        },

        link: function(scope, iElement) {
          $timeout(function() {
            $(iElement).find('.chart').highcharts({
              chart: {
                type: "areaspline",
                zoomType: "x"
              },
              title: {
                text: ""
              },
              plotOptions: {
                series: {
                  lineWidth: 1,
                  marker: {
                    enabled: false
                  }
                }
              },
              xAxis: {
                categories: scope.contributor.data.x,
                tickInterval: parseInt(scope.contributor.data.x.length / 10),
                labels: {
                  rotation: -45,
                  y: 35
                }
              },
              yAxis: {
                title: {
                  text: ""
                }
              },
              series: [
                {
                  name: "Commits",
                  data: scope.contributor.data.y
                }
              ]
            });
          }, 50);
        }
      };
    });

})();

(function () {
  'use strict';

  angular
    .module('gitPrettyStats')
    .directive('gpsCommitsByHourChart', function($timeout) {
      return {
        restrict: 'E',
        replace: true,
        template: '<div id="chart-commits-by-hour" class="chart hour" style="width:100%"></div>',

        link: function(scope) {
          var data = scope.charts.hour;

          $timeout(function() {
            $("#chart-commits-by-hour").highcharts({
              chart: {
                type: "bar"
              },
              title: {
                text: ""
              },
              xAxis: {
                categories: data.x
              },
              yAxis: {
                title: {
                  text: ""
                }
              },
              series: [
                {
                  name: "Commits",
                  data: data.y
                }
              ]
            });
          }, 50);
        }
      };
    });

})();

(function () {
  'use strict';

  angular
    .module('gitPrettyStats')
    .directive('gpsCommitsByDayChart', function($timeout) {
      return {
        restrict: 'E',
        replace: true,
        template: '<div id="chart-commits-by-day" class="chart" style="width:100%"></div>',

        link: function(scope) {
          var data = scope.charts.day;

          $timeout(function() {
            $("#chart-commits-by-day").highcharts({
              chart: {
                type: "pie"
              },
              title: {
                text: ""
              },
              yAxis: {
                title: {
                  text: ""
                }
              },
              series: [
                {
                  name: "Commits",
                  data: data
                }
              ]
            });
          }, 50);
        }
      };
    });

})();

(function () {
  'use strict';

  angular
    .module('gitPrettyStats')
    .directive('gpsCommitsByDateChart', function($timeout) {
      return {
        restrict: 'E',
        replace: true,
        template: '<div id="chart-commits-by-date" class="chart" style="width:100%"></div>',

        link: function(scope) {
          var data = scope.charts.date;

          $timeout(function() {
            $('#chart-commits-by-date').highcharts({
              chart: {
                type: 'areaspline',
                zoomType: 'x'
              },
              title: {
                text: ''
              },
              plotOptions: {
                series: {
                  lineWidth: 1,
                  marker: {
                    enabled: false
                  }
                }
              },
              xAxis: {
                categories: data.x,
                tickInterval: parseInt(data.x.length / 20),
                labels: {
                  rotation: -45,
                  y: 35
                }
              },
              yAxis: {
                title: {
                  text: ''
                }
              },
              series: [
                {
                  name: 'Commits',
                  data: data.y
                }
              ]
            });
          }, 50);
        }
      };
    });

})();

(function () {
  'use strict';

  angular
    .module('gitPrettyStats')
    .factory('Repository', function($http) {
      var repository = {};

      repository.all = function() {
        return $http({
          method: 'GET',
          url: base_url + '/repository/'
        });
      };

      repository.get = function(name) {
        return $http({
          method: 'GET',
          url: base_url + '/repository/' + name
        });
      };

      return repository;
    });

})();

(function () {
  'use strict';

  angular
  .module('gitPrettyStats')
  .controller('RepositoryController', function($scope, repositories, repo) {
    $scope.repository   = repo.data;
    $scope.repositories = repositories.data;
    $scope.charts       = $scope.repository.data.charts;
  });

}());

(function () {
  'use strict';

  angular
  .module('gitPrettyStats')
  .controller('RepositoriesController', function($scope, repositories) {
    $scope.repositories = repositories.data;
  });

}());
