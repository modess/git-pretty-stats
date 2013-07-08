$ ->
  window.chartColors = ['#3498DB', '#2ECC71', '#9B59B6', '#E74C3C', '#1ABC9C', '#F39C12', '#95A5A6']
  $('#tabs a').click(
    (e) ->
      e.preventDefault()
      $(this).tab 'show'
  )

angular.module("SharedServices", [])
.config(($httpProvider) ->
  $httpProvider.responseInterceptors.push "myHttpInterceptor"
  spinnerFunction = (data, headersGetter) ->
  $('#loader').modal { show: true }

  $httpProvider.defaults.transformRequest.push spinnerFunction
)
.factory "myHttpInterceptor", ($q, $window) ->
  (promise) ->
    promise.then ((response) ->
    $('#loader').modal 'hide'
    # response
    ), (response) ->
    $('#loader').modal 'hide'
    # $q.reject response

angular.module("main", ["SharedServices", "ngResource"])
.config ($interpolateProvider) ->
  $interpolateProvider.startSymbol('{[').endSymbol(']}')
.config ($routeProvider) ->
  $routeProvider
  .when '/index',
    controller: IndexCtrl
  .otherwise
    redirectTo: '/index'

IndexCtrl = ->
  $('#loader').modal { show: true }

  $.ajax
    url: $('#stats-endpoint').attr 'href'
    success: (data) ->
      if (typeof data.commits_by_date == "undefined" && data.commits_by_date == null)
        $("#loader h3").html "Shit..."
        $("#loader p").html "Something went wrong"
        return false

      $('#loader').modal 'hide'

      $("a[href='#commits']").trigger 'click'
      renderCommitsByDateChart data.commits_by_date
      renderCommitsByHourChart data.commits_by_hour
      renderCommitsByDayChart data.commits_by_day

      $("a[href='#contributors']").trigger 'click'
      renderCommitsByContributorsChart data.commits_by_contributor

      $("a[href='#statistics']").trigger 'click'
