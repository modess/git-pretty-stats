renderCommitsByDateChart = (data) ->
    $("#commitsByDate").highcharts
      colors: window.chartColors
      chart:
        type: "areaspline"
        zoomType: "x"
      title:
        text: ""
      plotOptions:
        series:
          lineWidth: 1
          marker:
            enabled: false
      xAxis:
        categories: data.x
        tickInterval: parseInt(data.x.length / 20)
        labels:
            rotation: -45
            y: 35
      yAxis:
        title:
          text: ""
      series: [
        name: "Commits"
        data: data.y
      ]

renderCommitsByHourChart = (data) ->
    $("#commitsByHour").highcharts
      colors: window.chartColors
      chart:
        type: "bar"
      title:
        text: ""
      xAxis:
        categories: data.x
      yAxis:
        title:
          text: ""
      series: [
        name: "Commits"
        data: data.y
      ]

renderCommitsByDayChart = (data) ->
    $("#commitsByDay").highcharts
      colors: window.chartColors
      chart:
        type: "pie"
      title:
        text: ""
      yAxis:
        title:
          text: ""
      series: [
        name: "Commits"
        data: data
      ]
