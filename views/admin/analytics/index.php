<?php
$page_title = 'Analytics & Reports';
$current_admin_page = 'analytics';
$page_script = 'analytics.js';
include dirname(__DIR__) . '/includes/header.php';

$range = $range ?? '30d';
$from = $from ?? date('Y-m-d', strtotime('-29 days'));
$to = $to ?? date('Y-m-d');
$analytics = $analytics ?? \App\Models\Analytics::getAnalyticsData($from, $to);

$sales = $analytics['sales'] ?? ['total_formatted' => '$0.00', 'delta' => 0, 'chart' => ['labels' => [], 'series' => []]];
$sessions = $analytics['sessions'] ?? ['total' => 0, 'uniques' => 0, 'delta' => 0, 'chart' => ['labels' => [], 'series' => []]];
$orders = $analytics['orders'] ?? ['count' => 0, 'aov' => '$0.00'];
$funnel = $analytics['funnel'] ?? ['conversion_rate' => '0%', 'stages' => []];
$topProducts = $analytics['top_products'] ?? [];
$browserRefs = $analytics['browser_referrers'] ?? [];
$socialRefs = $analytics['social_referrers'] ?? [];
$landingPages = $analytics['landing_pages'] ?? [];
$devices = $analytics['devices'] ?? [];
$locations = $analytics['locations'] ?? [];
$rangeInfo = $analytics['range'] ?? ['label' => 'Last 30 Days'];

$salesChartJson = json_encode($sales['chart'], JSON_UNESCAPED_SLASHES);
$sessionsChartJson = json_encode($sessions['chart'], JSON_UNESCAPED_SLASHES);
?>

<!-- Title & Action Bar with Dynamic Date Filter -->
<div class="page-title-bar">
  <div class="page-heading-group">
    <div class="d-flex align-center gap-sm">
      <h1>Analytics &amp; Performance</h1>
      <span class="badge-status badge-completed" style="font-size: 11px;">
        <span class="status-dot"></span> Live Telemetry
      </span>
    </div>
    <p class="page-subtitle">Track sales over time, visitor sessions, checkout conversion funnels, and customer acquisition.</p>
  </div>
  
  <div class="page-actions-group">
    <!-- Date Range Filter Group -->
    <div class="analytics-filter-bar">
      <div class="btn-group-segmented">
        <a href="<?= url('admin/analytics?range=today') ?>" class="btn-seg <?= $range === 'today' ? 'active' : '' ?>">Today</a>
        <a href="<?= url('admin/analytics?range=yesterday') ?>" class="btn-seg <?= $range === 'yesterday' ? 'active' : '' ?>">Yesterday</a>
        <a href="<?= url('admin/analytics?range=7d') ?>" class="btn-seg <?= $range === '7d' ? 'active' : '' ?>">7D</a>
        <a href="<?= url('admin/analytics?range=30d') ?>" class="btn-seg <?= $range === '30d' ? 'active' : '' ?>">30D</a>
        <a href="<?= url('admin/analytics?range=90d') ?>" class="btn-seg <?= $range === '90d' ? 'active' : '' ?>">90D</a>
      </div>

      <!-- Custom Date Picker -->
      <form method="GET" action="<?= url('admin/analytics') ?>" class="analytics-date-form">
        <input type="hidden" name="range" value="custom">
        <div class="date-input-wrap">
          <input type="date" name="from" id="analyticsFromDate" value="<?= htmlspecialchars($from) ?>" class="admin-form-input date-picker-sm" title="From Date">
          <span class="date-sep">–</span>
          <input type="date" name="to" id="analyticsToDate" value="<?= htmlspecialchars($to) ?>" class="admin-form-input date-picker-sm" title="To Date">
          <button type="submit" class="btn-admin btn-admin-secondary btn-admin-sm">Apply</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- SECTION 1: TWO MAIN GRAPHS (Total Sales Over Time & Sessions Over Time) -->
