renderCommitsByContributorsChart = (data) ->
    i = 0
    source   = $("#contributor-template").html()
    template = Handlebars.compile(source)
    for contributor in data
        contributor.i = i
        $("#contributors .thumbnails").append template(contributor)

        $("#chart-" + i).highcharts
          colors: window.chartColors
          chart:
            type: "area"
            zoomType: "x"
          title:
            text: ""
          plotOptions:
            series:
              lineWidth: 0
              marker:
                enabled: false
          xAxis:
            categories: contributor.data.x
            tickInterval: 30
            labels:
                rotation: -45
                y: 35
          yAxis:
            title:
              text: ""
          series: [
            name: "Commits"
            data: contributor.data.y
          ]
        i++
