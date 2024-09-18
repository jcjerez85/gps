var loader = {
    template: '<div class="loading"><div class="backdrop"></div><div class="outter"><div class="middle"><div class="inner"><i class="loader"></i></div></div></div></div>',
    add: function($container) {
        $container = $( $container );

        dd( 'loader.add', $container );

        if ( $('.loading', $container).length )
            return;

        let $loader = $(loader.template);
        $container.css('position', 'relative');
        $container.append($loader);

        return $loader;
    },
    remove: function($container, $loader) {
        $container = $( $container );
        $loader = $loader || $('.loading', $container);
        dd( 'loader.remove', $container );
        $loader.remove();
    }
};