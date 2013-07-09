renderStatistics = (data) ->
  source   = $("#statistics-template").html()
  template = Handlebars.compile(source)
  for statistic in data
    $("#statistics .thumbnails").append template(statistic)
