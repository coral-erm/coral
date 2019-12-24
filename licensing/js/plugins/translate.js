/*
 * gt : Object to translate JavaScript strings
 * function _() : Syntax of gettext
 */

var gt = new Gettext({ 'domain' : 'messages' });
function _(msgid) {
    return gt.gettext(msgid);
}