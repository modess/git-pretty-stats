@extends ('_layout')

@section('content')

<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="pull-left">{{ $name }} <small>on {{ $branch }}</small></h1>
                <div class="btn-group pull-right">
                    <button class="btn dropdown-toggle btn-info" data-toggle="dropdown">
                        Repositories <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        @foreach ($repositories as $repository)
                        <li>
                            <a href="{{ URL::to('repository', array($repository['name'])) }}">
                                <span class="pull-left">{{ $repository['name'] }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container" ng-controller="RepositoryController">

    <ul class="nav nav-tabs" id="tabs">
        <li class="active"><a href="#statistics" data-toggle="tab">Statistics</a></li>
        <li><a href="#commits" data-toggle="tab">Commit activity</a></li>
        <li><a href="#contributors" data-toggle="tab">Contributors</a></li>
    </ul>

    <div class="tab-content">
        <statistics></statistics>

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
            <ul class="thumbnails"></ul>
        </div>
    </div>

</div>

<div id="loader" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="loader" aria-hidden="true">
    <div class="modal-header">
        <h3>Hang in there</h3>
    </div>
    <div class="modal-body">
        <p><i class="icon-spinner icon-spin icon-large"></i> Loading repository, fetching commits and building graphs...</p>
    </div>
</div>

@stop
