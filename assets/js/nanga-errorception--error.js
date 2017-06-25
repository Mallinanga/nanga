setTimeout(function () {
    throw new Error('Something bad happened again.');
}, 10000);
throw new Error('Something bad happened.');
