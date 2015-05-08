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
  del = require('del'),
  livereload = require('gulp-livereload'),
  templateCache = require('gulp-angular-templatecache'),
  jshint = require('gulp-jshint'),
  jshintstylish = require('jshint-stylish'),
  angularFilesort = require('gulp-angular-filesort');

var assetsFolder          = './app/assets/';
var assetsVendorFolder    = assetsFolder + 'bower_components/';
var assetsJsFolder        = assetsFolder + 'scripts/';
var assetsSassFolder      = assetsFolder + 'styles/';
var assetsViewsFolder     = assetsFolder + 'views/';
var assetsFontsFolder     = assetsFolder + 'fonts/';
var assetsTemplatesFolder = assetsJsFolder + 'templates/';

var distFolder      = './public/';
var distJsFolder    = distFolder + 'scripts/';
var distCssFolder   = distFolder + 'styles/';
var distFontsFolder = distFolder + 'fonts/';

gulp.task('styles', function() {
  return sass(assetsSassFolder, { sourcemap: true })
    .pipe(autoprefixer('last 2 version', 'safari 5', 'ie 8', 'ie 9', 'opera 12.1'))
    .pipe(concat('main.css'))
    .pipe(gulp.dest('public/styles'));
});

gulp.task('scripts', function() {
  return gulp.src(assetsJsFolder + '**/*.js')
    .pipe(angularFilesort())
    .pipe(concat('app.js'))
    .pipe(gulp.dest('public/scripts'));
});

gulp.task('fonts', function () {
  var fontAwesome = gulp.src(assetsVendorFolder + 'font-awesome/fonts/*');
  var openSans    = gulp.src(assetsFontsFolder + '/*');

  return merge(fontAwesome, openSans)
    .pipe(gulp.dest(distFontsFolder));
});

gulp.task('jshint', function() {
  return gulp.src(assetsJsFolder + '**/*.js')
    .pipe(jshint('.jshintrc'))
    .pipe(jshint.reporter(jshintstylish));
});

gulp.task('clean', function (cb) {
  del([
    distFolder + '**/*',
    '!' + distFolder + 'index.php',
    '!' + distFolder + '.htaccess'
  ], cb);
});

gulp.task('angular-templates', function () {
  return gulp.src([assetsViewsFolder + '/**/*.html', assetsTemplatesFolder + '**/*.html'])
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
    .pipe(gulp.dest(distCssFolder));

  var scripts = gulp.src(mainBowerFiles())
    .pipe(gulpFilter('*.js'))
    .pipe(concat('vendor.js'))
    .pipe(gulp.dest(distJsFolder));

  return merge(styles, scripts);
});

gulp.task('watch', ['default'], function () {
  livereload.listen();

  gulp.watch(assetsSassFolder + '**/*.scss', ['styles']);
  gulp.watch(assetsJsFolder + '**/*.js', ['scripts', 'jshint']);
  gulp.watch([assetsViewsFolder + '**/*.html', assetsTemplatesFolder + '**/*.html'], ['angular-templates']);
  gulp.watch(distFolder + '**/*').on('change', livereload.changed);
});

gulp.task('default', ['clean'], function () {
  gulp.start(['styles', 'scripts', 'vendor', 'fonts', 'angular-templates']);
});
