/**
* @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
* For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.stylesSet.add( 'my_styles', [

    // Align img left with photo style
    { name: "Photo-left", element: "img", styles: {
        float: "left",
        "margin-right": "10px",
        border: "1px outset #eee",
        background: "#fff",
        padding: "8px",
    } },

    // Align img right with photo style
    { name: "Photo-right", element: "img", styles: {
        float: "right",
        "margin-left": "10px",
        border: "1px outset #eee",
        background: "#fff",
        padding: "8px",
    } },

    // { name: 'Red Title',  element: 'h3', styles: { color: 'Red' } },
    //
    // // Inline styles.
    // { name: 'CSS Style', element: 'span', attributes: { 'class': 'my_style' } },
    // { name: 'Marker: Yellow', element: 'span', styles: { 'background-color': 'Yellow' } }
]);

CKEDITOR.editorConfig = function( config ) {

    // disable resizing of the editor
    config.resize_enabled = false;

    // The default plugins included in the basic setup define some buttons that
    // we don't want too have in a basic editor. We remove them here.
    // config.removeButtons = 'PasteText,PasteFromWord,Strike,Subscript,Superscript,addFile,Image,Maximize,HorizontalRule,Blockquote,Indent,Outdent,Spell,Styles,Scayt';
    // config.removeButtons = 'Cut,Copy,Paste,PasteText,PasteFromWord,Undo,Redo,Anchor,Underline,Strike,Subscript,Superscript,addFile,Image,Table,Styles,Format,Maximize,HorizontalRule,Unlink,Blockquote,Indent,Outdent,RemoveFormat,Source,Spell,Scayt';
    config.removePlugins = 'about,specialchar,wsc,scayt,spellchecker,elementspath,resize';

    // Let's have it basic on dialogs as well.
    config.removeDialogTabs = 'link:advanced;link:advanced;link:target';

    config.toolbar = [
		[ 'Source' ],
        [ 'Format', 'Styles' ],
		[ 'Bold', 'Italic', 'Underline' ],
		[ 'BulletedList', 'NumberedList' ],
		[ 'addImage' ],
        [ 'Table' ],
        [ 'HorizontalRule', 'Unlink', 'Blockquote', 'Indent', 'Outdent', 'RemoveFormat' ]
    ];

    // For inline style definition.
    config.stylesSet = 'my_styles';

    config.height = 500;

    // simpleuploads plugin
    config.extraPlugins='simpleuploads';
    config.simpleuploads_acceptedExtensions = "jpg|jpeg|png|gif";
    config.simpleuploads_maxFileSize = 1024*1024*5; // 5mb
    config.simpleuploads_maxNumImages=5; // this is a custom config of ours

    // this is the file that will handle image uploads
    config.filebrowserUploadUrl = '/admin/articles/upload';

    // set the location of our skin
    config.skin = 'moono,' + '/js/ckeditor/skins/moono/'
};
