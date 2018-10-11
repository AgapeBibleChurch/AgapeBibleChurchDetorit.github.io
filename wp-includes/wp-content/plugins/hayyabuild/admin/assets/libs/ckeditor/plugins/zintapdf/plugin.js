﻿CKEDITOR.plugins.add('zintapdf',
{
    init: function (editor) {
        var pluginName = 'zintapdf';
        editor.ui.addButton('zintapdf',
            {
                label: 'ZintaPDF page break',
                command: 'zintapdf',
                icon: CKEDITOR.plugins.getPath('zintapdf') + 'zintapdf.gif'
            });
        var cmd = editor.addCommand('zintapdf', { exec: showMyDialog });
    }
});

function showMyDialog(e) {
	
	var oEditor = CKEDITOR.currentInstance;
	
    oEditor.insertHtml(' <p>[--pagebreak--]</p> ');
}
