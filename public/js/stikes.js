$(document).ready(function () {
    $(".datepicker").datepicker({
            "changeMonth": true,
            "changeYear": true,
            "dateFormat": "yy-mm-dd",
        })
        .attr('readonly', true)
        .css("background-color", "white");

    $(".select2").select2();
    $(".select2_tag").select2({
        tags: true,
        tokenSeparators: [',', ' ']
    });

    $(".datepicker_agent").datepicker({
        "dateFormat": "yy-mm-dd",
        minDate: new Date(),
    });

    $(".datepicker_valid_until_date").datepicker({
            "changeMonth": true,
            "changeYear": true,
            "dateFormat": "yy-mm-dd",
            yearRange: '+0:+20',
        })
        .attr('readonly', true)
        .css("background-color", "white");

    $(".btn").click(function () {
        var $btn = $(this);
        $btn.button('loading');
        // simulating a timeout
        setTimeout(function () {
            $btn.button('reset');
        }, 1000);
    });

});

function readURL(input, image_id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#' + image_id).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]); // convert to base64 string
    }
}

function money(num) {
  return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
}

function makeid(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}

function parse_str (str, array) {
    // http://jsphp.co/jsphp/fn/view/parse_str
    // +   original by: Cagri Ekin
    // +   improved by: Michael White (http://getsprink.com)
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // +   reimplemented by: stag019
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: stag019
    // -    depends on: urldecode
    // +   input by: Dreamer
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // %        note 1: When no argument is specified, will put variables in global scope.
    // *     example 1: var arr = {};
    // *     example 1: parse_str('first=foo&second=bar', arr);
    // *     results 1: arr == { first: 'foo', second: 'bar' }
    // *     example 2: var arr = {};
    // *     example 2: parse_str('str_a=Jack+and+Jill+didn%27t+see+the+well.', arr);
    // *     results 2: arr == { str_a: "Jack and Jill didn't see the well." }
    var glue1 = '=',
        glue2 = '&',
        array2 = String(str).replace(/^&?([\s\S]*?)&?$/, '$1').split(glue2),
        i, j, chr, tmp, key, value, bracket, keys, evalStr, that = this,
        fixStr = function (str) {
            return that.urldecode(str).replace(/([\\"'])/g, '\\$1').replace(/\n/g, '\\n').replace(/\r/g, '\\r');
        };

    if (!array) {
        array = this.window;
    }

    for (i = 0; i < array2.length; i++) {
        tmp = array2[i].split(glue1);
        if (tmp.length < 2) {
            tmp = [tmp, ''];
        }
        key = fixStr(tmp[0]);
        value = fixStr(tmp[1]);
        while (key.charAt(0) === ' ') {
            key = key.substr(1);
        }
        if (key.indexOf('\0') !== -1) {
            key = key.substr(0, key.indexOf('\0'));
        }
        if (key && key.charAt(0) !== '[') {
            keys = [];
            bracket = 0;
            for (j = 0; j < key.length; j++) {
                if (key.charAt(j) === '[' && !bracket) {
                    bracket = j + 1;
                } else if (key.charAt(j) === ']') {
                    if (bracket) {
                        if (!keys.length) {
                            keys.push(key.substr(0, bracket - 1));
                        }
                        keys.push(key.substr(bracket, j - bracket));
                        bracket = 0;
                        if (key.charAt(j + 1) !== '[') {
                            break;
                        }
                    }
                }
            }
            if (!keys.length) {
                keys = [key];
            }
            for (j = 0; j < keys[0].length; j++) {
                chr = keys[0].charAt(j);
                if (chr === ' ' || chr === '.' || chr === '[') {
                    keys[0] = keys[0].substr(0, j) + '_' + keys[0].substr(j + 1);
                }
                if (chr === '[') {
                    break;
                }
            }
            evalStr = 'array';
            for (j = 0; j < keys.length; j++) {
                key = keys[j];
                if ((key !== '' && key !== ' ') || j === 0) {
                    key = "'" + key + "'";
                } else {
                    key = eval(evalStr + '.push([]);') - 1;
                }
                evalStr += '[' + key + ']';
                if (j !== keys.length - 1 && eval('typeof ' + evalStr) === 'undefined') {
                    eval(evalStr + ' = [];');
                }
            }
            evalStr += " = '" + value + "';\n";
            eval(evalStr);
        }
    }
}