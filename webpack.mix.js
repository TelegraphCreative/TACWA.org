let mix = require('laravel-mix')
require('laravel-mix-tailwind')


mix
	.setPublicPath('./web')

	// The App Build
	.js('resources/js/app.js', 'assets/js')
	.sass('resources/scss/app.scss', 'assets/css')
	.sass('resources/scss/styleguide.scss', 'assets/css')
	.copyDirectory('resources/placeholder', 'web/assets/placeholder')
	.copyDirectory('resources/img', 'web/assets/img')
	.copyDirectory('resources/js/static', 'web/assets/js/static')
	.sourceMaps()
	.browserSync({
		proxy: 'tacwa.test',
		files: [
			'./templates/**/*.twig',
			'./web/css/**/*.css',
			'./web/js/**/*.js',    ]
		})
	.tailwind()
	.version();