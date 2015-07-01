var gulp = require('gulp');
var concat = require('gulp-concat');

gulp.task('default', ['scripts']);

gulp.task('scripts', function() {
    return gulp.src([
        'components/dropzone/dist/dropzone.js',
        'resources/assets/js/plugin-upload.js',
    ])
        .pipe(concat('upload.js'))
        .pipe(gulp.dest('resources/built'));
});
