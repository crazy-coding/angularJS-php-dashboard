/**
 * HELPER FUNCTIONS
 *
 */

 //It restrict the non-numbers
var specialKeys = new Array();
specialKeys.push(8,46); //Backspace
function IsNumeric(e) {
    var keyCode = e.which ? e.which : e.keyCode;
    var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
    return ret;
}

// Return url parameter/query string
function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

// live datetime
function liveDateTime (id) {
    var date = new Date;
    var year = date.getFullYear();
    var month = date.getMonth();
    var months = new Array('Jan', 'Feb', 'Mar', 'April', 'May', 'Jun', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
    var d = date.getDate();
    var day = date.getDay();
    var days = new Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds =date.getSeconds();
    var dn = "PM";
    if (hours<12)
    dn = "AM";
    if (hours>12);
    hours = hours-12;
    if (hours==0)
    hours = 12;
    if (minutes <= 9)
    minutes="0"+minutes;
    if (seconds <= 9)
    seconds = "0"+seconds;
    var ctime = hours+":"+minutes+":"+seconds+" "+dn;
    document.getElementById(id).innerHTML = ''+days[day]+' '+d+' '+months[month]+' '+year+' '+ctime;
    setTimeout('liveDateTime("'+id+'");','1000');
    return true;
}

function getNumber(t) {
    return window.accounting.unformat(t);
}

function formatDecimal(t, e) {
    return window.accounting.formatNumber(t, e, "", ".");
}

function is_numeric(t) {
    return ("number" == typeof t || "string" == typeof t && -1 === " \n\r\t\f\v\u2028\u2029　".indexOf(t.slice(-1))) && "" !== t && !isNaN(t);
}

function is_float(t) {
    return !(+t !== t || isFinite(t) && !(t % 1));
}

function formatDate(date) {
  var monthNames = [
    "January", "February", "March",
    "April", "May", "June", "July",
    "August", "September", "October",
    "November", "December"
  ];
  var day = date.getDate();
  var monthIndex = date.getMonth();
  var year = date.getFullYear();
  return day + ' ' + monthNames[monthIndex] + ' ' + year;
}