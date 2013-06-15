var loadContent;

$(function() {
  window.chartColors = ['#3498DB', '#2ECC71', '#9B59B6', '#E74C3C', '#1ABC9C', '#F39C12', '#95A5A6'];
  $('#tabs a').click(function(e) {
    e.preventDefault();
    return $(this).tab('show');
  });
  return loadContent();
});

loadContent = function() {
  $('#loader').modal({
    show: true
  });
  return $.ajax({
    url: "/stats",
    success: function(data) {
      if (typeof data.commits_by_date === "undefined" && data.commits_by_date === null) {
        $("#loader h3").html("Shit...");
        $("#loader p").html("Something went wrong");
        return false;
      }
      $('#loader').modal('hide');
      renderCommitsByDateChart(data.commits_by_date);
      renderCommitsByHourChart(data.commits_by_hour);
      renderCommitsByDayChart(data.commits_by_day);
      $("a[href='#contributors']").trigger('click');
      renderCommitsByContributorsChart(data.commits_by_contributor);
      return $("a[href='#commits']").trigger('click');
    }
  });
};
