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

<!-- Back bar -->
<div class="border-b border-line bg-white">
  <div class="mx-auto max-w-7xl flex items-center justify-between gap-3 px-6 py-2 text-xs">
    <a href="https://prabhuinsurance.com" class="inline-flex items-center gap-1.5 font-medium text-mute hover:text-brand transition-colors">
      <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
      Back to main website
    </a>
    <span class="hidden sm:inline text-mute">prabhuinsurance.com</span>
  </div>
</div>

<!-- Banner -->
<div class="brand-gradient text-white">
  <div class="mx-auto max-w-7xl flex flex-col gap-4 px-6 py-6 md:flex-row md:items-center md:justify-between">
    <div class="flex items-center gap-4">
      <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-white/95 p-2 shadow-lg ring-1 ring-white/40">
        <div class="grid h-full w-full place-items-center rounded-md bg-brand text-white text-lg font-bold">P</div>
      </div>
      <div class="min-w-0">
        <p class="text-xs font-medium tracking-[0.25em] text-white/70 uppercase">Public Disclosure</p>
        <h1 class="truncate text-xl font-semibold sm:text-2xl">Transparency Dashboard · Prabhu Insurance Limited</h1>
        <p class="text-xs text-white/70">Fiscal Year 2082-83 (YTD) · Last updated <span id="today"></span></p>
      </div>
    </div>
    <div class="flex flex-wrap items-center gap-2 text-xs">
      <a href="https://prabhuinsurance.com" class="rounded-full bg-white/15 px-3 py-1.5 ring-1 ring-white/20 backdrop-blur hover:bg-white/25 transition-colors">Visit Homepage</a>
      <span class="rounded-full bg-white/15 px-3 py-1.5 ring-1 ring-white/20 backdrop-blur">Amounts in NPR unless noted</span>
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
              <option value="{{ $fiscalYear }}">{{ $fiscalYear }}</option>
            @endforeach
          </select>
        </label>
        <label class="flex flex-col gap-1">
          <span class="text-[10px] font-semibold tracking-wide text-mute uppercase">Month</span>
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
              <option value="{{ $province->province_name }}">{{ $province->province_name }}</option>
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
      <div class="flex items-start justify-between">
        <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand text-white">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"/><path d="M4 6v12a2 2 0 0 0 2 2h14v-4"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg>
        </span>
        <span class="text-[10px] font-medium tracking-wide text-mute uppercase">+8.4% YoY</span>
      </div>
      <p class="mt-4 text-[11px] font-medium tracking-wide text-mute uppercase">Premium Collected</p>
      <p class="mt-1 text-2xl font-semibold tracking-tight tabular-nums">99,441,499.02</p>
      <p class="mt-0.5 text-xs text-mute">NPR</p>
      <span class="pointer-events-none absolute -right-8 -bottom-8 h-24 w-24 rounded-full bg-brand-soft kpi-blur"></span>
    </div>
    <div class="relative overflow-hidden rounded-2xl border border-line bg-white p-5 card-shadow">
      <div class="flex items-start justify-between">
        <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-soft text-brand">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
        </span>
        <span class="text-[10px] font-medium tracking-wide text-mute uppercase">19.3% loss ratio</span>
      </div>
      <p class="mt-4 text-[11px] font-medium tracking-wide text-mute uppercase">Claims Paid</p>
      <p class="mt-1 text-2xl font-semibold tracking-tight tabular-nums">19,219,809.30</p>
      <p class="mt-0.5 text-xs text-mute">NPR</p>
    </div>
    <div class="relative overflow-hidden rounded-2xl border border-line bg-white p-5 card-shadow">
      <div class="flex items-start justify-between">
        <span class="grid h-9 w-9 place-items-center rounded-lg bg-success/10 text-success">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </span>
        <span class="text-[10px] font-medium tracking-wide text-success uppercase">Healthy</span>
      </div>
      <p class="mt-4 text-[11px] font-medium tracking-wide text-mute uppercase">Solvency Ratio</p>
      <p class="mt-1 text-2xl font-semibold tracking-tight tabular-nums">1.82x</p>
      <p class="mt-0.5 text-xs text-mute">Regulatory ≥ 1.50x</p>
    </div>
    <div class="relative overflow-hidden rounded-2xl border border-line bg-white p-5 card-shadow">
      <div class="flex items-start justify-between">
        <span class="grid h-9 w-9 place-items-center rounded-lg bg-accent/25 text-brand-deep">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
        </span>
        <span class="text-[10px] font-medium tracking-wide text-mute uppercase">Positive</span>
      </div>
      <p class="mt-4 text-[11px] font-medium tracking-wide text-mute uppercase">Premium Growth</p>
      <p class="mt-1 text-2xl font-semibold tracking-tight tabular-nums">8.4%</p>
      <p class="mt-0.5 text-xs text-mute">vs. FY 2081-82</p>
    </div>
    <div class="relative overflow-hidden rounded-2xl border border-line bg-white p-5 card-shadow">
      <div class="flex items-start justify-between">
        <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-soft text-brand">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="14" rx="1"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        </span>
        <span class="text-[10px] font-medium tracking-wide text-mute uppercase">Nationwide</span>
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

  <!-- Row: Policy & Premium + Products -->
  <div class="grid gap-6 lg:grid-cols-3">
    <section class="lg:col-span-2 overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
        <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md bg-brand text-white text-xs">₹</span>
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Policy &amp; Premium</h2>
      </header>
      <div class="p-5 overflow-x-auto" id="policyTable"></div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center justify-between border-b border-line bg-brand-soft px-5 py-3">
        <div class="flex items-center gap-2.5">
          <span class="grid h-7 w-7 shrink-0 place-items-center rounded-md bg-brand text-white text-xs">◎</span>
          <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Our Insurance Products</h2>
        </div>
        <a href="#" class="text-xs font-medium text-brand hover:text-brand-deep">Browse →</a>
      </header>
      <ul class="p-5 grid grid-cols-1 gap-2 sm:grid-cols-2" id="productsList"></ul>
    </section>
  </div>

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
      <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
        <span class="grid h-7 w-7 place-items-center rounded-md bg-brand text-white text-xs">⏱</span>
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Outstanding Claims — Number</h2>
      </header>
      <div class="p-5 overflow-x-auto" id="outCountTable"></div>
    </section>
    <section class="overflow-hidden rounded-2xl border border-line bg-white card-shadow">
      <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
        <span class="grid h-7 w-7 place-items-center rounded-md bg-brand text-white text-xs">₨</span>
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Outstanding Claims — Amount (NPR)</h2>
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

  <!-- Grievance + Complaint reasons -->
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
        <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Complaint Reasons</h2>
      </header>
      <ul class="p-5 space-y-3" id="complaintList"></ul>
    </section>
  </div>

  <!-- Financial trend -->
  <section class="overflow-hidden rounded-2xl border border-line bg-white card-shadow">
    <header class="flex items-center gap-2.5 border-b border-line bg-brand-soft px-5 py-3">
      <span class="grid h-7 w-7 place-items-center rounded-md bg-brand text-white text-xs">↗</span>
      <h2 class="text-sm font-semibold tracking-wide text-brand-deep uppercase">Financial Strength &amp; Solvency — 5-Year Trend</h2>
    </header>
    <div class="p-5 grid gap-6 lg:grid-cols-5">
      <div class="lg:col-span-3 h-72 relative"><canvas id="finLine"></canvas></div>
      <div class="lg:col-span-2 overflow-x-auto" id="finTable"></div>
    </div>
  </section>

  <footer class="mt-4 flex flex-col items-start justify-between gap-3 border-t border-line pt-6 text-xs text-mute sm:flex-row sm:items-center">
    <p>© <span id="year"></span> Prabhu Insurance Limited. All figures are unaudited YTD unless stated otherwise.</p>
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
function refreshDistricts(){
  const province = provinceSelect.value;
  districtSelect.replaceChildren(new Option('All', ''));
  (PROV[province] || []).forEach(name => districtSelect.add(new Option(name, name)));
  districtSelect.disabled = !province;
  updateBranchNetwork();
}
function resetFilters(){
  document.querySelectorAll('select').forEach(s => { s.selectedIndex = 0; });
  refreshDistricts();
  updateBranchNetwork();
}
function applyFilters(){
  fiscalYearSelect.dispatchEvent(new Event('change'));
  monthSelect.dispatchEvent(new Event('change'));
  provinceSelect.dispatchEvent(new Event('change'));
  districtSelect.dispatchEvent(new Event('change'));
  updateBranchNetwork();
}
provinceSelect.addEventListener('change', refreshDistricts);
fiscalYearSelect.addEventListener('change', updateBranchNetwork);
monthSelect.addEventListener('change', updateBranchNetwork);
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

