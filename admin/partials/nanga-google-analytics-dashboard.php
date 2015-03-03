<style>
    html{
        /*background-image:url('/wp-content/plugins/nanga/admin/img/polygons.jpg');*/
        /*background-repeat:no-repeat;*/
        /*background-size:cover;*/
    }
</style>
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
<script src="https://ga-dev-tools.appspot.com/public/javascript/Chart.min.js"></script>
<script src="https://ga-dev-tools.appspot.com/public/javascript/moment.min.js"></script>
<script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/view-selector2.js"></script>
<script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/date-range-selector.js"></script>
<script src="https://ga-dev-tools.appspot.com/public/javascript/embed-api/active-users.js"></script>
<div class="wrap">
    <h2>Google Analytics</h2>

    <div class="Dashboard Dashboard--full">
        <header class="Dashboard-header">
            <ul class="FlexGrid">
                <li class="FlexGrid-item">
                    <div class="Titles">
                        <h1 class="Titles-main" id="view-name">Select a View</h1>

                        <div class="Titles-sub">Various visualizations</div>
                    </div>
                </li>
                <li class="FlexGrid-item FlexGrid-item--fixed">
                    <div id="active-users-container"></div>
                </li>
            </ul>
            <div id="view-selector-container"></div>
        </header>
        <ul class="FlexGrid FlexGrid--halves">
            <li class="FlexGrid-item">
                <div class="Chartjs">
                    <header class="Titles">
                        <h1 class="Titles-main">This Week vs Last Week</h1>

                        <div class="Titles-sub">By sessions</div>
                    </header>
                    <figure class="Chartjs-figure" id="chart-1-container"></figure>
                    <ol class="Chartjs-legend" id="legend-1-container"></ol>
                </div>
            </li>
            <li class="FlexGrid-item">
                <div class="Chartjs">
                    <header class="Titles">
                        <h1 class="Titles-main">This Year vs Last Year</h1>

                        <div class="Titles-sub">By users</div>
                    </header>
                    <figure class="Chartjs-figure" id="chart-2-container"></figure>
                    <ol class="Chartjs-legend" id="legend-2-container"></ol>
                </div>
            </li>
            <li class="FlexGrid-item">
                <div class="Chartjs">
                    <header class="Titles">
                        <h1 class="Titles-main">Top Browsers</h1>

                        <div class="Titles-sub">By pageview</div>
                    </header>
                    <figure class="Chartjs-figure" id="chart-3-container"></figure>
                    <ol class="Chartjs-legend" id="legend-3-container"></ol>
                </div>
            </li>
            <li class="FlexGrid-item">
                <div class="Chartjs">
                    <header class="Titles">
                        <h1 class="Titles-main">Top Countries</h1>

                        <div class="Titles-sub">By sessions</div>
                    </header>
                    <figure class="Chartjs-figure" id="chart-4-container"></figure>
                    <ol class="Chartjs-legend" id="legend-4-container"></ol>
                </div>
            </li>
        </ul>
    </div>
</div>
<script src="/wp-content/plugins/nanga/admin/js/nanga-google-analytics-dashboard.js"></script>
