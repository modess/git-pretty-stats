@extends ('_layout')

@section('content')

<div class="container">
  <div class="todo">
    <ul>
      @foreach ($repositories as $repository)
      <li>
        <a href="{{ URL::to('repository', array($repository['name'])) }}">
          <div class="todo-icon fui-list"></div>
          <div class="todo-content">
            <h4 class="todo-name">{{ $repository['name'] }} <small>@ {{ $repository['branch'] }}</small></h4>
            {{ $repository['commits'] }} commits
          </div>
        </a>
      </li>
      @endforeach
    </ul>
  </div>
</div>

@stop
