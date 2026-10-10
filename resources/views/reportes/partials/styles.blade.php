<style>
.reports-page { 
    display: flex;
    flex-direction: column;
    min-height: calc(100vh - var(--navbar-h) - 4rem);
    min-width: 0; 
    padding: 0; 
    color: var(--text-main); 
}
.reports-page-scrollable {
    flex: 1;

}
.reports-page .ic-card.flex-card {
    display: flex;
    flex-direction: column;
    flex: 1;
}
.reports-page .ic-card.flex-card .table-responsive {
    flex: 1;
}
    .reports-page .text-dark { color:var(--text-main)!important; }
    .reports-page .text-muted { color:var(--text-muted)!important; }
    .reports-toolbar { display:flex; flex-wrap:nowrap; align-items:flex-end; justify-content:space-between; gap:.65rem; }
    .reports-filter { display:flex; flex:1 1 auto; flex-wrap:nowrap; align-items:flex-end; gap:.7rem; }
    .report-detail-toolbar { gap:.85rem!important; align-items:flex-end!important; flex-shrink: 0; }
    .report-detail-toolbar > .btn { flex:0 0 auto; height:34px; min-height:34px; padding:.4rem .7rem; border-radius:7px; font-size:.8rem; line-height:1.25; font-weight:600; }
    .report-detail-toolbar > .reports-toolbar { flex:1 1 auto; min-width:0; }
    .reports-filter>div { min-width:145px; }
    .reports-filter .btn-primary { margin-left:auto; }
    .reports-filter .btn,.reports-toolbar>a { min-height:34px; display:inline-flex; align-items:center; justify-content:center; padding:.4rem .7rem; border-radius:7px; font-weight:600; white-space:nowrap; }
    .reports-toolbar>a { margin-left:auto; box-shadow:0 3px 8px rgba(220,53,69,.16); }
    .reports-filter .form-control { height:34px; border:1px solid #dfe5ef; border-radius:7px; background:#fff; }
    .reports-filter label { display:block; margin-bottom:.2rem; color:var(--text-muted); font-size:.68rem; font-weight:700; }
    .reports-filter .form-control { min-width:150px; border-radius:7px; font-size:.82rem; }
    .reports-page .ic-card { min-width:0; max-width:100%; border:0; border-radius:12px; box-shadow:0 4px 6px rgba(0,0,0,.04); overflow:hidden; margin-bottom: 1rem; flex-shrink: 0; }
    .reports-page .ic-card-header { padding:1rem 1.25rem; border-bottom:1px solid #f0f2f5; background:#fff; color:var(--text-main); font-weight:700; overflow-wrap:anywhere; flex-shrink: 0; }
    .reports-page .report-stat { display:flex; align-items:center; justify-content:space-between; min-height:92px; padding:1rem 1.1rem; border-radius:10px; color:#fff; background:var(--sidebar-bg); box-shadow:0 4px 10px rgba(0,0,0,.08); }
    .reports-page .report-stat-label { font-size:.7rem; font-weight:700; opacity:.82; text-transform:uppercase; }
    .reports-page .report-stat-value { margin:0; font-size:1.45rem; font-weight:800; line-height:1.2; }
    .reports-page .report-stat-icon { font-size:1.6rem; opacity:.42; }
    .report-chart-panel { position:relative; height:240px; padding:1rem; flex-shrink: 0; }
    .report-chart-panel canvas { position:absolute; inset:0; width:100% !important; height:100% !important; }
    .report-chart-title { margin-bottom:.75rem; color:var(--text-muted); font-size:.72rem; font-weight:700; text-transform:uppercase; overflow-wrap:anywhere; }
    .reports-page .ic-table { width:100%; margin:0; table-layout:fixed; }
    .reports-page .ic-table th { position: sticky; top: 0; padding:.7rem .65rem; background:color-mix(in srgb, var(--primary) 7%, #fff); color:var(--primary); font-size:.7rem; text-transform:uppercase; overflow-wrap:anywhere; z-index: 10; }
    .reports-page .ic-table td { padding:.65rem; color:var(--text-main); font-size:.82rem; vertical-align:middle; overflow-wrap:anywhere; word-break:normal; }
    .reports-page .report-empty { padding:1.4rem; color:var(--text-muted); text-align:center; font-size:.85rem; }
    .reports-page .row { flex-shrink: 0; }
    .reports-page .row > [class*="col-"] { min-width:0; }
    .report-part { border-top:1px solid #f0f2f5; }
    .report-part:first-child { border-top:0; }
    .report-card-link { position:relative; display:flex; flex-direction:column; height:100%; min-height:320px; padding:1.45rem 1.5rem 1.45rem; border:1px solid #eef1f6; border-radius:14px; background:#fff; box-shadow:0 4px 10px rgba(0,0,0,.04); color:var(--text-main); text-decoration:none; overflow:hidden; transition:transform .18s, box-shadow .18s, border-color .18s; }
    .report-card-link::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg, color-mix(in srgb, var(--primary) 75%, #fff), color-mix(in srgb, var(--primary) 18%, #fff)); }
    .report-card-link:hover { transform:translateY(-3px); box-shadow:0 14px 26px rgba(0,0,0,.09); border-color:color-mix(in srgb, var(--primary) 28%, #fff); color:var(--text-main); text-decoration:none; }
    .report-card-head { display:flex; align-items:center; gap:.8rem; min-width:0; }
    .report-card-icon { display:inline-flex; width:46px; height:46px; flex:0 0 46px; align-items:center; justify-content:center; border-radius:12px; background:linear-gradient(135deg, color-mix(in srgb, var(--primary) 14%, #fff), #f1f5ff); color:var(--primary); font-size:1.15rem; }
    .report-card-title { min-width:0; font-size:1rem; font-weight:700; color:var(--text-main); overflow-wrap:anywhere; }
    .report-card-stats { display:grid; grid-template-columns:1fr 1fr; gap:1rem; flex:1; margin:1rem 0 1.15rem; padding-top:.95rem; border-top:1px dashed #e8edf4; }
    .report-stat-item-label { display:block; color:var(--text-muted); font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.02em; }
    .report-stat-item-value { display:block; margin-top:.25rem; color:var(--text-main); font-size:1.3rem; font-weight:800; line-height:1.25; }
    .report-card-mini-chart { position:relative; flex:1; min-height:95px; margin-top:.9rem; margin-bottom:.35rem; border:1px solid #f5f7fa; border-radius:10px; background:#fff; }
    .report-card-mini-title { display:block; margin-bottom:.35rem; color:var(--text-muted); font-size:.64rem; font-weight:700; text-transform:uppercase; letter-spacing:.02em; }
    .report-card-mini-wrap { position:relative; width:100%; height:78px; }
    .report-card-mini-chart canvas { width:100% !important; height:100% !important; display:block; }
    .report-card-mini-empty { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:var(--text-muted); font-size:.7rem; pointer-events:none; }
    .report-card-link:hover .report-card-cta { border-color:var(--primary); background:var(--primary); color:#fff; }
    @media(max-width:900px) { .report-detail-toolbar{flex-wrap:wrap!important;align-items:flex-end!important}.report-detail-toolbar>.reports-toolbar{flex-basis:100%} }
    @media(max-width:767.98px) {
        .reports-page { height: auto; overflow: visible; padding:.5rem; }
        .reports-page-scrollable { overflow-y: visible; padding-right: 0; }
        .reports-page .ic-card.flex-card { overflow: visible; }
        .reports-page .ic-card.flex-card .table-responsive { overflow-y: auto; }
        .report-detail-toolbar { gap:.5rem!important; }
        .report-detail-toolbar>.reports-toolbar { flex-basis:100%; }
        .report-detail-toolbar>.btn { align-self:flex-start; }
        .reports-toolbar { flex-wrap:wrap; align-items:stretch; gap:.4rem; }
        .reports-filter { display:grid; width:100%; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.4rem; }
        .reports-filter>div { min-width:0; margin:0!important; }
        .reports-filter .form-control { width:100%; min-width:0; }
        .reports-filter .btn { width:100%; margin:0!important; padding:.4rem .35rem; font-size:.76rem; }
        .reports-filter .btn-primary { grid-column:1; }
        .reports-toolbar>a { width:100%; margin:0!important; }
    }
</style>
