@extends('Frontend.Layouts.modal')
@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon icon-fa fa-comments-o"></i> {!!trans('front.chat')!!}
@stop
@section('body')
    <div class="row no-padding">
        <div class="col-md-4">
            <div class="panel panel-default" id="chats_table" >
                <div class="panel-heading">
                    <div class="panel-form panel-form-right">
                        <div class="form-group search">
                            {!!Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('front.search'), 'autocomplete' => 'off', 'data-filter' => 'true'])!!}
                        </div>
                    </div>
                    <div class="panel-title"><i class="icon device"></i> {{trans('front.objects')}}</div>
                </div>
                <div data-table>
                    @include('Frontend.Chat.partials.table')
                </div>
            </div>
        </div>
        <div class="col-md-8" id="conversation">
            @include('Frontend.Chat.partials.conversation_empty')
        </div>
    </div>
@stop

@section('buttons')
@stop

<script type="text/javascript">
    $(document).ready(function () {
        tables.set_config('chats_table', {
            url: '{{ route('chat.searchParticipant') }}'
        });
    });
</script>