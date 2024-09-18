@if (count($items))
    <div class="table-icon">
    @foreach ($items as $item)
        <div class="item">
            <div class="controls">
                <a href="javascript:" class="btn btn-default" data-modal="{!! $section !!}_edit" data-url="{!! route("admin.{$section}.edit", $item->id) !!}">
                    <i class="icon edit"></i>
                </a>
                <a href="javascript:" class="btn btn-default" data-id="{{ $item->id }}"><i class="icon delete"></i></a>
            </div>
            <img src="{{ asset($item->path) }}" alt="Image">
        </div>
    @endforeach
    </div>
@else
    {{ trans('admin.no_data') }}
@endif

@include("Admin.Layouts.partials.pagination")