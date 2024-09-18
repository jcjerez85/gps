<script type="text/javascript">
        $(document).ready(function() {
                var lazy = JSON.parse('{!! json_encode(array_keys($blocks)) !!}');

                for (i in lazy) {
                    app.dashboard.loadBlockContent(lazy[i]);
                }

                app.dashboard.initEvents();
        });
</script>