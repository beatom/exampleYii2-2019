# Документация проекта invest

## Особенности
* автоматическая перезагрузка страницы в браузере с использованием Browsersync;
* использование препроцессора Pug;
* использование препроцессора SCSS;


## Запуск сборки
Используется gulp 4, для его работы нам нужен node 10 версии.

* скачайте node 10, установите/обновите;
* введите команду, которая скачает необходимые компоненты для корректной работы нашей сборки, указанные в файле ```package.json```: ```npm install```;
* введите команду: ```gulp watch``` (режим сборки, отслеживания изменений и автоматическое обновление страницы).

Если вы всё сделали правильно, у вас должен открыться браузер с локальным сервером и работающим ```browser-sync```.

## Плагины

* [browser-sync](https://browsersync.io/docs/gulp) - живая перезагрузка веб-страницы при внесении изменений в файлы вашего проекта;
* [gulp-autoprefixer](https://www.npmjs.com/package/gulp-autoprefixer) — автоматически расставляет вендорные префиксы в CSS в соответствии с сервисом [Can I Use](https://caniuse.com/);
* [gulp-uglify](https://www.npmjs.com/package/gulp-uglify) — минификация JS-файлов;
* [gulp-pug](https://www.npmjs.com/package/gulp-pug) — компиляция Pug в HTML;
* [gulp-sass](https://www.npmjs.com/package/gulp-sass) — компиляция SCSS в CSS;
* [gulp-group-css-media-queries](https://www.npmjs.com/package/gulp-group-css-media-queries) - группировка ```@media```;
* [gulp-clean-css](https://www.npmjs.com/package/gulp-clean-css) — минификация CSS-файлов;
* [gulp-rename](https://www.npmjs.com/package/gulp-rename) — переименование файлов, добавление суффиксов и префиксов (например, добавление суффикса ```.min``` к минифицированным файлам);
* [gulp-imagemin](https://www.npmjs.com/package/gulp-imagemin) — сжатие изображений PNG, JPG, GIF и SVG (включая дополнительные плагины для оптимизации);
* [gulp-svg-sprite](https://www.npmjs.com/package/gulp-svg-sprite) — создание SVG-спрайтов;
* [gulp-plumber](https://www.npmjs.com/package/gulp-plumber) — оповещения в командной строке (например, ошибки в SCSS/Sass);
* [gulp-debug](https://www.npmjs.com/package/gulp-debug) — отладка в терминале;
* [gulp-watch](https://www.npmjs.com/package/gulp-watch) — отслеживание изменений в ваших файлах проекта;
* [gulp-clean](https://www.npmjs.com/package/gulp-clean) — удаление файлов и папок;

## Файловая структура

### Исходники
* Pug-файлы находятся в папке ```src/views```
	* шаблон: ```src/views/extends/layout.pug```
	* компоненты (кнопки, инпуты, чекбоксы и т.д.): ```src/views/components```
	* инклюды (шапка, футер, etc): ```src/views/includes```
	* страницы: ```src/views/pages```
	* секции: ```src/views/sections```
	* миксины: ```src/views/utils```
* SCSS-файлы находятся в папке ```src/styles```
	* стили компонентов (кнопки, инпуты, чекбоксы и т.д.): ```src/styles/components```
	* инклюды (шапка, футер, etc): ```src/styles/includes```
	* стили секций: ```src/styles/sections```
	* переменные, миксины, наследуемые свойства, стили шрифтов: ```src/styles/utils```
	* сторонние библиотеки (от других разработчиков): ```src/styles/_libs.scss```
* JS-файлы находятся в папке ```src/js```
* Изображения находятся в папке ```src/img```
	* векторные изображения для создания спрайтов: ```src/img/svg```
	* единичное изображение для генерации фавиконок находится в ```src/img/favicon.png``` (данное изображение может иметь формат ```.jpg```, ```.png``` или ```.gif``` и размер не менее чем ```100px x 100px```)

### Готовые файлы
* скомпилированные HTML-файлы находятся в папке ```dist/```;
* минифицированные CSS-файлы находятся в папке ```dist/styles```;
* минифицированные JS-файлы с поддержкой ES6 находятся в папке ```dist/js```;
* сжатые изображения находятся в папке ```dist/img```.

## Сборка проекта
```gulp watch```