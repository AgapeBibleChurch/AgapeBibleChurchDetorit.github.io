CKEDITOR.editorConfig = function( config ) {
	config.skin = 'bootstrapck';
	config.toolbarGroups = [
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
		{ name: 'styles', groups: [ 'styles', 'insert', 'links' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup', 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'tools', groups: [ 'tools', 'mode' ] }
	];

	config.removeButtons = 'Flash,Templates,Save,NewPage,Preview,Print,Find,Replace,Scayt,Form,HiddenField,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,Language,HorizontalRule,Smiley,PageBreak,Iframe,About,SelectAll,ShowBlocks';
};