const portfolio = [
  { p:"Property", c:1, a:140855, o:"33%", s:"67%", t:46 },
  { p:"Motor", c:75, a:11696154, o:"46%", s:"54%", t:178 },
  { p:"Marine", c:0, a:0, o:"0%", s:"0%", t:0 },
  { p:"Engineering", c:1, a:29817, o:"40%", s:"60%", t:260 },
  { p:"Aviation", c:0, a:0, o:"0%", s:"100%", t:0 },
  { p:"Agriculture", c:0, a:0, o:"0%", s:"100%", t:0 },
  { p:"Micro", c:0, a:0, o:"0%", s:"100%", t:0 },
  { p:"Miscellaneous", c:618, a:7353183, o:"43%", s:"57%", t:24 },
];
renderTable('portfolioTable','Portfolio',
  ["Claims Paid","Amount (NPR)","Outstanding %","Settlement %","TAT (days)"],
  portfolio.map(r=>[r.p,r.c,npr(r.a),r.o,r.s,r.t]));

renderTable('outCountTable','Portfolio',["< 1 yr","1–3 yr","3–5 yr","5+ yr","Total"],@json($outstandingClaimCounts));
renderTable('outAmtTable','Portfolio',["< 1 yr","1–3 yr","3–5 yr","5+ yr","Total"],@json($outstandingClaimAmounts));

