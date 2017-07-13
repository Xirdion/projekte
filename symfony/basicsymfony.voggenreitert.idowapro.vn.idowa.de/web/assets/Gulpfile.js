'use strict';

var gulp    = require('gulp');
var sass    = require('gulp-sass');
var srcmaps = require('gulp-sourcemaps');
var uglify  = require('gulp-uglify');
var rename  = require('gulp-rename');

gulp.task('sass', function() {
    return gulp.src('./src/scss/main.scss')
        .pipe(srcmaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(srcmaps.write('.'))
        .pipe(gulp.dest('./dist/css'));
});

gulp.task('sass:watch', function() {
    gulp.watch('./src/scss/**/*.scss', ['sass']);
});

gulp.task('uglify', function() {
    return gulp.src('./src/js/*.js')
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./dist/js'));
});

gulp.task('uglify:watch', function () {
    gulp.watch('./src/js/*.js', ['uglify']);
});

gulp.task('default', [
    'sass:watch',
    'uglify:watch'
]);