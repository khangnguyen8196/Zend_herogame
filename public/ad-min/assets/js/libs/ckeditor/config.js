/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.filebrowserBrowseUrl = '/ad-min/assets/js/libs//kcfinder/browse.php?opener=ckeditor&type=files';
	config.filebrowserImageBrowseUrl = '/ad-min/assets/js//libs/kcfinder/browse.php?opener=ckeditor&type=images';
	config.filebrowserUploadUrl = '/ad-min/assets/js/libs//kcfinder/upload.php?opener=ckeditor&type=files';
	config.filebrowserImageUploadUrl = '/ad-min/assets/js//libs/kcfinder/upload.php?opener=ckeditor&type=images';
	config.filebrowserFlashUploadUrl = '/ad-min/assets/js//libs/kcfinder/upload.php?opener=ckeditor&type=flash';
};
