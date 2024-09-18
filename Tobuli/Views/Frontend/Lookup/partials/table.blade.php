<div id="{{ $lookup->getTableId() }}Container">
    {!! $html->table() !!}
</div>

@section('scripts')
    <script>
        function {{ "{$lookup->getTableId()}SettingsModal" }}_modal_callback() {
            var $container = $('#{{ $lookup->getTableId() }}Container');

            $.ajax({
                url: '{{ $lookup->getRoute('table') }}',
                beforeSend: function() {
                    loader.add( $container );
                },
                success: function (response) {
                    $container.html(response);
                    initComponents($container);
                },
                complete: function() {
                    loader.remove( $container );
                }
            });
        }

        function dataTableExport(event, format) {
            event.preventDefault();

            var table = $(event.target).closest('.dataTable').DataTable();

            window.location = table.ajax.url() + "?" + $.param(table.ajax.params()) + '&action=' + format;
        }

        @if ($lookup->isAutoRefresh())
            setInterval( function () {
                $('#{{ $lookup->getTableId() }}').DataTable().ajax.reload( null, false ); // user paging is not reset on reload
            }, {{ $lookup->getRefreshMiliseconds() }} );
        @endif
    </script>

    {!! $html->scripts() !!}
@stop