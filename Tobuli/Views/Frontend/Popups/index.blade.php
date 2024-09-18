@if (Auth::check())
    @foreach(Auth::user()->filteredUnreadNotifications(['!data.position' => 'top']) as $notification)
        <div class="modal-popup fade {!! $notification->data['position'] !!}" id="popup{{$notification->id}}" tabindex="-1" role="dialog" data-modaloverflow="true" data-backdrop="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    @if( ! empty($notification->data['title']) )
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <div class="modal-title">{!! $notification->data['title'] !!}</div>
                    </div>
                    @else
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    @endif

                    <div class="modal-body">
                        {!! $notification->data['content'] !!}
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(window).on("load", function() {
                setTimeout(function(){
                    $('#popup{{$notification->id}}').modal('show');
                }, 5000);
            });
        </script>
    @endforeach
@endif