<div class="admin-grid-2 mb-xl">
  
  <!-- 1. Total Sales Over Time Card (Screenshot 1) -->
  <div class="admin-card">
    <div class="admin-card-header d-flex justify-between align-center">
      <div>
        <span class="analytics-metric-title" title="Gross sales over the selected time period">Total sales over time</span>
        <div class="analytics-big-metric" id="metricTotalSales"><?= $sales['total_formatted'] ?></div>
      </div>
      <div class="text-right">
        <?php $sUp = ($sales['delta'] >= 0); ?>
        <span class="stat-trend <?= $sUp ? 'up' : 'down' ?>" id="metricSalesDelta">
          <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="<?= $sUp ? '23 6 13.5 15.5 8.5 10.5 1 18' : '23 18 13.5 8.5 8.5 13.5 1 6' ?>"></polyline>
          </svg>
          <?= ($sUp ? '+' : '') . $sales['delta'] ?>%
        </span>
        <div class="text-xs text-muted">vs previous period</div>
      </div>
    </div>
    <div class="admin-card-body">
      <!-- SVG Sales Line Chart -->
      <div class="chart-container" style="position: relative; height: 230px;">
        <svg class="analytics-svg-chart" id="salesOverTimeChart" data-chart='<?= htmlspecialchars($salesChartJson, ENT_QUOTES, 'UTF-8') ?>' data-color="#0284c7" data-prefix="$" style="width: 100%; height: 100%;"></svg>
        <div class="chart-tooltip" id="salesChartTooltip" style="opacity: 0;"></div>
      </div>
      <div class="chart-legend-bottom">
        <span class="chart-legend-pill">
          <span class="legend-dot" style="background: #0284c7;"></span>
          <span id="salesChartLegendText"><?= htmlspecialchars($rangeInfo['label']) ?></span>
        </span>
      </div>
    </div>
  </div>

  <!-- 2. Sessions Over Time Card (Screenshot 1) -->
  <div class="admin-card">
    <div class="admin-card-header d-flex justify-between align-center">
      <div>
        <span class="analytics-metric-title" title="Total visitor sessions recorded over time">Sessions over time</span>
        <div class="analytics-big-metric" id="metricTotalSessions"><?= number_format($sessions['total']) ?></div>
      </div>
      <div class="text-right">
        <?php $sessUp = ($sessions['delta'] >= 0); ?>
        <span class="stat-trend <?= $sessUp ? 'up' : 'down' ?>" id="metricSessionsDelta">
          <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="<?= $sessUp ? '23 6 13.5 15.5 8.5 10.5 1 18' : '23 18 13.5 8.5 8.5 13.5 1 6' ?>"></polyline>
          </svg>
          <?= ($sessUp ? '+' : '') . $sessions['delta'] ?>%
        </span>
        <div class="text-xs text-muted"><?= number_format($sessions['uniques']) ?> unique visitors</div>
      </div>
    </div>
    <div class="admin-card-body">
      <!-- SVG Sessions Line Chart -->
      <div class="chart-container" style="position: relative; height: 230px;">
        <svg class="analytics-svg-chart" id="sessionsOverTimeChart" data-chart='<?= htmlspecialchars($sessionsChartJson, ENT_QUOTES, 'UTF-8') ?>' data-color="#2563eb" data-prefix="" style="width: 100%; height: 100%;"></svg>
        <div class="chart-tooltip" id="sessionsChartTooltip" style="opacity: 0;"></div>
      </div>
      <div class="chart-legend-bottom">
        <span class="chart-legend-pill">
          <span class="legend-dot" style="background: #2563eb;"></span>
          <span id="sessionsChartLegendText"><?= htmlspecialchars($rangeInfo['label']) ?></span>
        </span>
      </div>
    </div>
  </div>

</div>

