<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Transparency Dashboard · Prabhu Insurance Limited</title>
<meta name="description" content="Public disclosure and monthly reports for Prabhu Insurance Limited — premiums, claims, solvency, network, and grievance data." />
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>
tailwind.config = {
  theme: {
    extend: {
      fontFamily: { sans: ['Inter','ui-sans-serif','system-ui','sans-serif'] },
      colors: {
        brand:       { DEFAULT:'#b3261e', deep:'#7a1a15', soft:'#fbecea' },
        ink:         '#241612',
        mute:        '#6b5b57',
        line:        '#ecdfdc',
        surface:     '#ffffff',
        bg:          '#fbf7f5',
        secondary:   '#f3ebe9',
        success:     '#1f8a5a',
        accent:      '#e0a458',
      }
    }
  }
}
</script>
<style>
  :root{
    --c1:#b3261e; --c2:#e0733a; --c3:#e0a458; --c4:#3d7fa8; --c5:#7d5aa8; --c6:#7a1a15;
  }
  html,body{background:#fbf7f5;color:#241612;font-family:Inter,system-ui,sans-serif;}
  .tabular-nums{font-variant-numeric:tabular-nums;}
  .brand-gradient{background:linear-gradient(135deg,#b3261e,#7a1a15);}
  .card-shadow{box-shadow:0 1px 2px rgba(36,22,18,.04),0 8px 24px -12px rgba(36,22,18,.08);}
  table{border-collapse:separate;border-spacing:0;}
  .kpi-blur{filter:blur(24px);opacity:.4;}
</style>
</head>
<body class="min-h-screen">

<!-- Banner -->
<div class="brand-gradient text-white">
  <div class="mx-auto max-w-7xl flex flex-col gap-4 px-6 py-6 md:flex-row md:items-center md:justify-between">
    <div class="flex items-center gap-4">
      <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-white/95 p-2 shadow-lg ring-1 ring-white/40">
        <img src="{{ asset('images/logo.png') }}" alt="Prabhu Insurance Limited" class="h-full w-full object-contain">
      </div>
      <div class="min-w-0">
        <h1 class="truncate text-xl font-semibold sm:text-2xl">Transparency Dashboard · Prabhu Insurance Limited</h1>
        <p class="text-xs text-white/70">Fiscal Year <span id="bannerFiscalYear">{{ $latestFinancialFiscalYear ?? ($fiscalYears[0] ?? 'N/A') }}</span> (YTD) · Last updated <span id="today"></span></p>
      </div>
    </div>
    <div class="flex flex-wrap items-center gap-2 text-xs">
      <a href="https://prabhuinsurance.com" target="_blank" rel="noopener noreferrer" class="rounded-full bg-white/15 px-3 py-1.5 ring-1 ring-white/20 backdrop-blur hover:bg-white/25 transition-colors">Visit Homepage</a>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="border-b border-line bg-brand-soft/60">
  <div class="mx-auto max-w-7xl px-6 py-4">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
      <div class="flex items-center gap-2 text-xs font-semibold tracking-wide text-brand-deep uppercase">
        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
        Filter Reports
      </div>
      <div class="grid flex-1 grid-cols-2 gap-3 md:grid-cols-4 lg:max-w-4xl">
        <label class="flex flex-col gap-1">
          <span class="text-[10px] font-semibold tracking-wide text-mute uppercase">Fiscal Year</span>
          <select id="fiscalYearSel" class="h-9 rounded-md border border-line bg-white px-2.5 text-sm font-medium shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
            @foreach ($fiscalYears as $fiscalYear)
              <option value="{{ $fiscalYear }}" @selected($latestFinancialFiscalYear === $fiscalYear)>{{ $fiscalYear }}</option>
            @endforeach
          </select>
        </label>
        <label class="flex flex-col gap-1">
          <span class="text-[10px] font-semibold tracking-wide text-mute uppercase">Fiscal Month</span>
          <select id="monthSel" class="h-9 rounded-md border border-line bg-white px-2.5 text-sm font-medium shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
            <option value="">All</option>
            @foreach ($months as $monthValue => $monthName)
              <option value="{{ $monthValue }}">{{ $monthName }}</option>
            @endforeach
          </select>
        </label>
        <label class="flex flex-col gap-1">
          <span class="text-[10px] font-semibold tracking-wide text-mute uppercase">Province</span>
          <select id="provinceSel" class="h-9 rounded-md border border-line bg-white px-2.5 text-sm font-medium shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
            <option value="">All</option>
            @foreach ($provinces as $province)
              <option value="{{ $province }}">{{ $province }}</option>
            @endforeach
          </select>
        </label>
        <label class="flex flex-col gap-1">
          <span class="text-[10px] font-semibold tracking-wide text-mute uppercase">District</span>
          <select id="districtSel" disabled class="h-9 rounded-md border border-line bg-white px-2.5 text-sm font-medium shadow-sm focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20 disabled:opacity-50 disabled:cursor-not-allowed">
            <option value="">All</option>
          </select>
        </label>
      </div>
      <div class="flex gap-2 self-start lg:self-end">
        <button onclick="applyFilters()" class="rounded-md bg-brand px-3 py-2 text-xs font-medium text-white hover:bg-brand-deep transition-colors">Filter</button>
        <button onclick="resetFilters()" class="rounded-md border border-line bg-white px-3 py-2 text-xs font-medium text-mute hover:border-brand hover:text-brand transition-colors">Reset</button>
      </div>
    </div>
    <div id="filterState" class="mt-3 text-xs text-mute"></div>
  </div>
</div>

<main class="mx-auto max-w-7xl space-y-6 px-6 py-8">

  <!-- KPIs -->
  <section class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
    <!-- kpi -->
    <div class="relative overflow-hidden rounded-2xl border border-line bg-white p-5 card-shadow">
      <div class="flex items-start">
        <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand text-white">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"/><path d="M4 6v12a2 2 0 0 0 2 2h14v-4"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg>
        </span>
      </div>
      <p class="mt-4 text-[11px] font-medium tracking-wide text-mute uppercase">Premium Collected</p>
      <p class="mt-1 text-2xl font-semibold tracking-tight tabular-nums">99,441,499.02</p>
      <p class="mt-0.5 text-xs text-mute">NPR</p>
      <span class="pointer-events-none absolute -right-8 -bottom-8 h-24 w-24 rounded-full bg-brand-soft kpi-blur"></span>
    </div>
    <div class="relative overflow-hidden rounded-2xl border border-line bg-white p-5 card-shadow">
      <div class="flex items-start">
        <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-soft text-brand">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
        </span>
      </div>
      <p class="mt-4 text-[11px] font-medium tracking-wide text-mute uppercase">Claims Paid</p>
      <p class="mt-1 text-2xl font-semibold tracking-tight tabular-nums">19,219,809.30</p>
      <p class="mt-0.5 text-xs text-mute">NPR</p>
    </div>
    <div class="relative overflow-hidden rounded-2xl border border-line bg-white p-5 card-shadow">
      <div class="flex items-start">
        <span class="grid h-9 w-9 place-items-center rounded-lg bg-success/10 text-success">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </span>
      </div>
      <p class="mt-4 text-[11px] font-medium tracking-wide text-mute uppercase">Solvency Ratio</p>
      <p id="publicSolvencyValue" class="mt-1 text-2xl font-semibold tracking-tight tabular-nums">—</p>
      <p id="publicSolvencyPeriod" class="mt-0.5 text-xs text-mute">Latest reported quarter</p>
    </div>
    <div class="relative overflow-hidden rounded-2xl border border-line bg-white p-5 card-shadow">
      <div class="flex items-start">
        <span class="grid h-9 w-9 place-items-center rounded-lg bg-accent/25 text-brand-deep">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
        </span>
      </div>
      <p class="mt-4 text-[11px] font-medium tracking-wide text-mute uppercase">Premium Growth</p>
      <p class="mt-1 text-2xl font-semibold tracking-tight tabular-nums">8.4%</p>
      <p class="mt-0.5 text-xs text-mute">vs. FY 2081-82</p>
    </div>
    <div class="relative overflow-hidden rounded-2xl border border-line bg-white p-5 card-shadow">
      <div class="flex items-start">
        <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-soft text-brand">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="14" rx="1"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        </span>
      </div>
      <p class="mt-4 text-[11px] font-medium tracking-wide text-mute uppercase">Network Branches</p>
      <p class="mt-1 text-2xl font-semibold tracking-tight tabular-nums">57</p>
      <p class="mt-0.5 text-xs text-mute">across 7 provinces</p>
    </div>
  </section>

  <!-- Row: Claims Overview + Gross Premium Pie -->
  <div class="grid gap-6 lg:grid-cols-3">
    <section class="lg:col-span-2 overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md bg-brand text-white text-xs">✓</span>
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Claims Overview</h2>
      </header>
      <div class="p-5 overflow-x-auto" id="claimsTable"></div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md bg-brand text-white text-xs">%</span>
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Gross Premium Collection</h2>
      </header>
      <div class="p-5">
        <div class="h-64 relative"><canvas id="premiumPie"></canvas></div>
        <ul class="mt-3 grid grid-cols-2 gap-2 text-xs" id="premiumLegend"></ul>
      </div>
    </section>
  </div>

  <!-- Policy & Premium -->
  <section class="overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md bg-brand text-white text-xs">रू</span>
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Policy &amp; Premium</h2>
      </header>
      <div class="p-5 overflow-x-auto" id="policyTable"></div>
  </section>

  <!-- Portfoliowise claims -->
  <section class="overflow-hidden rounded-2xl border border-line bg-white card-shadow">
    <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
      <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md bg-brand text-white text-xs">≡</span>
      <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Portfoliowise Claim Details</h2>
    </header>
    <div class="p-5 grid gap-6 lg:grid-cols-5">
      <div class="lg:col-span-3 overflow-x-auto" id="portfolioTable"></div>
      <div class="lg:col-span-2">
        <p class="mb-2 text-xs font-medium tracking-wide text-mute uppercase">Amount Paid by Portfolio (NPR)</p>
        <div class="h-72 relative"><canvas id="portfolioBar"></canvas></div>
      </div>
    </div>
  </section>

  <!-- Outstanding Claims -->
  <div class="grid gap-6 lg:grid-cols-2">
    <section class="overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center justify-between gap-3 border-b border-line bg-brand-soft px-5 py-3">
        <div class="flex items-center gap-2.5">
          <span class="grid h-7 w-7 place-items-center rounded-md bg-brand text-white text-xs">⏱</span>
          <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Outstanding Claims — Number</h2>
        </div>
        <span id="outCountPeriod" class="text-right text-xs font-semibold text-brand-deep"></span>
      </header>
      <div class="p-5 overflow-x-auto" id="outCountTable"></div>
    </section>
    <section class="overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center justify-between gap-3 border-b border-line bg-brand-soft px-5 py-3">
        <div class="flex items-center gap-2.5">
          <span class="grid h-7 w-7 place-items-center rounded-md bg-brand text-white text-xs">₨</span>
          <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Outstanding Claims — Amount (NPR)</h2>
        </div>
        <span id="outAmountPeriod" class="text-right text-xs font-semibold text-brand-deep"></span>
      </header>
      <div class="p-5 overflow-x-auto" id="outAmtTable"></div>
    </section>
  </div>

  <!-- Network + Branches -->
  <div class="grid gap-6 lg:grid-cols-3">
    <section class="overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
        <span class="grid h-7 w-7 place-items-center rounded-md bg-brand text-white text-xs">◉</span>
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Network Information</h2>
      </header>
      <dl class="p-5 space-y-4" id="networkList"></dl>
    </section>

    <section class="lg:col-span-2 overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
        <span class="grid h-7 w-7 place-items-center rounded-md bg-brand text-white text-xs">▤</span>
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Branch Distribution by Province</h2>
      </header>
      <ul class="p-5 space-y-3" id="branchList"></ul>
    </section>
  </div>

  <!-- Grievance overview and reasons -->
  <div class="grid gap-6 lg:grid-cols-3">
    <section class="lg:col-span-2 overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
        <span class="grid h-7 w-7 place-items-center rounded-md bg-brand text-white text-xs">!</span>
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Grievance Redressal</h2>
      </header>
      <div class="p-5 overflow-x-auto" id="grievanceTable"></div>
    </section>
    <section class="overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
        <span class="grid h-7 w-7 place-items-center rounded-md bg-brand text-white text-xs">✎</span>
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Grievance Reasons</h2>
      </header>
      <ul class="p-5 space-y-3" id="complaintList"></ul>
    </section>
  </div>

  <!-- Financial highlights -->
  <section class="overflow-hidden rounded-2xl border border-line bg-white card-shadow">
    <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
      <div class="flex items-center gap-2.5">
        <span class="grid h-7 w-7 place-items-center rounded-md bg-brand text-white text-xs">↗</span>
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Financial Highlights</h2>
      </div>
    </header>
    <div class="p-5">
      <div id="financialHighlightsTable" class="overflow-x-auto"></div>
      <p class="mt-4 flex items-center gap-1.5 text-xs text-mute">
        <svg class="h-3.5 w-3.5 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 11v5"/><path d="M12 8h.01"/></svg>
        Financial highlights follow the fiscal year and month filters only; province and district filters do not apply.
      </p>
    </div>
  </section>

  <footer class="mt-4 flex flex-col items-start justify-between gap-3 border-t border-line pt-6 text-xs text-mute sm:flex-row sm:items-center">
    <p>© <span id="year"></span> Prabhu Insurance Limited.</p>
    <p>Prepared in accordance with Nepal Insurance Authority disclosure guidelines.</p>
  </footer>
</main>

<script>
const years = ["2082-83","2081-82","2080-81","2079-80","2078-79"];
const npr = n => n===0?"—":new Intl.NumberFormat("en-IN").format(n);
document.getElementById('today').textContent = new Date().toLocaleDateString("en-GB",{day:"numeric",month:"short",year:"numeric"});
document.getElementById('year').textContent = new Date().getFullYear();

const PROV = @json($districtsByProvince);
const fiscalYearSelect = document.getElementById('fiscalYearSel');
const monthSelect = document.getElementById('monthSel');
const provinceSelect = document.getElementById('provinceSel');
const districtSelect = document.getElementById('districtSel');
const financialHighlights = @json($financialHighlights);
const grievanceReports = @json($grievanceReports);
const outstandingClaims = @json($outstandingClaims);
const portfolioClaimRows = @json($portfolioClaimRows);
const monthNames = @json($months);
const latestFinancialFiscalYear = @json($latestFinancialFiscalYear);
const latestFinancialQuarter = Number(@json($latestFinancialQuarter));
function refreshDistricts(){
  const province = provinceSelect.value;
  districtSelect.replaceChildren(new Option('All', ''));
  (PROV[province] || []).forEach(name => districtSelect.add(new Option(name, name)));
  districtSelect.disabled = !province;
  updateBranchNetwork();
  updateOutstandingClaims();
  updatePortfolioClaims();
}
function resetFilters(){
  document.querySelectorAll('select').forEach(s => { s.selectedIndex = 0; });
  if (latestFinancialFiscalYear) fiscalYearSelect.value = latestFinancialFiscalYear;
  refreshDistricts();
  updateBranchNetwork();
  updateFinancialHighlights();
  updateGrievances();
  updateOutstandingClaims();
  updatePortfolioClaims();
}
function applyFilters(){
  fiscalYearSelect.dispatchEvent(new Event('change'));
  monthSelect.dispatchEvent(new Event('change'));
  provinceSelect.dispatchEvent(new Event('change'));
  districtSelect.dispatchEvent(new Event('change'));
  updateBranchNetwork();
  updateFinancialHighlights();
  updateGrievances();
  updateOutstandingClaims();
}
provinceSelect.addEventListener('change', refreshDistricts);
fiscalYearSelect.addEventListener('change', updateBranchNetwork);
monthSelect.addEventListener('change', updateBranchNetwork);
fiscalYearSelect.addEventListener('change', updateFinancialHighlights);
monthSelect.addEventListener('change', updateFinancialHighlights);
fiscalYearSelect.addEventListener('change', updateGrievances);
monthSelect.addEventListener('change', updateGrievances);
fiscalYearSelect.addEventListener('change', updateOutstandingClaims);
monthSelect.addEventListener('change', updateOutstandingClaims);
provinceSelect.addEventListener('change', updateOutstandingClaims);
districtSelect.addEventListener('change', updateOutstandingClaims);
fiscalYearSelect.addEventListener('change', updatePortfolioClaims);
monthSelect.addEventListener('change', updatePortfolioClaims);
provinceSelect.addEventListener('change', updatePortfolioClaims);
districtSelect.addEventListener('change', updatePortfolioClaims);
districtSelect.addEventListener('change', updateBranchNetwork);

function renderTable(elId, firstCol, head, rows){
  const el=document.getElementById(elId);
  const th = `<tr class="bg-secondary text-ink">
    <th class="rounded-l-lg px-3 py-2.5 text-left text-sm font-medium">${firstCol}</th>
    ${head.map((h,i)=>`<th class="px-3 py-2.5 text-right text-sm font-medium ${i===head.length-1?'rounded-r-lg':''}">${h}</th>`).join('')}
  </tr>`;
  const tr = rows.map(r=>`<tr class="border-b border-line/60 last:border-b-0 hover:bg-brand-soft/60">
    ${r.map((c,i)=>`<td class="px-3 py-2.5 text-sm ${i===0?'text-left font-medium text-ink':'text-right tabular-nums text-mute'}">${c}</td>`).join('')}
  </tr>`).join('');
  el.innerHTML = `<table class="w-full min-w-[520px] text-sm"><thead>${th}</thead><tbody>${tr}</tbody></table>`;
}

renderTable('claimsTable','Metric',years,[
  ["Number of Claims Paid",635,612,588,540,495],
  ["Gross Claims Paid (NPR)","19,219,809","17,845,220","16,102,910","14,988,301","13,540,110"],
  ["Withdrawal Claims",59,52,44,39,33],
  ["Outstanding Claim Ratio (%)","43%","45%","48%","51%","54%"],
  ["Claim Settlement Ratio (%)","57%","55%","52%","49%","46%"],
  ["Claim Turnaround (days)",41,46,51,58,64],
]);

renderTable('policyTable','Metric',years,[
  ["Total Policies","48,210","44,905","41,220","38,140","35,010"],
  ["New Policies","9,120","8,204","7,510","7,015","6,412"],
  ["Renewal Policies","37,190","35,110","32,090","29,455","27,010"],
  ["Endorsed Policies","1,900","1,591","1,620","1,670","1,588"],
  ["Gross Premium (NPR)","99,441,499","91,720,110","84,015,220","77,120,540","70,880,110"],
  ["Premium Growth Rate (%)","8.4%","9.2%","8.9%","8.8%","—"],
  ["Loss Ratio (%)","19.3%","19.5%","19.2%","19.4%","19.1%"],
]);

let portfolioBarChart;

function latestPortfolioMonth(rows){
  return rows.reduce((latest, row) => fiscalMonthOrder(row.month) > fiscalMonthOrder(latest) ? Number(row.month) : latest, null);
}

function updatePortfolioClaims(){
  const fiscalYear = fiscalYearSelect.value;
  const province = provinceSelect.value;
  const district = districtSelect.value;
  const selectedMonth = monthSelect.value ? Number(monthSelect.value) : null;
  const fiscalYearRows = portfolioClaimRows.filter(row => row.fiscal_year === fiscalYear);
  // With a month filter, only files that actually contain that month qualify.
  // The highest import id is the latest uploaded qualifying file.
  const eligibleIntimations = fiscalYearRows.filter(row => row.type === 'intimation'
    && (!selectedMonth || Number(row.month) === selectedMonth));
  const latestIntimationImportId = eligibleIntimations.reduce((latest, row) => Math.max(latest, Number(row.import_id || 0)), 0);
  const scoped = fiscalYearRows.filter(row => (!province || row.province === province)
    && (!district || row.district === district));
  const selectedIntimations = scoped.filter(row => row.type === 'intimation'
    && Number(row.import_id) === latestIntimationImportId
    && (!selectedMonth || Number(row.month) === selectedMonth));
  const selectedIntimationMonths = new Set(selectedIntimations.map(row => Number(row.month)));
  const grouped = new Map();

  scoped.filter(row => {
    if (row.type === 'intimation') return Number(row.import_id) === latestIntimationImportId
      && (!selectedMonth || Number(row.month) === selectedMonth);
    // Excel uses the paid-claim rows from the exact selected month. With no month
    // filter, use the months present in the selected latest intimation file.
    if (row.type === 'paid') return selectedIntimationMonths.has(Number(row.month));
    return false;
  }).forEach(row => {
    if (!grouped.has(row.portfolio)) grouped.set(row.portfolio, {totalRows:0, outstanding:0, paid:0, withdrawal:0, paidClaimCount:0, amount:0, tat:0, tatCount:0});
    const item = grouped.get(row.portfolio);
    if (row.type === 'intimation') {
      // Every row in the chosen file/month is part of the denominator, regardless of status.
      item.totalRows += Number(row.count || 0);
      if (['outstanding', 'paid', 'withdrawal'].includes(row.status)) item[row.status] += Number(row.count || 0);
    } else if (row.type === 'paid') {
      item.paidClaimCount += Number(row.count || 0);
      item.amount += Number(row.amount || 0);
      item.tat += Number(row.turnaround_days || 0);
      item.tatCount += Number(row.count || 0);
    }
  });

  const portfolio = [...grouped.entries()].map(([name, item]) => ({
    p: name,
    c: item.paidClaimCount,
    a: item.amount,
    o: item.totalRows ? (item.outstanding / item.totalRows) * 100 : 0,
    s: item.totalRows ? 100 - ((item.outstanding / item.totalRows) * 100) : 0,
    t: item.tatCount ? item.tat / item.tatCount : 0,
  })).sort((left, right) => left.p.localeCompare(right.p));

  const percent = value => `${new Intl.NumberFormat('en-IN', {maximumFractionDigits:2}).format(value)}%`;
  renderTable('portfolioTable', 'Portfolio',
    ['Claims Paid', 'Amount (NPR)', 'Outstanding %', 'Settlement %', 'TAT (days)'],
    portfolio.length ? portfolio.map(row => [row.p, row.c, npr(row.a), percent(row.o), percent(row.s), Math.round(row.t)])
      : [['No claim data for this period', '—', '—', '—', '—', '—']]);

  if (portfolioBarChart) portfolioBarChart.destroy();
  const barData = portfolio.filter(row => row.a > 0);
  portfolioBarChart = new Chart(document.getElementById('portfolioBar'), {
    type:'bar',
    data:{labels:barData.map(row=>row.p),datasets:[{label:'Amount (NPR)',data:barData.map(row=>row.a),backgroundColor:barData.map((_,i)=>palette[i%palette.length]),borderRadius:6}]},
    options:{indexAxis:'y',maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{ticks:{callback:value=>(value/1e6).toFixed(1)+'M',font:{size:11}},grid:{color:'#f1e5e2'}},y:{ticks:{font:{size:11}},grid:{display:false}}}}
  });
}

function updateOutstandingClaims(){
  const fiscalYear = fiscalYearSelect.value;
  const requestedMonth = monthSelect.value ? Number(monthSelect.value) : null;
  const province = provinceSelect.value;
  const district = districtSelect.value;
  const buckets = ['lt_1', 'yr_1_3', 'yr_3_5', 'yr_5_plus'];
  const grouped = new Map();
  const fiscalYearClaims = outstandingClaims.filter(claim => {
    if (claim.fiscal_year !== fiscalYear) return false;
    if (province && claim.province !== province) return false;
    if (district && claim.district !== district) return false;
    return true;
  });
  const availableMonths = [...new Set(fiscalYearClaims.map(claim => Number(claim.month)).filter(Boolean))];
  const requestedMonthAvailable = requestedMonth && availableMonths.includes(requestedMonth);
  const latestAvailableMonth = availableMonths.sort((left, right) => fiscalMonthOrder(right) - fiscalMonthOrder(left))[0] || null;
  const displayedMonth = requestedMonthAvailable ? requestedMonth : latestAvailableMonth;

  fiscalYearClaims
    .filter(claim => displayedMonth && Number(claim.month) === displayedMonth)
    .forEach(claim => {
      if (!grouped.has(claim.portfolio)) {
        grouped.set(claim.portfolio, Object.fromEntries(buckets.map(bucket => [bucket, {count: 0, amount: 0}])));
      }
      const values = grouped.get(claim.portfolio);
      const bucket = values[claim.bucket] || values.yr_5_plus;
      bucket.count += 1;
      bucket.amount += Number(claim.amount || 0);
    });

  const entries = [...grouped.entries()].sort(([left], [right]) => left.localeCompare(right));
  const countRows = entries.map(([portfolio, values]) => [
    portfolio,
    ...buckets.map(bucket => values[bucket].count),
    buckets.reduce((total, bucket) => total + values[bucket].count, 0),
  ]);
  const amountRows = entries.map(([portfolio, values]) => [
    portfolio,
    ...buckets.map(bucket => npr(values[bucket].amount)),
    npr(buckets.reduce((total, bucket) => total + values[bucket].amount, 0)),
  ]);
  const emptyRow = [['No outstanding claims for this period', '—', '—', '—', '—', '—']];
  const usedFallback = requestedMonth && displayedMonth && requestedMonth !== displayedMonth;
  const period = `${fiscalYear}${displayedMonth ? `<span class="block font-medium">(${monthNames[displayedMonth]})</span>` : ''}`;

  document.getElementById('outCountPeriod').innerHTML = period;
  document.getElementById('outAmountPeriod').innerHTML = period;
  document.getElementById('outCountPeriod').title = usedFallback ? 'Selected month has no data; showing latest available month.' : '';
  document.getElementById('outAmountPeriod').title = usedFallback ? 'Selected month has no data; showing latest available month.' : '';
  renderTable('outCountTable', 'Portfolio', ['< 1 yr', '1–3 yr', '3–5 yr', '5+ yr', 'Total'], countRows.length ? countRows : emptyRow);
  renderTable('outAmtTable', 'Portfolio', ['< 1 yr', '1–3 yr', '3–5 yr', '5+ yr', 'Total'], amountRows.length ? amountRows : emptyRow);
}

updateOutstandingClaims();

function fiscalMonthOrder(month){
  const order = {4:1,5:2,6:3,7:4,8:5,9:6,10:7,11:8,12:9,1:10,2:11,3:12};
  return order[Number(month)] || 0;
}

function grievanceReportFor(fiscalYear, requestedMonth = null){
  const reports = grievanceReports.filter(report => report.fiscal_year === fiscalYear);
  if (requestedMonth) return reports.find(report => Number(report.month) === Number(requestedMonth));
  return reports.sort((a, b) => fiscalMonthOrder(b.month) - fiscalMonthOrder(a.month))[0];
}

function updateGrievances(){
  const selectedFiscalYear = fiscalYearSelect.value;
  const selectedMonth = monthSelect.value ? Number(monthSelect.value) : null;
  const fiscalYears = fiveFiscalYearsFrom(selectedFiscalYear);
  const reports = fiscalYears.map(year => grievanceReportFor(year, selectedMonth));
  const value = (report, key) => report ? Number(report[key] || 0) : null;
  const pending = report => report ? Math.max(0, value(report, 'received') - value(report, 'resolved')) : null;
  const rate = report => report && value(report, 'received') > 0
    ? (value(report, 'resolved') / value(report, 'received')) * 100
    : (report ? 0 : null);
  const display = (number, suffix = '') => number === null || number === undefined
    ? '-'
    : `${new Intl.NumberFormat('en-IN', {maximumFractionDigits:2}).format(number)}${suffix}`;

  renderTable('grievanceTable', 'Metric', fiscalYears, [
    ['Grievances Received', ...reports.map(report => display(value(report, 'received')))],
    ['Grievances Resolved', ...reports.map(report => display(value(report, 'resolved')))],
    ['Grievances Pending', ...reports.map(report => display(pending(report)))],
    ['Resolution Rate (%)', ...reports.map(report => display(rate(report), '%'))],
    ['Avg. Resolution Time (days)', ...reports.map(report => display(report?.average_resolution_time === null || report?.average_resolution_time === undefined ? null : Number(report.average_resolution_time)))],
  ]);

  const currentReport = grievanceReportFor(selectedFiscalYear, selectedMonth);
  const reasons = currentReport ? Object.entries(currentReport.reasons || {}) : [];
  const totalReasons = reasons.reduce((total, [, count]) => total + Number(count || 0), 0);
  const colors = ['var(--c1)','var(--c2)','var(--c3)','var(--c4)','var(--c5)','var(--c6)'];
  const escapeHtml = value => String(value).replace(/[&<>'"]/g, character => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[character]));
  document.getElementById('complaintList').innerHTML = reasons.length
    ? reasons.map(([reason, count], index) => {
        const percentage = totalReasons > 0 ? (Number(count) / totalReasons) * 100 : 0;
        return `<li>
          <div class="flex items-baseline justify-between text-sm">
            <span class="truncate">${escapeHtml(reason)}</span><span class="tabular-nums text-mute">${count} · ${percentage.toFixed(1)}%</span>
          </div>
          <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-secondary">
            <div class="h-full rounded-full" style="width:${Math.min(percentage,100)}%;background:${colors[index % colors.length]}"></div>
          </div>
        </li>`;
      }).join('')
    : '<li class="rounded-xl border border-line/70 bg-brand-soft/40 p-3 text-sm text-mute">No grievance data for this reporting period.</li>';
}

function fiscalQuarterFromMonth(month){
  const value = Number(month);
  if ([4,5,6].includes(value)) return 1; // Shrawan, Bhadra, Ashwin
  if ([7,8,9].includes(value)) return 2; // Kartik, Mangsir, Poush
  if ([10,11,12].includes(value)) return 3; // Magh, Falgun, Chaitra
  if ([1,2,3].includes(value)) return 4; // Baisakh, Jestha, Asar
  return null;
}

function financialValue(value, suffix = ''){
  if (value === null || value === undefined || value === '') return '—';
  return `${new Intl.NumberFormat('en-IN', {maximumFractionDigits: 4}).format(Number(value))}${suffix}`;
}

function updateFinancialHighlights(){
  const fiscalYear = fiscalYearSelect.value;
  const selectedQuarter = fiscalQuarterFromMonth(monthSelect.value) || latestFinancialQuarter || 1;
  let row = financialHighlights.find(item => item.fiscal_year === fiscalYear && (!selectedQuarter || Number(item.quarter) === selectedQuarter));

  if (!row) {
    document.getElementById('publicSolvencyValue').textContent = '-';
    document.getElementById('publicSolvencyPeriod').textContent = `FY ${fiscalYear} · Q${selectedQuarter}`;
    document.getElementById('bannerFiscalYear').textContent = fiscalYear;
  } else {
    document.getElementById('publicSolvencyValue').textContent = financialValue(row.solvency_ratio, 'x');
    document.getElementById('publicSolvencyPeriod').textContent = `FY ${row.fiscal_year} · Q${row.quarter}`;
    document.getElementById('bannerFiscalYear').textContent = row.fiscal_year;
  }

  renderFinancialHighlightsTable(fiscalYear, selectedQuarter);
}

function fiveFiscalYearsFrom(fiscalYear){
  const match = String(fiscalYear || '').match(/(\d{4})\D+(\d{2,4})/);
  if (!match) return [fiscalYear];

  const start = Number(match[1]);
  return Array.from({length: 5}, (_, index) => {
    const yearStart = start - index;
    return `${yearStart}-${String(yearStart + 1).slice(-2)}`;
  });
}

function renderFinancialHighlightsTable(selectedFiscalYear, quarter){
  const years = fiveFiscalYearsFrom(selectedFiscalYear);
  const records = new Map(
    financialHighlights
      .filter(item => Number(item.quarter) === Number(quarter))
      .map(item => [item.fiscal_year, item])
  );
  const metrics = [
    ['Solvency Ratio (x)', 'solvency_ratio', 'x'],
    ['Return on Equity (%)', 'return_on_equity', '%'],
    ['Earnings per Share (NPR)', 'earnings_per_share', ''],
    ['Net Worth (NPR)', 'net_worth', ''],
    ['Net Profit Margin (%)', 'net_profit_margin', '%'],
    ['Liquidity Ratio', 'liquidity_ratio', ''],
    ['Investment Yield (%)', 'investment_yield', '%'],
  ];
  const headings = years.map((year, index) => `
    <th class="min-w-[120px] px-3 py-2.5 text-right text-sm font-medium ${index === 0 ? 'bg-brand-soft text-brand-deep' : ''}">${year}</th>`).join('');
  const rows = metrics.map(([label, key, suffix]) => `
    <tr class="border-b border-line/60 last:border-b-0 hover:bg-brand-soft/40">
      <td class="sticky left-0 z-10 min-w-[230px] bg-white px-3 py-2.5 text-sm font-medium text-ink">${label}</td>
      ${years.map((year, index) => {
        const value = records.get(year)?.[key];
        const display = value === null || value === undefined || value === '' ? '-' : financialValue(value, suffix);
        return `<td class="px-3 py-2.5 text-right text-sm tabular-nums text-mute ${index === 0 ? 'bg-brand-soft/60 font-semibold text-brand-deep' : ''}">${display}</td>`;
      }).join('')}
    </tr>`).join('');

  document.getElementById('financialHighlightsTable').innerHTML = `
    <table class="w-full min-w-[820px] text-sm">
      <thead><tr class="bg-secondary text-ink"><th class="sticky left-0 z-20 min-w-[230px] bg-secondary px-3 py-2.5 text-left text-sm font-medium">Metric</th>${headings}</tr></thead>
      <tbody>${rows}</tbody>
    </table>`;
}

updateFinancialHighlights();

// network
const branchNetworkRows = @json($branchNetworkRows);
const totalProvinceCount = @json($totalProvinceCount);

function fiscalYearStart(fiscalYear){
  const match = String(fiscalYear || '').match(/\d{4}/);
  return match ? Number(match[0]) : null;
}

function periodSerial(fiscalYear, month, fallbackMonth = null){
  const startYear = fiscalYearStart(fiscalYear);
  const selectedMonth = Number(month || fallbackMonth);

  if (!startYear || !selectedMonth) return null;

  const bsYear = selectedMonth >= 4 ? startYear : startYear + 1;
  return (bsYear * 12) + selectedMonth;
}

function selectedPeriodSerial(){
  return periodSerial(fiscalYearSelect.value, monthSelect.value, 3);
}

function branchIsVisible(branch){
  const selectedSerial = selectedPeriodSerial();
  const activeSerial = periodSerial(branch.fiscal_year, branch.month);
  const inactiveSerial = periodSerial(branch.inactive_fiscal_year, branch.inactive_month);

  if (activeSerial && selectedSerial < activeSerial) return false;
  if (branch.status === 'inactive' && !inactiveSerial) return false;
  if (inactiveSerial && selectedSerial >= inactiveSerial) return false;

  return true;
}

function filteredBranches(){
  const province = provinceSelect.value;
  const district = districtSelect.value;

  return branchNetworkRows.filter(branch => {
    if (!branchIsVisible(branch)) return false;
    if (province && branch.province !== province) return false;
    if (district && branch.district !== district) return false;

    return true;
  });
}

function renderNetwork(network){
  document.getElementById('networkList').innerHTML = network.map(([l,v,url])=>`
    <div class="flex items-center gap-3 rounded-xl border border-line/70 bg-brand-soft/40 p-3">
      <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand text-white text-xs">◉</span>
      <div class="min-w-0 flex-1"><dt class="text-xs text-mute">${l}</dt><dd class="text-lg font-semibold tabular-nums">${v}</dd></div>
      ${url ? `<a href="${url}" target="_blank" rel="noopener noreferrer" class="text-xs font-medium text-brand hover:text-brand-deep">View</a>` : ''}
    </div>`).join('');
}

function renderBranches(branches){
  document.getElementById('branchList').innerHTML = branches.length
    ? branches.map(([p,b,s])=>`
      <li class="grid grid-cols-[120px_1fr_60px_60px] items-center gap-3">
        <span class="truncate text-sm font-medium">${p}</span>
        <div class="h-2 overflow-hidden rounded-full bg-secondary">
          <div class="h-full rounded-full" style="width:${s}%;background:linear-gradient(90deg,#b3261e,#7a1a15)"></div>
        </div>
        <span class="text-right text-sm tabular-nums text-mute">${b} br.</span>
        <span class="text-right text-sm font-semibold tabular-nums">${s}%</span>
      </li>`).join('')
    : `<li class="rounded-xl border border-line/70 bg-brand-soft/40 p-3 text-sm text-mute">No branches for the selected period.</li>`;
}

function updateBranchNetwork(){
  const rows = filteredBranches();
  const provinces = [...new Set(rows.map(branch => branch.province).filter(Boolean))];
  const grouped = rows.reduce((acc, branch) => {
    if (!branch.province) return acc;

    acc[branch.province] = (acc[branch.province] || 0) + 1;
    return acc;
  }, {});
  const branches = Object.entries(grouped)
    .sort((a, b) => b[1] - a[1] || a[0].localeCompare(b[0]))
    .map(([province, count]) => [
      province,
      count,
      rows.length > 0 ? Math.round((count / rows.length) * 100) : 0,
    ]);

  renderNetwork([
    ['Total Branch Offices', new Intl.NumberFormat('en-IN').format(rows.length), 'https://prabhuinsurance.com/our-network/'],
    ['Provinces Covered', `${new Intl.NumberFormat('en-IN').format(provinces.length)} / ${new Intl.NumberFormat('en-IN').format(totalProvinceCount)}`],
    ['Licensed Agents', '0', 'https://prabhuinsurance.com/about-us/our-agents/'],
    ['Surveyors', '0', 'https://prabhuinsurance.com/about-us/surveyors-list/'],
  ]);
  renderBranches(branches);

  const monthLabel = monthSelect.value
    ? monthSelect.options[monthSelect.selectedIndex].textContent
    : 'All months';
  const provinceLabel = provinceSelect.value || 'All provinces';
  const districtLabel = districtSelect.value || 'All districts';
  document.getElementById('filterState').textContent =
    `Applied to branch network: FY ${fiscalYearSelect.value}, ${monthLabel}, ${provinceLabel}, ${districtLabel} - ${rows.length} branch(es) visible.`;
}

updateBranchNetwork();

updateGrievances();

// Charts
const palette = ['#b3261e','#e0733a','#e0a458','#3d7fa8','#7d5aa8','#7a1a15'];
const mix = [["Property",32],["Motor",41],["Marine",4],["Engineering",8],["Aviation",6],["Miscellaneous",9]];
document.getElementById('premiumLegend').innerHTML = mix.map(([n,v],i)=>`
  <li class="flex items-center gap-2">
    <span class="h-2 w-2 rounded-full" style="background:${palette[i]}"></span>
    <span class="text-mute">${n}</span><span class="ml-auto font-medium tabular-nums">${v}%</span>
  </li>`).join('');

new Chart(document.getElementById('premiumPie'),{
  type:'doughnut',
  data:{labels:mix.map(m=>m[0]),datasets:[{data:mix.map(m=>m[1]),backgroundColor:palette,borderColor:'#fff',borderWidth:2}]},
  options:{plugins:{legend:{display:false}},cutout:'62%',maintainAspectRatio:false}
});

updatePortfolioClaims();

</script>
</body>
</html>
