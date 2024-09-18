{!! Form::hidden('id', $item->id ?? null) !!}

<div class="form-group">
    {!! Form::label('title', trans('validation.attributes.title') . ':') !!}
    {!! Form::text('title', $item->title ?? null, ['class' => 'form-control']) !!}
</div>

@if (Auth::User()->isAdmin())
    <div class="form-group">
        {!! Form::label('user_id', trans('validation.attributes.user') . ':') !!}
        {!! Form::select('user_id', $users, $item->user_id ?? null, ['class' => 'form-control']) !!}
    </div>
@endif