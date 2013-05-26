var CommitsByDate, rgb2hex;

angular.module("app", []).config([
  "$interpolateProvider", function($interpolateProvider) {
    $interpolateProvider.startSymbol("[[");
    return $interpolateProvider.endSymbol("]]");
  }
]);

$(function() {
  return window.primary_color = rgb2hex($("#primary-color").css('color'));
});

rgb2hex = function(rgb) {
  rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
  return "#" + ("0" + parseInt(rgb[1], 10).toString(16)).slice(-2) + ("0" + parseInt(rgb[2], 10).toString(16)).slice(-2) + ("0" + parseInt(rgb[3], 10).toString(16)).slice(-2);
};

CommitsByDate = function($scope, $http) {
  return $http.get("/stats").success(function(data) {
    return renderCommitsByDateChart(data.commits_by_date);
  });
};