renderTable('grievanceTable','Metric',years,[
  ["Complaints Received",42,51,48,55,60],
  ["Complaints Resolved",39,47,43,49,54],
  ["Complaints Pending",3,4,5,6,6],
  ["Resolution Rate (%)","92.8%","92.1%","89.5%","89.0%","90.0%"],
  ["Avg. Resolution Time (days)",12,14,16,18,21],
]);

const financial = [
  { y:"78-79", solvency:1.42, roe:8.2, eps:9.1, nw:2.11, npm:11.4, liq:1.18, iy:6.4 },
  { y:"79-80", solvency:1.51, roe:9.4, eps:10.6, nw:2.28, npm:12.1, liq:1.22, iy:6.8 },
  { y:"80-81", solvency:1.63, roe:10.7, eps:12.0, nw:2.55, npm:12.9, liq:1.28, iy:7.1 },
  { y:"81-82", solvency:1.74, roe:11.9, eps:13.4, nw:2.81, npm:13.5, liq:1.31, iy:7.4 },
  { y:"82-83", solvency:1.82, roe:12.8, eps:14.6, nw:3.05, npm:14.1, liq:1.35, iy:7.8 },
];
renderTable('finTable','Metric',years,[
  ["Solvency Ratio (x)",...financial.map(f=>f.solvency).reverse()],
  ["Return on Equity (%)",...financial.map(f=>f.roe+'%').reverse()],
  ["EPS (NPR)",...financial.map(f=>f.eps).reverse()],
  ["Net Worth (NPR Cr)",...financial.map(f=>f.nw).reverse()],
  ["Net Profit Margin (%)",...financial.map(f=>f.npm+'%').reverse()],
  ["Liquidity Ratio",...financial.map(f=>f.liq).reverse()],
  ["Investment Yield (%)",...financial.map(f=>f.iy+'%').reverse()],
]);

