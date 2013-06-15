var renderCommitsByDateChart, renderCommitsByDayChart, renderCommitsByHourChart;

renderCommitsByDateChart = function(data) {
  return $("#commitsByDate").highcharts({
    colors: window.chartColors,
    chart: {
      type: "area",
      zoomType: "x"
    },
    title: {
      text: ""
    },
    plotOptions: {
      series: {
        lineWidth: 0,
        marker: {
          enabled: false
        }
      }
    },
    xAxis: {
      categories: data.x,
      tickInterval: 20,
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
        data: data.y
      }
    ]
  });
};

renderCommitsByHourChart = function(data) {
  return $("#commitsByHour").highcharts({
    colors: window.chartColors,
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
};

renderCommitsByDayChart = function(data) {
  return $("#commitsByDay").highcharts({
    colors: window.chartColors,
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
};
