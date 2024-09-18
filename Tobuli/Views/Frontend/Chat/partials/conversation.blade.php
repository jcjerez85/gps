<div class="conversation" data-chatId="{{$chat->id}}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <a class="close" onclick="app.chat.close(this)">&times;</a>
            <div class="panel-title" title="{{$chat->title}}"><i class="icon icon user"></i> {{$chat->title}}</div>
        </div>

        <ul class="messages">
            @include('Frontend.Chat.partials.message')
        </ul>

        <div class="panel-footer">
            {!!Form::open(['route' => ['chat.message', $chat->id], 'method' => 'POST', 'onsubmit' => 'return app.chat.send(this);'])!!}
            <div class="input-group">
                {!!Form::text('message', null, ['class' => 'form-control ', 'autocomplete' => 'off', 'onkeydown' => 'app.chat.keyDown(event,this);'])!!}
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit">{{trans('front.send')}}</button>
                </span>
            </div>
            {!!Form::close()!!}
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var $container = $(".conversation[data-chatId='{{ $chat->id }}'] .messages");

        $container.scroll(function () {
            var $next = $('[data-next]', $container);

            if ($next.length && $container.scrollTop() < 1) {
                app.chat.getMessages($next.attr('data-next'), $container);

                $next.remove();
            }
        });

        setTimeout(function() {
            app.chat.getMessagesLatest("{{route('chat.messages', $chat->id)}}", $container, {{ time() }})
        }, app.checkChatFrequency * 1000);

    });
</script>