// products
const products = ["Motor Insurance","Property & Fire","Marine & Cargo","Engineering & Contractors","Aviation","Agriculture & Livestock","Micro Insurance","Miscellaneous & Liability"];
document.getElementById('productsList').innerHTML = products.map(p=>`
  <li class="flex items-center gap-2 rounded-lg border border-line/70 bg-brand-soft/40 px-3 py-2 text-sm">
    <span class="h-1.5 w-1.5 rounded-full bg-brand"></span><span class="truncate">${p}</span>
  </li>`).join('');

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
  document.getElementById('networkList').innerHTML = network.map(([l,v])=>`
    <div class="flex items-center gap-3 rounded-xl border border-line/70 bg-brand-soft/40 p-3">
      <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand text-white text-xs">◉</span>
      <div class="min-w-0 flex-1"><dt class="text-xs text-mute">${l}</dt><dd class="text-lg font-semibold tabular-nums">${v}</dd></div>
      <a href="#" class="text-xs font-medium text-brand hover:text-brand-deep">View</a>
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
    ['Total Branch Offices', new Intl.NumberFormat('en-IN').format(rows.length)],
    ['Provinces Covered', `${new Intl.NumberFormat('en-IN').format(provinces.length)} / ${new Intl.NumberFormat('en-IN').format(totalProvinceCount)}`],
    ['Licensed Agents', '0'],
    ['Surveyors', '0'],
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

// complaints
const complaints = [
  ["Claim delays",10,20],["Policy disputes",5,10],["Premium billing",12,24],
  ["Service quality",8,16],["Documentation",14,27],["Other",2,4]
];
const cc = ['var(--c1)','var(--c2)','var(--c3)','var(--c4)','var(--c5)','var(--c6)'];
document.getElementById('complaintList').innerHTML = complaints.map(([r,c,s],i)=>`
  <li>
    <div class="flex items-baseline justify-between text-sm">
      <span class="truncate">${r}</span><span class="tabular-nums text-mute">${c} · ${s}%</span>
    </div>
    <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-secondary">
      <div class="h-full rounded-full" style="width:${Math.min(s*3,100)}%;background:${cc[i]}"></div>
    </div>
  </li>`).join('');

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

const barData = portfolio.filter(p=>p.a>0);
new Chart(document.getElementById('portfolioBar'),{
  type:'bar',
  data:{labels:barData.map(p=>p.p),datasets:[{label:'Amount (NPR)',data:barData.map(p=>p.a),backgroundColor:barData.map((_,i)=>palette[i%palette.length]),borderRadius:6}]},
  options:{indexAxis:'y',maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{ticks:{callback:v=>(v/1e6).toFixed(1)+'M',font:{size:11}},grid:{color:'#f1e5e2'}},y:{ticks:{font:{size:11}},grid:{display:false}}}}
});

new Chart(document.getElementById('finLine'),{
  type:'line',
  data:{labels:financial.map(f=>f.y),datasets:[
    {label:'Solvency (x)',data:financial.map(f=>f.solvency),borderColor:palette[0],backgroundColor:palette[0],tension:.3,borderWidth:2.5},
    {label:'Return on Equity %',data:financial.map(f=>f.roe),borderColor:palette[1],backgroundColor:palette[1],tension:.3,borderWidth:2},
    {label:'EPS (NPR)',data:financial.map(f=>f.eps),borderColor:palette[3],backgroundColor:palette[3],tension:.3,borderWidth:2},
    {label:'Net Profit Margin %',data:financial.map(f=>f.npm),borderColor:palette[4],backgroundColor:palette[4],tension:.3,borderWidth:2},
  ]},
  options:{maintainAspectRatio:false,plugins:{legend:{position:'bottom',labels:{boxWidth:8,font:{size:11},usePointStyle:true}}},scales:{y:{grid:{color:'#f1e5e2'},ticks:{font:{size:11}}},x:{grid:{display:false},ticks:{font:{size:11}}}}}
});
</script>
</body>
</html>
