<div class="nav-pagination">
    @if(!empty($limitChoice))
        <div class="pull-left">
            <div class="btn-group bootstrap-select form-control">
                <select class="form-control" name="limit" data-filter>
                    @foreach($limitOptions ?? [10, 25, 50, 100, 500, 1000] as $option)
                        <option value="{{ $option }}" @if($items->perPage() == $option) selected @endif>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif

    {!! $items->render() !!}
</div>