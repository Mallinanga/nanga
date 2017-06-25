// window.onerror = function nangaErrorception(message, url, lineNumber, columnNumber, error) {
//     console.log(message);
//     console.log(url);
//     console.log(lineNumber);
//     console.log(columnNumber);
//     console.log(error);
//     return true;
// };
setInterval(function () {
    if (_errs && _errs.splice) {
        _errs.splice(0, _errs.length);
    }
}, 5000);
var http = new XMLHttpRequest();
http.open('POST', 'errorception', true);
http.setRequestHeader('Content-Type', 'application/json');
// http.send(JSON.stringify({name: "John Rambo", time: "2pm"}));
http.send(JSON.stringify(_errs));
