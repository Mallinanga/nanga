<div id="embed-api-auth-container"></div>
<div id="chart-container"></div>
<div id="view-selector-container"></div>
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
    gapi.analytics.ready(function () {
        var dataChart;
        var viewSelector;
        gapi.analytics.auth.authorize({
            container: 'embed-api-auth-container',
            clientid: '487950333609.apps.googleusercontent.com'
        });
        viewSelector = new gapi.analytics.ViewSelector({
            container: 'view-selector-container'
        });
        viewSelector.execute();
        dataChart = new gapi.analytics.googleCharts.DataChart({
            query: {
                metrics: 'ga:sessions',
                dimensions: 'ga:date',
                'start-date': '30daysAgo',
                'end-date': 'yesterday'
            },
            chart: {
                container: 'chart-container',
                type: 'LINE',
                options: {
                    width: '100%'
                }
            }
        });
        viewSelector.on('change', function (ids) {
            dataChart.set({query: {ids: ids}}).execute();
        });
    });
</script>
