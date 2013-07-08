var IndexCtrl;

$(function() {
  window.chartColors = ['#3498DB', '#2ECC71', '#9B59B6', '#E74C3C', '#1ABC9C', '#F39C12', '#95A5A6'];
  return $('#tabs a').click(function(e) {
    e.preventDefault();
    return $(this).tab('show');
  });
});

angular.module("SharedServices", []).config(function($httpProvider) {
  var spinnerFunction;

  $httpProvider.responseInterceptors.push("myHttpInterceptor");
  spinnerFunction = function(data, headersGetter) {};
  $('#loader').modal({
    show: true
  });
  return $httpProvider.defaults.transformRequest.push(spinnerFunction);
}).factory("myHttpInterceptor", function($q, $window) {
  return function(promise) {
    promise.then((function(response) {}, $('#loader').modal('hide')), function(response) {});
    return $('#loader').modal('hide');
  };
});

angular.module("main", ["SharedServices", "ngResource"]).config(function($interpolateProvider) {
  return $interpolateProvider.startSymbol('{[').endSymbol(']}');
}).config(function($routeProvider) {
  return $routeProvider.when('/index', {
    controller: IndexCtrl
  }).otherwise({
    redirectTo: '/index'
  });
});

IndexCtrl = function() {
  $('#loader').modal({
    show: true
  });
  return $.ajax({
    url: $('#stats-endpoint').attr('href'),
    success: function(data) {
      if (typeof data.charts.date === "undefined" && data.charts.date === null) {
        $("#loader h3").html("Shit...");
        $("#loader p").html("Something went wrong");
        return false;
      }
      $('#loader').modal('hide');
      $("a[href='#commits']").trigger('click');
      renderCommitsByDateChart(data.charts.date);
      renderCommitsByHourChart(data.charts.hour);
      renderCommitsByDayChart(data.charts.day);
      $("a[href='#contributors']").trigger('click');
      renderCommitsByContributorsChart(data.charts.contributor);
      return $("a[href='#statistics']").trigger('click');
    }
  });
};
