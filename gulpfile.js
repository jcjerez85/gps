const gulp = require('gulp'),
    es = require('event-stream'),
    sass = require('gulp-sass')(require('node-sass')),
    sourcemaps = require('gulp-sourcemaps'),
    cleanCSS = require('gulp-clean-css'),
    browserSync = require('browser-sync').create(),
    concat = require('gulp-concat'),
    group = require('gulp-group-files'),
    minify = require('gulp-minify'),
    purify = require('gulp-purifycss');

var scripts = {
    'report': [
        'resources/assets/js/lib/jquery-3.2.1.min.js',
        'resources/assets/js/lib/moment.js',
        'resources/assets/js/lib/moment-timezone.js',
        'resources/assets/js/lib/flot/jquery.flot.js',
        'resources/assets/js/lib/flot/jquery.flot.canvas.js',
        'resources/assets/js/lib/flot/jquery.flot.crosshair.js',
        'resources/assets/js/lib/flot/jquery.flot.navigate.js',
        'resources/assets/js/lib/flot/jquery.flot.resize.js',
        'resources/assets/js/lib/flot/jquery.flot.selection.js',
        'resources/assets/js/lib/flot/jquery.flot.time.js',

        'resources/assets/js/lib/leaflet/leaflet.1.0.3.js',
        'resources/assets/js/lib/leaflet/leaflet.polylineDecorator.js',
    ],
    'core': [
        'resources/assets/js/lib/jquery-3.2.1.min.js',
        'resources/assets/js/lib/jquery-ui.js',
        'resources/assets/js/lib/jquery.ui.touch-punch.min.js',
        'resources/assets/js/lib/jquery.autocomplete.js',
        'resources/assets/js/lib/bootstrap.min.js',
        'resources/assets/js/lib/bootstrap-select.js',
        'resources/assets/js/lib/bootstrap-select-ajax.min.js',
        'resources/assets/js/lib/bootstrap-datepicker.min.js',
        'resources/assets/js/lib/datepicker-locales/*',
        'resources/assets/js/lib/bootstrap-datetimepicker.js',
        'resources/assets/js/lib/datetimepicker-locales/*',
        'resources/assets/js/lib/bootstrap-colorpicker.min.js',
        'resources/assets/js/lib/bootstrap-modal.js',
        'resources/assets/js/lib/bootstrap-modalmanager.js',
        'resources/assets/js/lib/bootstrap-toastr.js',
        'resources/assets/js/lib/jquery.ba-throttle-debounce.js',
        'resources/assets/js/lib/drag-select.min.js',
        'resources/assets/js/lib/jquery.dataTables.min.js',
        'resources/assets/js/lib/jquery.intl-tel-input.js',

        'resources/assets/js/lib/flot/jquery.flot.js',
        'resources/assets/js/lib/flot/jquery.flot.canvas.js',
        'resources/assets/js/lib/flot/jquery.flot.crosshair.js',
        'resources/assets/js/lib/flot/jquery.flot.navigate.js',
        'resources/assets/js/lib/flot/jquery.flot.resize.js',
        'resources/assets/js/lib/flot/jquery.flot.selection.js',
        'resources/assets/js/lib/flot/jquery.flot.time.js',
        'resources/assets/js/lib/flot/jquery.flot.pie.js',
        'resources/assets/js/lib/flot/jquery.flot.orderBars.js',
        'resources/assets/js/lib/flot/jquery.flot.tooltip.js',

        'resources/assets/js/helpers/helper.js',
        'resources/assets/js/helpers/fileread.js',

        'resources/assets/js/plugins/outer-html.js',
        'resources/assets/js/plugins/jquery.databox.js',
        'resources/assets/js/plugins/loader.js',
        'resources/assets/js/plugins/modals.js',
        'resources/assets/js/plugins/tables.js',
        'resources/assets/js/plugins/multi-checkbox.js',
        'resources/assets/js/plugins/jquery.element-disabler.js',
        'resources/assets/js/plugins/checklists.js',
        'resources/assets/js/plugins/actions.js',
        'resources/assets/js/plugins/dragger.js',
    ],
    'app':[
        'resources/assets/js/lib/moment.js',
        'resources/assets/js/lib/moment-timezone.js',
        'resources/assets/js/lib/es6-promise.min.js',

        'resources/assets/js/lib/leaflet/leaflet.1.0.3.js',
        'resources/assets/js/lib/leaflet/leaflet.polylineDecorator.js',
        'resources/assets/js/lib/leaflet/leaflet.markerCluster.js',
        'resources/assets/js/lib/leaflet/leaflet.draw.js',
        'resources/assets/js/lib/leaflet/leaflet.editable.js',
        'resources/assets/js/lib/leaflet/leaflet.ruler.js',
        'resources/assets/js/lib/leaflet/marker.rotate.js',
        'resources/assets/js/lib/leaflet/Leaflet.Marker.SlideTo.js',
        'resources/assets/js/lib/leaflet/leaflet.bing.min.js',
        'resources/assets/js/lib/leaflet/Leaflet.GoogleMutant.js',
        'resources/assets/js/lib/leaflet/Yandex.js',
        'resources/assets/js/lib/leaflet/leaflet.circle.topolygon-min.js',

        'resources/assets/js/controller/listview.js',
        'resources/assets/js/controller/historyGraph.js',
        'resources/assets/js/controller/historyPlayer.js',
        'resources/assets/js/controller/history.js',
        'resources/assets/js/controller/devices.js',
        'resources/assets/js/controller/pois.js',
        'resources/assets/js/controller/geofences.js',
        'resources/assets/js/controller/routes.js',
        'resources/assets/js/controller/alerts.js',
        'resources/assets/js/controller/events.js',
        'resources/assets/js/controller/sensors.js',
        'resources/assets/js/controller/app.js',
        'resources/assets/js/controller/notifications.js',
        'resources/assets/js/controller/commands.js',
        'resources/assets/js/controller/deviceMedia.js',

        'resources/assets/js/model/device.js',
        'resources/assets/js/model/alert.js',
        'resources/assets/js/model/poi.js',
        'resources/assets/js/model/geofence.js',
        'resources/assets/js/model/route.js',
        'resources/assets/js/model/event.js',
        'resources/assets/js/model/MapTiles.js',
        'resources/assets/js/lib/socket.io.js',

        'resources/assets/js/plugins/chat.js',
        'resources/assets/js/controller/dashboard.js'
    ]
};

