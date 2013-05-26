$ ->
    window.primary_color = rgb2hex($("#primary-color").css('color'))
    loadContent()

rgb2hex = (rgb) ->
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/)
    return "#" + ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[3],10).toString(16)).slice(-2)

loadContent = ->
    $.get("/stats", (data) ->
        renderCommitsByDateChart data.commits_by_date
    )

