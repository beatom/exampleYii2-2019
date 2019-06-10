'use strict';

const dest = require('gulp-dest'),
    src = require('gulp-src'),
    gulp = require('gulp'),
    watch = require('gulp-watch'),
    series = require('gulp-series'),
    sass = require('gulp-sass'),
    browsersync = require('browser-sync'),
    autoprefixer = require('gulp-autoprefixer'),
    uglify = require('gulp-uglify'),
    pug = require('gulp-pug'),
    groupmediaqueries = require('gulp-group-css-media-queries'),
    mincss = require('gulp-clean-css'),
    rename = require('gulp-rename'),
    imagemin = require('gulp-imagemin'),
    imageminPngquant = require('imagemin-pngquant'),
    svgSprite = require('gulp-svg-sprite'),
    plumber = require('gulp-plumber'),
    debug = require('gulp-debug'),
    concat = require('gulp-concat'),
    clean = require('gulp-clean');

/* Path const */
const paths = {
  views: {
    src: [
      "./src/views/index.pug",
      "./src/views/pages/*.pug"
    ],
    dist: "./dist/",
    fonts: "./dist/fonts",
    watch: "./src/views/**/*.pug"
  },
  styles: {
    src: "./src/styles/main.scss",
    dist: "./dist/styles/",
    watch: "./src/styles/**/*.scss"
  },
  scripts: {
    src: "./src/js/main.js",
    src_js: "./src/js/",
    dist: "./dist/js/",
    watch: "./src/js/**/*.js"
  },
  images: {
    src: [
      "./src/img/**/*.{jpg,jpeg,png,gif,svg}",
      "./src/img/svg/*.svg",
      "!./src/img/favicon.{jpg,jpeg,png,gif}"
    ],
    dist: "./dist/img/",
    watch: "./src/img/**/*.{jpg,jpeg,png,gif,svg}"
  },
  sprites: {
    src: "./src/img/svg/*.svg",
    dist: "./dist/img/sprites/",
    watch: "./src/img/svg/*.svg"
  },
  frontend: {
    img: "../frontend/web/img",
    js: "../frontend/web/js",
    fonts: "../frontend/web/fonts",
    css: "../frontend/web/css"
  }
};
/* ==================== */

/* Auto browser reload */
gulp.task('server', function () {
  browsersync({
    server: {
      baseDir: './dist/',
      port: 4000
    },
    notify: true
  });
});
/* Auto browser reload end*/
gulp.task('watchCode', function () {
  gulp.watch(paths.views.watch, gulp.series('views'));
  gulp.watch(paths.styles.watch, gulp.series(['styles', 'cabinet-styles', 'registration-styles', 'copy-styles-animation']));
  gulp.watch(paths.scripts.watch, gulp.series('scripts'));
  gulp.watch(paths.images.watch, gulp.series('images'));
  gulp.watch(paths.sprites.watch, gulp.series('sprites'));
});


/* clear dist directory */
gulp.task('cleanFiles', function () {
  return gulp.src('./dist/**/', {read: false})
      .pipe(clean())
      .pipe(debug({title: 'Cleaning...'}))
});
/* clear dist directory end */

gulp.task('views', function () {
  return gulp.src(paths.views.src)
      .pipe(pug({pretty: true}))
      .pipe(gulp.dest(paths.views.dist))
      .pipe(browsersync.reload({stream: true}))
});

gulp.task('styles', function () {
  return gulp.src([
    paths.styles.src,
    './node_modules/animate.css/animate.css',
    './node_modules/wow.js/css/libs/animate.css'
  ])
      .pipe(plumber())
      .pipe(sass())
      .pipe(groupmediaqueries())
      .pipe(concat('main.css'))
      .pipe(plumber.stop())
      .pipe(autoprefixer({
        browsers: ['last 16 versions'],
        cascade: false
      }))
      .pipe(gulp.dest(paths.styles.dist))
      .pipe(debug({
        "title": "CSS files"
      }))
      .pipe(browsersync.stream());
});

gulp.task('cabinet-styles', function () {
  return gulp.src('./src/styles/cabinet.scss')
      .pipe(plumber())
      .pipe(sass())
      .pipe(groupmediaqueries())
      .pipe(concat('cabinet.css'))
      .pipe(plumber.stop())
      .pipe(autoprefixer({
        browsers: ['last 16 versions'],
        cascade: false
      }))
      .pipe(gulp.dest(paths.styles.dist))
      .pipe(debug({
        "title": "CSS files"
      }))
      .pipe(browsersync.stream());
});
gulp.task('registration-styles', function () {
  return gulp.src('./src/styles/registration.scss')
      .pipe(plumber())
      .pipe(sass())
      .pipe(groupmediaqueries())
      .pipe(concat('registration.css'))
      .pipe(plumber.stop())
      .pipe(autoprefixer({
        browsers: ['last 16 versions'],
        cascade: false
      }))
      .pipe(gulp.dest(paths.styles.dist))
      .pipe(debug({
        "title": "CSS files"
      }))
      .pipe(browsersync.stream());
});

