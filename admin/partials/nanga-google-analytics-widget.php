<div id="embed-api-auth-container"></div>
<div id="chart-container-1"></div><!--<div id="view-selector-container-1"></div>-->
<div id="chart-container-2"></div><!--<div id="view-selector-container-2"></div>-->
<script>
    (function (w, d, s, g, js, fs) {
        g = w.gapi || (w.gapi = {});
        g.analytics = {
            q: [], ready: function (f) {
                this.q.push(f);
            }
        };
        js = d.createElement(s);
        fs = d.getElementsByTagName(s)[0];
        js.src = 'https://apis.google.com/js/platform.js';
        fs.parentNode.insertBefore(js, fs);
        js.onload = function () {
            g.load('analytics');
        };
    }(window, document, 'script'));
</script>
<script>
    (function ($) {
        'use strict';
        $(function () {
        });
        $(window).load(function () {
            $('#chart-container-1');
            $('#chart-container-2');
        });
    })(jQuery);
    gapi.analytics.ready(function () {
        var dataChart1;
        var dataChart2;
        //var viewSelector1;
        //var viewSelector2;
        gapi.analytics.auth.authorize({
            container: 'embed-api-auth-container',
            clientid: '487950333609.apps.googleusercontent.com'
        });
        //viewSelector1 = new gapi.analytics.ViewSelector({ container: 'view-selector-container-1'});
        //viewSelector2 = new gapi.analytics.ViewSelector({ container: 'view-selector-container-2'});
        //viewSelector1.execute();
        //viewSelector2.execute();
        dataChart1 = new gapi.analytics.googleCharts.DataChart({
            query: {
                metrics: 'ga:sessions',
                dimensions: 'ga:country',
                'start-date': '30daysAgo',
                'end-date': 'yesterday',
                'max-results': 6,
                sort: '-ga:sessions'
            },
            chart: {
                container: 'chart-container-1',
                type: 'PIE',
                options: {
                    width: '100%'
                }
            }
        });
        dataChart1.set({query: {ids: 'ga:81300888'}}).execute();
        dataChart2 = new gapi.analytics.googleCharts.DataChart({
            query: {
                metrics: 'ga:sessions',
                dimensions: 'ga:date',
                'start-date': '30daysAgo',
                'end-date': 'yesterday'
            },
            chart: {
                container: 'chart-container-2',
                type: 'LINE',
                options: {
                    width: '100%'
                }
            }
        });
        dataChart2.set({query: {ids: 'ga:81300888'}}).execute();
        //viewSelector1.on('change', function (ids) { dataChart1.set({query: {ids: ids}}).execute(); });
        //viewSelector2.on('change', function (ids) { dataChart2.set({query: {ids: ids}}).execute(); });
    });
</script>
