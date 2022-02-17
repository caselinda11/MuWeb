/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// 在此处定义对默认配置的更改。 例如：
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	
	// 基本设置中包含的默认插件定义了一些
	// 在基本编辑器中不需要。它们在这里被移除。
	// config.removeButtons = 'Cut,Copy,Paste,Undo,Redo,Anchor,Underline,Strike,Subscript,Superscript';
		//取消过滤
	config.allowedContent = true;

	//引入css
	config.contentsCss = 'assets\/css\/bootstrap.min.css';
};