gulp.task('scripts', function () {
  return gulp.src([paths.scripts.src])
      .pipe(concat('main.min.js'))
      .pipe(gulp.dest(paths.scripts.dist))
      .pipe(debug({
        "title": "JS files"
      }))
      .pipe(browsersync.stream());
});
gulp.task('copyScripts', function () {
  return gulp.src([paths.scripts.watch])
      // .pipe(concat('main.min.js'))
      .pipe(gulp.dest(paths.scripts.dist))
      .pipe(debug({
        "title": "JS files COPY"
      }))
      .pipe(browsersync.stream());
});
gulp.task('libs', function () {
  return gulp.src([
    // './node_modules/jquery/dist/jquery.min.js',
    './node_modules/odometer/odometer.js',
    './node_modules/slick-carousel/slick/slick.min.js',
    './node_modules/bootstrap/dist/js/bootstrap.min.js',
    './node_modules/wow.js/dist/wow.min.js',
    './node_modules/highcharts/highcharts.js'
  ])
      .pipe(concat('libs.min.js'))
      .pipe(gulp.dest(paths.scripts.dist))
      .pipe(debug({
        "title": "jQuery and bootstrap libs:"
      }));
});
gulp.task('cabinet-libs', function () {
  return gulp.src([
    // './node_modules/jquery/dist/jquery.min.js',
    './node_modules/nouislider/distribute/nouislider.min.js',
    // './node_modules/jquery/dist/jquery.slim.min.js',
    './node_modules/slick-carousel/slick/slick.min.js',
    './node_modules/bootstrap/dist/js/bootstrap.min.js',
    './node_modules/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js'
  ])
      .pipe(concat('cabinet-libs.min.js'))
      .pipe(gulp.dest(paths.scripts.dist))
      .pipe(debug({
        "title": "jQuery and bootstrap libs:"
      }));
});

gulp.task('images', function () {
  return gulp.src(paths.images.src)
      .pipe(imagemin([
        imageminPngquant({
          speed: 5,
          quality: 75
        }),
        imagemin.svgo({
          plugins: [
            {removeViewBox: false},
            {removeUnusedNS: false},
            {removeUselessStrokeAndFill: false},
            {cleanupIDs: false},
            {removeComments: true},
            {removeEmptyAttrs: true},
            {removeEmptyText: true},
            {collapseGroups: true}
          ]
        })
      ]))
      .pipe(gulp.dest(paths.images.dist))
      .pipe(debug({
        "title": "Images"
      }))
      .pipe(browsersync.reload({stream: true}))
});


gulp.task('sprites', function () {
  return gulp.src(paths.sprites.src)
      .pipe(svgSprite({
        mode: {
          stack: {
            sprite: "../sprite.svg"
          }
        }
      }))
      .pipe(gulp.dest(paths.sprites.dist))
      .pipe(debug({
        "title": "Sprites"
      }))
      .pipe(browsersync.reload({stream: true}))
});

gulp.task('fonts', function () {
  return gulp.src('./src/fonts/**/*')
      .pipe(gulp.dest(paths.views.fonts))
      .pipe(debug({
        "title": "Copy fonts:"
      }));
});




/* copy tasks */
gulp.task('copy-styles-animation', function() {
  return gulp.src('./src/styles/first-slide.css')
      .pipe(gulp.dest(paths.styles.dist))
});

gulp.task('copy-styles', function() {
  return gulp.src('./dist/styles/**')
      .pipe(gulp.dest(paths.frontend.css))
});
gulp.task('copy-images', function() {
  return gulp.src('./dist/img/**')
      .pipe(gulp.dest(paths.frontend.img))
});
gulp.task('copy-js', function() {
  return gulp.src('./dist/js/**')
      .pipe(gulp.dest(paths.frontend.js))
});
gulp.task('copy-fonts', function() {
  return gulp.src('./dist/fonts/**')
      .pipe(gulp.dest(paths.frontend.fonts))
});
gulp.task('copy-popper', function() {
  return gulp.src('./node_modules/popper.js/dist/popper.min.js')
      .pipe(gulp.dest([paths.scripts.dist, paths.scripts.src_js]))
});

gulp.task('copy-jquery', function() {
  return gulp.src('./node_modules/jquery/dist/jquery.min.js')
      .pipe(gulp.dest([paths.scripts.dist, paths.scripts.src_js]))
});

gulp.task('copy-isotope', function(done) {
  gulp.src('./node_modules/isotope-layout/dist/isotope.pkgd.js')
      .pipe(gulp.dest(paths.scripts.dist));
  done();
});
gulp.task('copy-files',
    gulp.series(['copy-styles', 'copy-images', 'copy-js', 'copy-fonts'])
);

gulp.task('watch',
    gulp.series([
          'views',
          'styles',
          'scripts',
          'images',
          'fonts',
          'sprites',
          'copyScripts',
          'libs',
          'cabinet-libs',
          'cabinet-styles',
          'registration-styles',
          'copy-jquery',
          'copy-isotope',
          'copy-files',
          'copy-styles-animation',
          'copy-popper'
        ],
        gulp.parallel('watchCode', 'server'))
);

gulp.task('build',
    gulp.series(['styles', 'scripts', 'images', 'sprites', 'libs'])
);
gulp.task('default', gulp.series('watch'));