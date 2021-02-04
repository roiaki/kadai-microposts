{{-- 課題お気に入りボタン追加 --}}
@if (Auth::user()->is_favoriting($micropost->id))
    {!! Form::open(['route' => ['user.unfavorite', $micropost->id], 'method' => 'delete']) !!}
        {!! Form::submit('Unfavorite', ['class' => "btn btn-danger btn-block"]) !!}
    {!! Form::close() !!}
@else
    {!! Form::open(['route' => ['user.favorite', $micropost->id]]) !!}
        {!! Form::submit('favorite', ['class' => "btn btn-primary btn-block"]) !!}
    {!! Form::close() !!}
@endif
