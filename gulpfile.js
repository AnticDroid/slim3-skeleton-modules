var gulp = require('gulp'),
    less = require('gulp-less'),
    minifyCss = require('gulp-minify-css'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    coffee = require('gulp-coffee'),
    sourcemaps = require('gulp-sourcemaps'),
    watch = require('gulp-watch');

// task for compiling less, combining css, and minifying
gulp.task('css', function () {
    // complile frontend less
    gulp.src('assets/less/portal.less')
        .pipe(sourcemaps.init())  // Process the original sources
        .pipe(less())
        .pipe(sourcemaps.write()) // Add the map to modified source.
        .pipe(gulp.dest('public/css'));

    // complile admin less
    gulp.src('assets/less/admin.less')
        .pipe(sourcemaps.init())  // Process the original sources
        .pipe(less())
        .pipe(sourcemaps.write()) // Add the map to modified source.
        .pipe(gulp.dest('public/css'));

    // // combine all files
    // return gulp.src([
    //         'public/css/app.css'
    //     ])
    //     .pipe(concat('app.css'))
    //     // //only minify if gulp is ran with '--type production'
    //     // .pipe(gutil.env.type === 'production' ? minifyCss() : gutil.noop())
    //     .pipe(gulp.dest('public/css'));

});

gulp.task('js', function() {
    // prepare the coffeescript files
    gulp.src('assets/coffee/*.coffee')
        .pipe(sourcemaps.init())
        .pipe(coffee())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./public/js'));

    // combine all files
    return gulp.src([
            'public/js/app.js'
        ])
        .pipe(concat('app.js'))
        // //only uglify if gulp is ran with '--type production'
        // .pipe(gutil.env.type === 'production' ? uglify() : gutil.noop())
        .pipe(gulp.dest('public/js'));
});

gulp.task('default', function() {
    gulp.start('css');
    gulp.start('js');
});

gulp.task('watch', function () {
    watch(['assets/less/**/*.less', 'assets/coffee/**/*.js'], function (event) {
        gulp.start('css');
    });
});
