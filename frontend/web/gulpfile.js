var gulp = require('gulp'),
	sass = require('gulp-sass'),
	concat = require('gulp-concat'),
	uglify = require('gulp-uglify'),
	cleanCSS = require('gulp-clean-css'),
	rename = require('gulp-rename'),
    browserSync = require('browser-sync'),
	sourcemaps = require('gulp-sourcemaps');


gulp.task('main-style', function () {
	return gulp.src([
		'css/site.css',
		'css/lib/customscroll.min.css',
		'css/lib/slick.css',
		'css/lib/animate.min.css',
		'css/lib/select2.css',
		'css/lib/nouislider.min.css',
		'css/lib/jquery.fullpage.min.css',
		'css/global.css',
		'css/reviews.css',
		'fonts/fonts.css',
		'css/chat.css',
		'css/banner.css',
        'css/progress-invest-dividends.css',
        'css/new-partners.css',
		'css/invest-today.css',
    'css/new-home-page.css'
	])
		.pipe(concat('main-style.css'))
		.pipe(cleanCSS())
		.pipe(gulp.dest('css'))
        .pipe(browserSync.reload({stream: true}));
});

gulp.task('cabinet-style', function () {
	return gulp.src([
		'css/main.min.css',
		'css/exchange-points.min.css',
        'css/lib/nouislider.min.css',
		'css/jquery-ui.min.css',
		'css/structure.min.css',
		'css/become-manager.min.css',
		'css/trade-account.min.css',
		'css/cabinet.css',
		'css/partnership.min.css',
		'css/managing-reviews.css',
		'css/verify.min.css',
        'css/lib/slick.css',
        'css/progress-invest-dividends.css',
		'css/new-partners.css',
		'css/banner.css'
	])
		.pipe(concat('cabinet-style.css'))
		.pipe(cleanCSS())
		.pipe(gulp.dest('css'));
});

gulp.task('login-style', function () {
	return gulp.src([
		'css/site.css',
		'css/main.min.css',
		'css/login.min.css'
	])
		.pipe(concat('login-style.css'))
		.pipe(cleanCSS())
		.pipe(gulp.dest('css'));
});

/* JavaScript */
gulp.task('cabinet-script', function () {
	return gulp.src([
        'js/cabinet/jquery-ui.min.js',
        'js/jquery.cookie.js',
		'js/cabinet/plugins.min.js',
        'js/lib/nouislider.min.js',
		'js/cabinet/main.js',
		//  'js/cabinet/main.min.js',
		'js/cabinet/messagesUnread.js',
		'js/cabinet/cabinet.js',
		'js/cabinet/open-modal.js',
		'js/partnership.js',
		'js/manager-reviews.js',
		'js/modal.js',
        'js/lib/slick.min.js',
		'js/new-partner.js'
	])
		.pipe(concat('cabinet-script.js'))
		.pipe(gulp.dest('js'));
});

gulp.task('main-script', function () {
	return gulp.src([
        'js/cabinet/jquery-ui.min.js',
		'js/script.js',
		'js/lib/svg4everybody.min.js',
		'js/lib/jquery.placeholder.min.js',
		'js/lib/smoothscroll.min.js',
		'js/lib/validate.min.js',
		'js/lib/slick.min.js',
		'js/lib/parallax.min.js',
		'js/lib/jquery.matchHeight-min.js',
		'js/lib/isotope.pkgd.min.js',
		'js/lib/odometer.min.js',
		'js/lib/jquery.vide.min.js',
		'js/lib/wow.min.js',
		'js/common/slider.js',
		'js/dev.js',
		'js/lib/select2.full.min.js',
		'js/lib/nouislider.min.js',
		'js/lib/wNumb.min.js',
		'js/common/select.js',
		'js/common/viewport.js',
		'js/lib/jquery.fullpage.min.js',
		'js/common/popup.js',
		'js/common/calculator.js',
		'js/lib/jquery.magnific-popup.min.js',
        'js/cabinet/open-modal.js',
        'js/lib/customscroll.min.js',
        'js/modal.js',
        'js/manager-reviews.js',
        'js/new-partner.js'
	])
		.pipe(concat('main-script.js'))
		.pipe(gulp.dest('js'));
});

gulp.task('watch', ['main-style', 'cabinet-style', 'login-style', 'cabinet-script', 'main-script'], function () {
	gulp.watch('css/**/**.css', ['main-style', 'cabinet-style', 'login-style']);
	gulp.watch('js/**/**.js', ['cabinet-script', 'main-script']);
});

gulp.task('default', ['watch']);