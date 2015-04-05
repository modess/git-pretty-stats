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