let scriptTasks = [];
Object.keys(scripts).forEach(dst => {
    let taskName = 'script-' + dst;
    scriptTasks.push(taskName);

    gulp.task(taskName, function() {
        return gulp.src(scripts[dst])
            .pipe(concat(dst + ".js"))
            .pipe(gulp.dest("public/assets/js/"))
            .pipe(browserSync.reload({
                stream: true
            }))
    });
});
gulp.task('scripts', gulp.series(scriptTasks));

gulp.task('sass', function(){
    //return gulp.src('resources/assets/scss/app.scss')
    return gulp.src('resources/assets/scss/templates/facelift.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('public/assets/css'))
        .pipe(browserSync.reload({
            stream: true
        }))
});

gulp.task('sass-all', function(){
    return gulp.src('resources/assets/scss/templates/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('public/assets/css'))
});

gulp.task('minify-css', function() {
    return gulp.src('public/assets/css/*.css')
        .pipe(cleanCSS({debug: true}, function(details) {
            console.log(details.name + ': ' + details.stats.originalSize);
            console.log(details.name + ': ' + details.stats.minifiedSize);
        }))
        .pipe(gulp.dest('public/assets/css'));
});

gulp.task('purify', function() {
    return gulp.src('public/assets/css/*.css')
        .pipe(purify(['public/assets/**/*.js', 'Tobuli/Views/**/*.blade.php']))
        .pipe(gulp.dest('public/assets/css'));
});

gulp.task('minify-js', function() {
    return gulp.src('public/assets/js/*.js')
        .pipe(minify({
            ext:{
                src:'-debug.js',
                min:'.js'
            },
            exclude: ['tasks'],
            ignoreFiles: ['.min.js']
        }))
        .pipe(gulp.dest('public/assets/js'));
});

gulp.task('watch', function() {
    browserSync.init({
        //proxy: "http://46.101.121.251"
        // proxy: "dev-cs.gpswox.com"
        proxy: "localhost"
    });

    gulp.watch('resources/assets/scss/**/*.scss', gulp.series('sass'));
    gulp.watch('resources/assets/js/**/*.js', gulp.series(['scripts']));
});

gulp.task('default', gulp.series('sass', 'scripts', 'watch'));
gulp.task('templates', gulp.series('sass-all', 'minify-css'));
gulp.task('assets', gulp.series('sass-all', 'minify-css', 'scripts', 'minify-js'));