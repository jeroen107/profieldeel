var gulp = require('gulp');
var minify = require('gulp-minify');
var watch = require('gulp-watch');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var addsrc = require('gulp-add-src');

//samenvoegen van de JS
gulp.task('js', function() {
  return gulp.src('src/js/*.js')
    .pipe(concat('app.js'))
    .pipe(minify({ext:{min:'-min.js'}
    }))
    .pipe(gulp.dest('dist'));
  
});

gulp.task('sass', function () {
  return gulp.src('src/sass/custom.sass')
    .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
    .pipe(gulp.dest('dist'));
});

gulp.task('watch', function() {
   gulp.watch('src/sass/*.sass', ['sass','js']);
});

gulp.task('default', ['sass','js']);
