{{-- 投稿一覧表示 --}}

<ul class="list-unstyled">
    @foreach ($microposts as $micropost)
        <li class="media mb-3">
            <img class="mr-2 rounded" src="{{ Gravatar::src($micropost->user->email, 50) }}" alt="">
            <div class="media-body">
                <div>
                    {!! link_to_route('users.show', $micropost->user->name, ['id' => $micropost->user->id]) !!} <span class="text-muted">posted at {{ $micropost->created_at }}</span>
                </div>
                <div>
                    <p class="mb-0">{!! nl2br(e($micropost->content)) !!}</p>
                </div>
                <div>
                    {{-- 削除ボタン --}}
                    @if (Auth::id() == $micropost->user_id)
                        {!! Form::open(['route' => ['microposts.destroy', $micropost->id], 'method' => 'delete']) !!}
                            {!! Form::submit('Delete', ['class' => 'btn btn-danger btn-sm']) !!}
                        {!! Form::close() !!}
                        
                    @endif
                </div>
            </div>
            
            <!-- 課題お気に入り追加　投稿に関してはログインユーザーと表示ユーザーの区別なくお気に入りボタンを表示-->
            @include('user_favorite.favorite_button', ['micropost' => $micropost])
            
        </li>
    @endforeach
</ul>
{{ $microposts->links('pagination::bootstrap-4') }}