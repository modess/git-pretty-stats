@extends ('_layout')

@section('content')
<div ng-controller="RepositoryController">
  <input type="hidden" name="name" value="{{ $name }}" />

  <div class="header">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h1 class="pull-left"><% repository.name %> <small>on <% repository.branch %></small></h1>
          <div class="btn-group pull-right">
            <button class="btn dropdown-toggle btn-info" data-toggle="dropdown">Repositories <span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu">
              <li ng-repeat="repository in repositories">
                <a href="{{ URL::to('/') }}/repository/<% repository.name %>"><span class="pull-left"><% repository.name %></span></a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <ul class="nav nav-tabs" id="tabs">
      <li class="active"><a href="#statistics" data-toggle="tab">Statistics</a></li>
      <li><a href="#commits" data-toggle="tab">Commit activity</a></li>
      <li><a href="#contributors" data-toggle="tab">Contributors</a></li>
    </ul>

  <div class="tab-content">
    <div id="statistics" class="tab-pane active">
      <statistics></statistics>
    </div>

    <div id="commits" class="tab-pane fade">
      <h3>By date <small>(zoomable)</small></h3>
      <div id="commitsByDate" style="height: 300px; width: 100%;"></div>

      <h3>By hour of day</h3>
      <div id="commitsByHour" style="height: 400px; width: 100%;"></div>

      <h3>By day of week</h3>
      <div id="commitsByDay" style="height: 300px; width: 100%;"></div>
    </div>

    <div id="contributors" class="tab-pane fade">
      <h3>Contributors <small>(zoomable)</small></h3>
      <div class="thumbnails"></div>
    </div>

  </div>

  <div class="modal fade" id="loader" tabindex="-1" role="dialog" aria-labelledby="myLoader" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h4 class="modal-title">Hang in there</h4></div>
        <div class="modal-body">
          <p><i class="fa fa-spinner fa-spin"></i> Loading repository, fetching commits and building graphs...</p>
        </div>
      </div>
    </div>
  </div>
</div>

@stop