<!-- SECTION 2: CONVERSION RATE BREAKDOWN FUNNEL (Screenshot 2) -->
<div class="admin-card mb-xl">
  <div class="admin-card-header d-flex justify-between align-center">
    <div>
      <span class="analytics-metric-title" title="Percentage of visitor sessions that completed a purchase">Conversion rate breakdown</span>
      <div class="analytics-big-metric" style="color: #0f172a;" id="metricConversionRate"><?= $funnel['conversion_rate'] ?></div>
    </div>
    <div class="text-right">
      <span class="badge-status badge-completed font-semibold">Storewide Checkout Funnel</span>
    </div>
  </div>
  
  <div class="admin-card-body">
    <!-- 4-Step Funnel Numbers Grid -->
    <div class="funnel-steps-grid">
      <!-- Step 1: Sessions -->
      <div class="funnel-step-item">
        <div class="funnel-step-header">
          <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
          <span>Sessions</span>
        </div>
        <div class="funnel-step-numbers">
          <strong class="funnel-pct"><?= $funnel['stages']['sessions']['pct'] ?? '100%' ?></strong>
          <span class="funnel-count" id="funnelCountSessions"><?= number_format($funnel['stages']['sessions']['count'] ?? 0) ?></span>
        </div>
      </div>

      <!-- Step 2: Added to cart -->
      <div class="funnel-step-item">
        <div class="funnel-step-header">
          <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
          <span>Added to cart</span>
        </div>
        <div class="funnel-step-numbers">
          <strong class="funnel-pct"><?= $funnel['stages']['cart']['pct'] ?? '0%' ?></strong>
          <span class="funnel-count" id="funnelCountCart"><?= number_format($funnel['stages']['cart']['count'] ?? 0) ?></span>
        </div>
      </div>

      <!-- Step 3: Reached checkout -->
      <div class="funnel-step-item">
        <div class="funnel-step-header">
          <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
          <span>Reached checkout</span>
        </div>
        <div class="funnel-step-numbers">
          <strong class="funnel-pct"><?= $funnel['stages']['checkout']['pct'] ?? '0%' ?></strong>
          <span class="funnel-count" id="funnelCountCheckout"><?= number_format($funnel['stages']['checkout']['count'] ?? 0) ?></span>
        </div>
      </div>

      <!-- Step 4: Completed checkout -->
      <div class="funnel-step-item">
        <div class="funnel-step-header">
          <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
          <span>Completed checkout</span>
        </div>
        <div class="funnel-step-numbers">
          <strong class="funnel-pct text-success"><?= $funnel['stages']['completed']['pct'] ?? '0%' ?></strong>
          <span class="funnel-count" id="funnelCountOrders"><?= number_format($funnel['stages']['completed']['count'] ?? 0) ?></span>
        </div>
      </div>
    </div>

    <!-- Connected Step-Down Funnel Graphic -->
    <div class="funnel-visual-wrap">
      <div class="funnel-visual-bar b-1" style="flex: 100;" title="Sessions: 100%"></div>
      <div class="funnel-connector"></div>
      <div class="funnel-visual-bar b-2" style="flex: <?= max(12, (float)($funnel['stages']['cart']['pct'] ?? 25)) ?>;" title="Cart Additions"></div>
      <div class="funnel-connector"></div>
      <div class="funnel-visual-bar b-3" style="flex: <?= max(10, (float)($funnel['stages']['checkout']['pct'] ?? 18)) ?>;" title="Checkouts Started"></div>
      <div class="funnel-connector"></div>
      <div class="funnel-visual-bar b-4" style="flex: <?= max(8, (float)($funnel['stages']['completed']['pct'] ?? 5) * 3) ?>;" title="Completed Orders"></div>
    </div>
  </div>
</div>

