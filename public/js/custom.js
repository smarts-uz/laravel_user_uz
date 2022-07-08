var input = document.getElementById('car_1');
var currentValue;

input.addEventListener('input', function(event) {
    var cursorPosition = getCaretPosition(input);
    var valueBefore = input.value;
    var lengthBefore = input.value.length;
    var specialCharsBefore = getSpecialCharsOnSides(input.value);
    var number = removeThousandSeparators(input.value);

    if (input.value == '') {
        return;
    }

    input.value = formatNumber(number.replace(getCommaSeparator(), '.'));

    // if deleting the comma, delete it correctly
    if (currentValue == input.value && currentValue == valueBefore.substr(0, cursorPosition) + getThousandSeparator() + valueBefore.substr(cursorPosition)) {
        input.value = formatNumber(removeThousandSeparators(valueBefore.substr(0, cursorPosition-1) + valueBefore.substr(cursorPosition)));
        cursorPosition--;
    }

    // if entering comma for separation, leave it in there (as well support .000)
    var commaSeparator = getCommaSeparator();
    if (valueBefore.endsWith(commaSeparator) || valueBefore.endsWith(commaSeparator+'0') || valueBefore.endsWith(commaSeparator+'00') || valueBefore.endsWith(commaSeparator+'000')) {
        input.value = input.value + valueBefore.substring(valueBefore.indexOf(commaSeparator));
    }

    // move cursor correctly if thousand separator got added or removed
    var specialCharsAfter = getSpecialCharsOnSides(input.value);
    if (specialCharsBefore[0] < specialCharsAfter[0]) {
        cursorPosition += specialCharsAfter[0] - specialCharsBefore[0];
    } else if (specialCharsBefore[0] > specialCharsAfter[0]) {
        cursorPosition -= specialCharsBefore[0] - specialCharsAfter[0];
    }
    setCaretPosition(input, cursorPosition);

    currentValue = input.value;
});

function getSpecialCharsOnSides(x, cursorPosition) {
    var specialCharsLeft = x.substring(0, cursorPosition).replace(/[0-9]/g, '').length;
    var specialCharsRight = x.substring(cursorPosition).replace(/[0-9]/g, '').length;
    return [specialCharsLeft, specialCharsRight]
}

function formatNumber(x) {
    return getNumberFormat().format(x);
}

function removeThousandSeparators(x) {
    return x.toString().replace(new RegExp(escapeRegExp(getThousandSeparator()), 'g'), "");
}

function getThousandSeparator() {
    return getNumberFormat().format('1000').replace(/[0-9]/g, '')[0];
}

function getCommaSeparator() {
    return getNumberFormat().format('0.01').replace(/[0-9]/g, '')[0];
}

function getNumberFormat() {
    return new Intl.NumberFormat();
}

/* From: http://stackoverflow.com/a/6969486/496992 */
function escapeRegExp(str) {
    return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}

/*
** Returns the caret (cursor) position of the specified text field.
** Return value range is 0-oField.value.length.
** From: http://stackoverflow.com/a/2897229/496992
*/
function getCaretPosition (oField) {
    // Initialize
    var iCaretPos = 0;

    // IE Support
    if (document.selection) {

        // Set focus on the element
        oField.focus();

        // To get cursor position, get empty selection range
        var oSel = document.selection.createRange();

        // Move selection start to 0 position
        oSel.moveStart('character', -oField.value.length);

        // The caret position is selection length
        iCaretPos = oSel.text.length;
    }

    // Firefox support
    else if (oField.selectionStart || oField.selectionStart == '0')
        iCaretPos = oField.selectionStart;

    // Return results
    return iCaretPos;
}

/* From: http://stackoverflow.com/a/512542/496992 */
function setCaretPosition(elem, caretPos) {
    if(elem != null) {
        if(elem.createTextRange) {
            var range = elem.createTextRange();
            range.move('character', caretPos);
            range.select();
        }
        else {
            if(elem.selectionStart) {
                elem.focus();
                elem.setSelectionRange(caretPos, caretPos);
            }
            else
                elem.focus();
        }
    }
}
