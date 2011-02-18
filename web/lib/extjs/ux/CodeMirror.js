Ext.namespace('Ext.ux.form');

Ext.ux.form.CodeMirror = Ext.extend(Ext.form.TextArea, {
	language: 'txt',
	codeMirrorPath: 'lib/codemirror', // should be path to code mirror on your server!
	initComponent: function() {
		if (this.codeMirrorPath === null) {
			throw 'Ext.ux.form.CodeMirror: codeMirrorPath required';
		}
		this.initialized = false;
		Ext.ux.form.CodeMirror.superclass.initComponent.apply(this, arguments);
		this.addEvents('initialize');
		this.on({
			resize: function(ta, width, height) {
				var el = Ext.select('.'+this.id, true);
				if (el) {
					width -= 35;
					el.elements.forEach(function(e) {
						e.setSize(width, height);
					});
				}
			},
			afterrender: function() {
				var parser, stylesheet;
				switch (this.language.toLowerCase()) {
					case 'css':
						parser = 'parsecss.js';
						stylesheet = this.codeMirrorPath+'/css/csscolors.css';
						break;
					case 'js':
						parser = ['tokenizejavascript.js', 'parsejavascript.js'];
						stylesheet = this.codeMirrorPath+'/css/jscolors.css';
						break;
					case 'php':
						parser = [
							"parsexml.js",
							"parsecss.js",
							"tokenizejavascript.js",
							"parsejavascript.js",
							"../contrib/php/js/tokenizephp.js",
							"../contrib/php/js/parsephp.js",
							"../contrib/php/js/parsephphtmlmixed.js"
						];
						stylesheet = [
							this.codeMirrorPath+'/css/xmlcolors.css',
							this.codeMirrorPath+'/css/jscolors.css',
							this.codeMirrorPath+'/css/csscolors.css',
							this.codeMirrorPath+'/contrib/php/css/phpcolors.css'
						];
						break;
					case 'htm':
					case 'html':
					case 'xml':
						parser = 'parsexml.js';
						stylesheet = 'xmlcolors.css';
						break;
					default:
						parser = 'parsedummy.js';
						stylesheet = '';
						break;

				}
				var me = this;
				me.codeEditor = new CodeMirror.fromTextArea(me.id, {
					parserfile: parser,
					stylesheet: stylesheet,
					path: me.codeMirrorPath+'/js/',
					textWrapping: false,
					lineNumbers: false,
					iframeClass: 'codemirror-iframe '+me.id,
					content: me.initialConfig.value,
					initCallback: function() {
						me.initialized = true;
						me.fireEvent('initialize', true);
					}
				});
			}
		});
	},
	getValue: function() {
		if (this.initialized) {
			return this.codeEditor.getCode();
		}
		return this.initialConfig.value;
	},
	setValue: function(v) {
		if (this.initialized) {
			this.codeEditor.setCode(v);
		}
	},
	validate: function() {
		this.getValue();
		Ext.ux.form.CodeMirror.superclass.validate.apply(this, arguments);
	}
});
Ext.reg('codemirror', Ext.ux.form.CodeMirror);