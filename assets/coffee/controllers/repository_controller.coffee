RepositoryCtrl = ->
  $('#loader').modal { show: true }

  $.ajax
    url: $('#stats-endpoint').attr 'href'
    success: (data) ->
      if (typeof data.charts.date == "undefined" && data.charts.date == null)
        $("#loader h3").html "Shit..."
        $("#loader p").html "Something went wrong"
        return false

      $('#loader').modal 'hide'

      renderStatistics data.statistics

      $("a[href='#commits']").trigger 'click'
      renderCommitsByDateChart data.charts.date
      renderCommitsByHourChart data.charts.hour
      renderCommitsByDayChart data.charts.day

      $("a[href='#contributors']").trigger 'click'
      renderCommitsByContributorsChart data.charts.contributor

      $("a[href='#statistics']").trigger 'click'
