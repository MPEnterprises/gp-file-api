var gulp = require('gulp');
var concat = require('gulp-concat');
var less = require('gulp-less');
var path = require('path');

gulp.task('default', ['scripts', 'styles']);

gulp.task('scripts', function() {
    return gulp.src([
        'components/dropzone/dist/dropzone.js',
        'resources/assets/js/plugin-upload.js',
    ])
        .pipe(concat('upload.js'))
        .pipe(gulp.dest('resources/built'));
});

gulp.task('styles', function () {
    return gulp.src([
        'resources/assets/less/app.less',
    ])
        .pipe(less({
            paths: [ path.join(__dirname, 'less', 'includes') ]
        }))
        .pipe(gulp.dest('resources/built'));
});