<!-- SECTION 3: NUMBER OF ORDERS & TOTAL SALES BY PRODUCT (Screenshot 2) -->
<div class="admin-grid-2 mb-xl">

  <!-- Number of Orders & Key Financials Card -->
  <div class="admin-card">
    <div class="admin-card-header d-flex justify-between align-center">
      <h2 class="admin-card-title">Number of Orders &amp; Financials</h2>
      <a href="<?= url('admin/orders') ?>" class="btn-admin btn-admin-secondary btn-admin-sm">All Orders</a>
    </div>
    <div class="admin-card-body">
      <div class="stats-grid mb-md" style="grid-template-columns: 1fr 1fr; gap: 12px;">
        <div class="stat-card" style="padding: 16px;">
          <div class="stat-info">
            <span class="stat-label">Total Orders</span>
            <div class="stat-value" id="boxOrdersCount"><?= number_format($orders['count']) ?></div>
            <span class="text-xs text-muted">Orders in date range</span>
          </div>
          <div class="stat-icon-wrap blue">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line></svg>
          </div>
        </div>

        <div class="stat-card" style="padding: 16px;">
          <div class="stat-info">
            <span class="stat-label">Average Order Value</span>
            <div class="stat-value" id="boxOrdersAov"><?= $orders['aov'] ?></div>
            <span class="text-xs text-muted">Revenue per order</span>
          </div>
          <div class="stat-icon-wrap green">
            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
          </div>
        </div>
      </div>

      <div class="info-list-group">
        <div class="info-list-item d-flex justify-between align-center py-sm">
          <span class="text-muted">Net Gross Sales</span>
          <strong id="boxNetSales"><?= $sales['total_formatted'] ?></strong>
        </div>
        <div class="info-list-item d-flex justify-between align-center py-sm">
          <span class="text-muted">Total Visitor Sessions</span>
          <strong id="boxTotalVis"><?= number_format($sessions['total']) ?></strong>
        </div>
        <div class="info-list-item d-flex justify-between align-center py-sm">
          <span class="text-muted">Overall Funnel Conversion</span>
          <strong class="text-success" id="boxConv"><?= $funnel['conversion_rate'] ?></strong>
        </div>
      </div>
    </div>
  </div>

  <!-- Total Sales by Product Card (Screenshot 2) -->
  <div class="admin-card">
    <div class="admin-card-header d-flex justify-between align-center">
      <h2 class="admin-card-title">Total Sales by Product</h2>
      <a href="<?= url('admin/products') ?>" class="btn-admin btn-admin-secondary btn-admin-sm">Catalog</a>
    </div>
    <div class="admin-card-body p-0">
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Product</th>
              <th class="text-center">Units Sold</th>
              <th class="text-right">Revenue</th>
            </tr>
          </thead>
          <tbody id="topProductsTableBody">
            <?php if (!empty($topProducts)): ?>
              <?php foreach ($topProducts as $tp): 
                $sold = (int)($tp['units_sold'] ?? 0);
                $rev = (float)($tp['total_revenue'] ?? 0);
                $img = !empty($tp['featured_image']) ? asset($tp['featured_image']) : asset('assets/images/product-1.webp');
              ?>
                <tr>
                  <td>
                    <div class="table-product-cell">
                      <img src="<?= $img ?>" alt="<?= htmlspecialchars($tp['product_title']) ?>" class="table-product-thumb" style="width: 38px; height: 38px; object-fit: cover; border-radius: 6px;" onerror="this.src='<?= asset('assets/images/vape-image.webp') ?>'">
                      <div class="table-product-info">
                        <span class="table-product-title font-semibold"><?= htmlspecialchars($tp['product_title']) ?></span>
                      </div>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="badge-status <?= $sold > 0 ? 'badge-completed' : 'badge-pending' ?>">
                      <?= $sold ?> sold
                    </span>
                  </td>
                  <td class="text-right">
                    <strong><?= $rev > 0 ? '$' . number_format($rev, 2) : '$0.00' ?></strong>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" class="text-center p-md text-muted">No product sales in selected period.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- SECTION 4: TRAFFIC ACQUISITION (Browser Referrers & Social Referrers) -->
<div class="admin-grid-2 mb-xl">
  
  <!-- Sessions by Browser Referrer Card (Screenshot 2) -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">Sessions by Browser Referrer</h2>
    </div>
    <div class="admin-card-body">
      <ul class="analytics-bar-list" id="browserReferrerList">
        <?php foreach ($browserRefs as $br): ?>
          <li class="analytics-bar-item">
            <div class="bar-header">
              <span class="bar-name"><?= htmlspecialchars($br['name']) ?></span>
              <span class="bar-val"><strong><?= number_format($br['count']) ?></strong> <span class="text-muted text-xs">(<?= $br['pct'] ?>)</span></span>
            </div>
            <div class="bar-track">
              <div class="bar-fill" style="width: <?= $br['bar_pct'] ?>; background: #0284c7;"></div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <!-- Sessions by Social Referrer Card (Screenshot 2) -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">Sessions by Social Referrer</h2>
    </div>
    <div class="admin-card-body">
      <ul class="analytics-bar-list" id="socialReferrerList">
        <?php foreach ($socialRefs as $sr): ?>
          <li class="analytics-bar-item">
            <div class="bar-header">
              <span class="bar-name"><?= htmlspecialchars($sr['name']) ?></span>
              <span class="bar-val"><strong><?= number_format($sr['count']) ?></strong> <span class="text-muted text-xs">(<?= $sr['pct'] ?>)</span></span>
            </div>
            <div class="bar-track">
              <div class="bar-fill" style="width: <?= $sr['bar_pct'] ?>; background: #8b5cf6;"></div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

