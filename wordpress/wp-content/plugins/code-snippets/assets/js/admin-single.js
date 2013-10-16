/**
 * Loads CodeMirror on the snippet editor
 */

var editor = CodeMirror.fromTextArea(document.getElementById("snippet_code"), {
	lineNumbers: true,
	matchBrackets: true,
	lineWrapping: true,
	mode: "text/x-php",
	indentUnit: 4,
	indentWithTabs: true,
	enterMode: "keep",
	tabMode: "shift"
});
