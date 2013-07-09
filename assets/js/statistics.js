var renderStatistics;

renderStatistics = function(data) {
  var source, statistic, template, _i, _len, _results;

  source = $("#statistics-template").html();
  template = Handlebars.compile(source);
  _results = [];
  for (_i = 0, _len = data.length; _i < _len; _i++) {
    statistic = data[_i];
    _results.push($("#statistics .thumbnails").append(template(statistic)));
  }
  return _results;
};
