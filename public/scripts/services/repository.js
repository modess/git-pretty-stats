(function() {
  'use strict';
  angular.module('gitPrettyStats').factory('Repository', function($http) {
    var repository;
    repository = {};
    repository.all = function() {
      return $http({
        method: 'GET',
        url: base_url + '/repository/'
      });
    };
    return repository;
  });

}).call(this);

/*
//@ sourceMappingURL=repository.js.map
*/