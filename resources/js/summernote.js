/**
 * File to hold all our summernote related JavaScript
 */

require('summernote/dist/summernote-bs4');
window.docsSoap = require('docs-soap');

$(document).ready(function() {
    var docsSoap = window.docsSoap.default;
    $('#emailContent').summernote({
        callbacks: {
          onPaste: function(e) {
            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('text/html');
            var clean = docsSoap(bufferText);
            clean = clean.replace(/<\/p><br><p>/gm, '</p><p>');
            clean = clean.replace(/<li><p>/gm, '<li>');
            clean = clean.replace(/<\/p><\/li>/gm, '</li>');
            clean = clean.replace(/<\/?body>/gm, '');
            clean = clean.replace(/<br class=.*?>/gm, '');
            clean = clean.replace(/(<br>)+$/, '');
            e.preventDefault();
                // Firefox fix
                setTimeout(function () {
                    window.document.execCommand('insertHtml', false, clean);
                }, 10);
            }
        }
    });
});
