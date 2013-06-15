$ ->
    window.chartColors = ['#3498DB', '#2ECC71', '#9B59B6', '#E74C3C', '#1ABC9C', '#F39C12', '#95A5A6']
    $('#tabs a').click(
        (e) ->
            e.preventDefault()
            $(this).tab 'show'
    )
    loadContent()

loadContent = ->
    $('#loader').modal { show: true }

    $.get("/stats", (data) ->
        $('#loader').modal 'hide'

        renderCommitsByDateChart data.commits_by_date
        renderCommitsByHourChart data.commits_by_hour
        renderCommitsByDayChart data.commits_by_day
        $("a[href='#contributors']").trigger 'click'
        renderCommitsByContributorsChart data.commits_by_contributor
        $("a[href='#commits']").trigger 'click'
    )

