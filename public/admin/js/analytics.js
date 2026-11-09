/**
 * E-Commerce Analytics Dashboard Interactive Engine
 * Responsive SVG Curved Line & Area charts, Tooltips & Dynamic Date Management
 */
(function () {
  'use strict';

  function formatValue(val, prefix) {
    prefix = prefix || '';
    if (val >= 1000000) {
      return prefix + (val / 1000000).toFixed(1) + 'M';
    }
    if (val >= 1000) {
      return prefix + (val / 1000).toFixed(1) + 'k';
    }
    return prefix + (typeof val === 'number' && val % 1 !== 0 ? val.toFixed(2) : Math.round(val));
  }

  /**
   * Render Responsive Curved SVG Line/Area Graph
   */
  function renderCurveChart(svgId, tooltipId) {
    var svg = document.getElementById(svgId);
    if (!svg) return;

    var tooltip = document.getElementById(tooltipId);
    var rawData = svg.getAttribute('data-chart');
    if (!rawData) return;

    var chartData;
    try {
      chartData = JSON.parse(rawData);
    } catch (e) {
      return;
    }

    var labels = chartData.labels || [];
    var series = chartData.series || [];
    var strokeColor = svg.getAttribute('data-color') || '#0284c7';
    var prefix = svg.getAttribute('data-prefix') || '';

    var width = svg.clientWidth || 540;
    var height = svg.clientHeight || 230;
    var pad = { top: 16, right: 18, bottom: 28, left: 54 };

    svg.setAttribute('viewBox', '0 0 ' + width + ' ' + height);

    var n = series.length;
    if (n === 0) {
      svg.innerHTML = '<text x="' + (width / 2) + '" y="' + (height / 2) + '" text-anchor="middle" fill="#94a3b8" font-size="13">No data in this period</text>';
      return;
    }

    var maxVal = 0;
    series.forEach(function (v) {
      if (v > maxVal) maxVal = v;
    });

    if (maxVal <= 0) {
      maxVal = 10;
    } else {
      maxVal = Math.ceil(maxVal * 1.15);
    }

    function getX(i) {
      if (n <= 1) return pad.left + (width - pad.left - pad.right) / 2;
      return pad.left + (width - pad.left - pad.right) * (i / (n - 1));
    }

    function getY(v) {
      return pad.top + (height - pad.top - pad.bottom) * (1 - v / maxVal);
    }

    var gradId = 'grad_' + svgId;
    var html = '<defs>';
    html += '<linearGradient id="' + gradId + '" x1="0" y1="0" x2="0" y2="1">';
    html += '<stop offset="0%" stop-color="' + strokeColor + '" stop-opacity="0.28"/>';
    html += '<stop offset="100%" stop-color="' + strokeColor + '" stop-opacity="0.0"/>';
    html += '</linearGradient>';
    html += '</defs>';

    // Horizontal Gridlines & Y-Axis Labels
    var gridSteps = 4;
    for (var g = 0; g <= gridSteps; g++) {
      var gridY = pad.top + (height - pad.top - pad.bottom) * (g / gridSteps);
      var gridVal = maxVal * (1 - g / gridSteps);
      html += '<line x1="' + pad.left + '" x2="' + (width - pad.right) + '" y1="' + gridY + '" y2="' + gridY + '" stroke="#f1f5f9" stroke-width="1"/>';
      html += '<text x="' + (pad.left - 8) + '" y="' + (gridY + 4) + '" fill="#94a3b8" font-size="11" font-weight="500" text-anchor="end">' + formatValue(gridVal, prefix) + '</text>';
    }

    // X-Axis Date/Hour Labels
    var step = Math.ceil(n / (width < 500 ? 4 : 7));
    labels.forEach(function (l, i) {
      if (i % step === 0 || i === n - 1) {
        var xPos = getX(i);
        html += '<text x="' + xPos + '" y="' + (height - 8) + '" fill="#94a3b8" font-size="11" font-weight="500" text-anchor="middle">' + l + '</text>';
      }
    });

    // Build Smooth Bézier Curved Path
    var points = [];
    for (var i = 0; i < n; i++) {
      points.push({ x: getX(i), y: getY(series[i]) });
    }

    var pathD = '';
    if (points.length === 1) {
      pathD = 'M ' + points[0].x + ' ' + points[0].y;
    } else if (points.length === 2) {
      pathD = 'M ' + points[0].x + ' ' + points[0].y + ' L ' + points[1].x + ' ' + points[1].y;
    } else {
      pathD = 'M ' + points[0].x + ' ' + points[0].y;
      for (var i = 0; i < points.length - 1; i++) {
        var p0 = i > 0 ? points[i - 1] : points[0];
        var p1 = points[i];
        var p2 = points[i + 1];
        var p3 = i != points.length - 2 ? points[i + 2] : p2;

        var cp1x = p1.x + (p2.x - p0.x) / 6;
        var cp1y = p1.y + (p2.y - p0.y) / 6;
        var cp2x = p2.x - (p3.x - p1.x) / 6;
        var cp2y = p2.y - (p3.y - p1.y) / 6;

        pathD += ' C ' + cp1x.toFixed(1) + ' ' + cp1y.toFixed(1) + ', ' + cp2x.toFixed(1) + ' ' + cp2y.toFixed(1) + ', ' + p2.x.toFixed(1) + ' ' + p2.y.toFixed(1);
      }
    }

    // Area Fill
    var areaD = pathD + ' L ' + getX(n - 1) + ' ' + getY(0) + ' L ' + getX(0) + ' ' + getY(0) + ' Z';
    html += '<path d="' + areaD + '" fill="url(#' + gradId + ')"/>';

    // Stroke Line
    html += '<path d="' + pathD + '" fill="none" stroke="' + strokeColor + '" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>';

    // Interactive Hover Crosshair Line and Circle
    html += '<line id="' + svgId + '_crosshair" x1="0" x2="0" y1="' + pad.top + '" y2="' + (height - pad.bottom) + '" stroke="#cbd5e1" stroke-width="1.5" stroke-dasharray="3 3" opacity="0"/>';
    html += '<circle id="' + svgId + '_point" cx="0" cy="0" r="4.5" fill="#ffffff" stroke="' + strokeColor + '" stroke-width="3" opacity="0"/>';

    svg.innerHTML = html;

    // Hover Interaction
    var crosshair = document.getElementById(svgId + '_crosshair');
    var pointDot = document.getElementById(svgId + '_point');

    svg.onmousemove = function (e) {
      var rect = svg.getBoundingClientRect();
      var mouseX = e.clientX - rect.left;
      var availWidth = width - pad.left - pad.right;
      var relX = mouseX - pad.left;
      var ratio = Math.max(0, Math.min(1, relX / availWidth));
      var idx = Math.round(ratio * (n - 1));

      if (idx < 0 || idx >= n) return;

      var px = getX(idx);
      var py = getY(series[idx]);

      if (crosshair) {
        crosshair.setAttribute('x1', px);
        crosshair.setAttribute('x2', px);
        crosshair.setAttribute('opacity', '1');
      }

      if (pointDot) {
        pointDot.setAttribute('cx', px);
        pointDot.setAttribute('cy', py);
        pointDot.setAttribute('opacity', '1');
      }

      if (tooltip) {
        var valStr = prefix ? prefix + Number(series[idx]).toFixed(2) : Number(series[idx]).toLocaleString();
        tooltip.innerHTML = '<div style="font-size:11px;color:#94a3b8;margin-bottom:2px;">' + labels[idx] + '</div><div>' + valStr + '</div>';
        tooltip.style.opacity = '1';
        tooltip.style.left = px + 'px';
        tooltip.style.top = py + 'px';
      }
    };

    svg.onmouseleave = function () {
      if (crosshair) crosshair.setAttribute('opacity', '0');
      if (pointDot) pointDot.setAttribute('opacity', '0');
      if (tooltip) tooltip.style.opacity = '0';
    };
  }

  function initCharts() {
    renderCurveChart('salesOverTimeChart', 'salesChartTooltip');
    renderCurveChart('sessionsOverTimeChart', 'sessionsChartTooltip');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCharts);
  } else {
    initCharts();
  }

  // Redraw on window resize
  var resizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(initCharts, 120);
  });
})();

