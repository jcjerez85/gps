@if ($messages->nextPageUrl())
<li data-next="{{ $messages->nextPageUrl() }}"></li>
@endif
@foreach($messages as $message)
<li class="message @if ($message->isMyMessage(auth()->user())) me @endif" id="message-{{$message->id}}">
    <span class="text">{{$message->content}}</span>
    <span class="author" title="{{ $message->created_at }}">{{$message->senderName}}</span>
</li>
@endforeach