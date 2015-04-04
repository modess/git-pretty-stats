'use strict';

var gulp = require('gulp'),
  sass = require('gulp-ruby-sass'),
  concat = require('gulp-concat'),
  autoprefixer = require('gulp-autoprefixer'),
  mainBowerFiles = require('main-bower-files'),
  gulpFilter = require('gulp-filter'),
  merge = require('merge-stream'),
  watch = require('gulp-watch'),
  gulpif = require('gulp-if'),
  clean = require('gulp-clean'),
  livereload = require('gulp-livereload'),
  templateCache = require('gulp-angular-templatecache'),
  jshint = require('gulp-jshint'),
  jshintstylish = require('jshint-stylish'),
  angularFilesort = require('gulp-angular-filesort');

gulp.task('styles', function() {
  return sass('app/assets/styles/', { sourcemap: true })
    .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'))
    .pipe(concat('main.css'))
    .pipe(gulp.dest('public/styles'));
});

gulp.task('scripts', function() {
  return gulp.src('app/assets/scripts/**/*.js')
    .pipe(angularFilesort())
    .pipe(concat('app.js'))
    .pipe(gulp.dest('public/scripts'));
});

gulp.task('jshint', function() {
  return gulp.src('./app/assets/scripts/**/*.js')
    .pipe(jshint('.jshintrc'))
    .pipe(jshint.reporter(jshintstylish));
});

gulp.task('clean', function () {
  return gulp.src(['public/**/*', '!public/index.php', '!public/.htaccess'], {read: false})
    .pipe(clean());
});

gulp.task('angular-templates', function () {
  gulp.src(['app/assets/views/**/*.html', 'app/assets/scripts/templates/**/*.html'])
    .pipe(templateCache({
      module: 'gitPrettyStats.templates',
      standalone: true
    }))
    .pipe(gulp.dest('public/scripts'));
});

gulp.task('vendor', function() {
  var styles = gulp.src(mainBowerFiles())
    .pipe(gulpFilter('*.css'))
    .pipe(concat('vendor.css'))
    .pipe(gulp.dest('public/styles'));

  var scripts = gulp.src(mainBowerFiles())
    .pipe(gulpFilter('*.js'))
    .pipe(concat('vendor.js'))
    .pipe(gulp.dest('public/scripts'));

  return merge(styles, scripts);
});

gulp.task('watch', ['default'], function () {
  gulp.watch('app/assets/styles/**/*.scss', ['styles']);
  gulp.watch('app/assets/scripts/**/*.js', ['scripts', 'jshint']);
  gulp.watch('app/assets/views/**/*.html', ['angular-templates']);
  gulp.watch('public/**/*').on('change', livereload.changed);
});

gulp.task('default', ['clean'], function () {
  gulp.start(['styles', 'scripts', 'vendor', 'angular-templates']);
});