</div>

<!-- SECTION 5: LANDING PAGES, DEVICE TYPES & LOCATIONS (Screenshot 2) -->
<div class="admin-grid-3 mb-xl">

  <!-- Sessions by Landing Page Card (Screenshot 2) -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">Sessions by Landing Page</h2>
    </div>
    <div class="admin-card-body p-0">
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Page Path</th>
              <th class="text-right">Sessions</th>
            </tr>
          </thead>
          <tbody id="landingPagesTableBody">
            <?php if (!empty($landingPages)): ?>
              <?php foreach ($landingPages as $lp): ?>
                <tr>
                  <td>
                    <span class="table-cell-text font-medium" title="<?= htmlspecialchars($lp['url']) ?>" style="max-width: 170px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                      <?= htmlspecialchars($lp['url']) ?>
                    </span>
                  </td>
                  <td class="text-right">
                    <strong><?= number_format($lp['sessions']) ?></strong>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="2" class="text-center p-md text-muted">No page hits recorded yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Sessions by Device Type Card (Screenshot 2) -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">Sessions by Device Type</h2>
    </div>
    <div class="admin-card-body">
      <div class="device-donut-wrap mb-md text-center">
        <!-- Responsive Donut Representation -->
        <svg viewBox="0 0 100 100" class="donut-svg" style="width: 120px; height: 120px;">
          <?php
            $accumPct = 0;
            foreach ($devices as $d):
              $pct = (float)$d['raw_pct'];
              if ($pct <= 0) continue;
              $dashArray = "{$pct} " . (100 - $pct);
              $dashOffset = 25 - $accumPct;
              $accumPct += $pct;
          ?>
            <circle cx="50" cy="50" r="15.915" fill="transparent" stroke="<?= $d['color'] ?>" stroke-width="6" stroke-dasharray="<?= $dashArray ?>" stroke-dashoffset="<?= $dashOffset ?>"></circle>
          <?php endforeach; ?>
          <text x="50" y="54" font-size="12" font-weight="700" text-anchor="middle" fill="#0f172a"><?= $devices[0]['pct'] ?? '0%' ?></text>
        </svg>
      </div>

      <ul class="analytics-bar-list" id="deviceList">
        <?php foreach ($devices as $d): ?>
          <li class="analytics-bar-item">
            <div class="bar-header">
              <span class="bar-name d-flex align-center gap-xs">
                <span class="legend-dot" style="background: <?= $d['color'] ?>;"></span>
                <?= htmlspecialchars($d['name']) ?>
              </span>
              <span class="bar-val"><strong><?= $d['pct'] ?></strong> <span class="text-muted text-xs">(<?= number_format($d['count']) ?>)</span></span>
            </div>
            <div class="bar-track">
              <div class="bar-fill" style="width: <?= $d['pct'] ?>; background: <?= $d['color'] ?>;"></div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

  <!-- Sessions by Location Card (Screenshot 2) -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">Sessions by Location</h2>
    </div>
    <div class="admin-card-body">
      <ul class="analytics-bar-list" id="locationList">
        <?php foreach ($locations as $loc): ?>
          <li class="analytics-bar-item">
            <div class="bar-header">
              <span class="bar-name d-flex align-center gap-xs">
                <span class="country-flag"><?= $loc['flag'] ?></span>
                <?= htmlspecialchars($loc['country']) ?>
              </span>
              <span class="bar-val"><strong><?= number_format($loc['count']) ?></strong> <span class="text-muted text-xs">(<?= $loc['pct'] ?>)</span></span>
            </div>
            <div class="bar-track">
              <div class="bar-fill" style="width: <?= $loc['bar_pct'] ?>; background: #059669;"></div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>

</div>

<!-- Styles Specific to Analytics Engine integrated in theme -->
<style>
.analytics-filter-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}
.btn-group-segmented {
  display: inline-flex;
  background: #f1f5f9;
  border-radius: 8px;
  padding: 3px;
  border: 1px solid #e2e8f0;
}
.btn-seg {
  padding: 6px 14px;
  font-size: 12.5px;
  font-weight: 600;
  color: #64748b;
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.15s ease;
}
.btn-seg:hover {
  color: #0f172a;
}
.btn-seg.active {
  background: #ffffff;
  color: #0f172a;
  box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}
.analytics-date-form {
  display: inline-flex;
  align-items: center;
}
.date-input-wrap {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #ffffff;
  padding: 3px 8px;
  border: 1px solid #cbd5e1;
  border-radius: 8px;
}
.date-picker-sm {
  border: none !important;
  padding: 4px 6px !important;
  font-size: 12px !important;
  background: transparent !important;
  outline: none !important;
  color: #334155;
}
.date-sep {
  color: #94a3b8;
  font-size: 12px;
}
.analytics-metric-title {
  font-size: 13.5px;
  font-weight: 600;
  color: #475569;
  border-bottom: 1px dotted #94a3b8;
  cursor: help;
  display: inline-block;
  margin-bottom: 6px;
}
.analytics-big-metric {
  font-size: 1.85rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -0.5px;
}
.chart-container {
  width: 100%;
  position: relative;
}
.analytics-svg-chart {
  overflow: visible;
}
.chart-tooltip {
  position: absolute;
  pointer-events: none;
  background: #0f172a;
  color: #ffffff;
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0,0,0,0.18);
  white-space: nowrap;
  transition: opacity 0.12s ease;
  z-index: 10;
  transform: translate(-50%, -120%);
}
.chart-legend-bottom {
  text-align: center;
  margin-top: 14px;
}
.chart-legend-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  color: #334155;
}
.legend-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
}
/* Funnel Styles */
.funnel-steps-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  border-bottom: 1px solid #f1f5f9;
  padding-bottom: 18px;
  margin-bottom: 18px;
}
.funnel-step-item {
  border-right: 1px solid #f1f5f9;
  padding-right: 12px;
}
.funnel-step-item:last-child {
  border-right: none;
}
.funnel-step-header {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  margin-bottom: 8px;
}
.funnel-step-numbers {
  display: flex;
  align-items: baseline;
  gap: 8px;
}
.funnel-pct {
  font-size: 1.45rem;
  font-weight: 800;
  color: #0f172a;
}
.funnel-count {
  font-size: 13px;
  font-weight: 500;
  color: #64748b;
}
.funnel-visual-wrap {
  display: flex;
  align-items: flex-end;
  height: 54px;
  gap: 4px;
  background: #f8fafc;
  border-radius: 8px;
  padding: 8px;
}
.funnel-visual-bar {
  height: 100%;
  border-radius: 4px;
  transition: all 0.3s ease;
}
.funnel-visual-bar.b-1 { background: #2563eb; }
.funnel-visual-bar.b-2 { background: #3b82f6; }
.funnel-visual-bar.b-3 { background: #60a5fa; }
.funnel-visual-bar.b-4 { background: #10b981; }
.funnel-connector {
  width: 8px;
  height: 100%;
  background: linear-gradient(to right, rgba(0,0,0,0.05), transparent);
}
/* Bar list */
.analytics-bar-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.analytics-bar-item {
  width: 100%;
}
.bar-header {
  display: flex;
  justify-content: space-between;
  font-size: 12.5px;
  margin-bottom: 5px;
}
.bar-name {
  color: #334155;
  font-weight: 500;
}
.bar-track {
  width: 100%;
  height: 7px;
  background: #f1f5f9;
  border-radius: 10px;
  overflow: hidden;
}
.bar-fill {
  height: 100%;
  border-radius: 10px;
  transition: width 0.4s ease;
}
.country-flag {
  font-size: 15px;
}
.admin-grid-2 {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}
.admin-grid-3 {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}
@media (max-width: 1024px) {
  .admin-grid-2, .admin-grid-3 {
    grid-template-columns: 1fr;
  }
  .funnel-steps-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 640px) {
  .funnel-steps-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<?php
include dirname(__DIR__) . '/includes/footer.php';
?>

