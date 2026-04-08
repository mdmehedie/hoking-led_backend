<style>
:root {
    --mfm-bg: #ffffff;
    --mfm-text: #111827;
    --mfm-text-muted: #6b7280;
    --mfm-border: #e5e7eb;
    --mfm-bg-hover: #f3f4f6;
    --mfm-badge-bg: #f3f4f6;
    --mfm-badge-text: #6b7280;
    --mfm-card-bg: #ffffff;
    --mfm-overlay: rgba(0,0,0,0.5);
    --mfm-grid-empty: #9ca3af;
    --mfm-input-bg: #ffffff;
    --mfm-input-border: #d1d5db;
    --mfm-modal-bg: #ffffff;
}
:is(.dark, .dark *) {
    --mfm-bg: #1f2937;
    --mfm-text: #f9fafb;
    --mfm-text-muted: #9ca3af;
    --mfm-border: #374151;
    --mfm-bg-hover: #374151;
    --mfm-badge-bg: #374151;
    --mfm-badge-text: #d1d5db;
    --mfm-card-bg: #1f2937;
    --mfm-overlay: rgba(0,0,0,0.7);
    --mfm-grid-empty: #6b7280;
    --mfm-input-bg: #374151;
    --mfm-input-border: #4b5563;
    --mfm-modal-bg: #1f2937;
}

#mfm-modal { display:none; position:fixed; inset:0; z-index:9999; align-items:center; justify-content:center; background:var(--mfm-overlay); }
#mfm-panel { background:var(--mfm-modal-bg); border-radius:8px; box-shadow:0 20px 60px rgba(0,0,0,0.3); width:90vw; max-width:1100px; height:85vh; display:flex; flex-direction:column; }
#mfm-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--mfm-border); }
#mfm-header h2 { font-size:18px; font-weight:600; color:var(--mfm-text); margin:0; }
#mfm-close-btn { background:none; border:none; font-size:24px; cursor:pointer; color:var(--mfm-text-muted); }
#mfm-toolbar { padding:16px 20px; border-bottom:1px solid var(--mfm-border); display:flex; gap:12px; flex-wrap:wrap; align-items:center; }
#mfm-search-input { flex:1; min-width:200px; padding:8px 12px; border:1px solid var(--mfm-input-border); border-radius:6px; font-size:14px; background:var(--mfm-input-bg); color:var(--mfm-text); }
#mfm-search-input::placeholder { color:var(--mfm-text-muted); }
#mfm-file-label { padding:6px 12px; background:var(--mfm-badge-bg); border-radius:6px; font-size:13px; color:var(--mfm-badge-text); cursor:pointer; transition:all 0.2s; }
#mfm-file-label:hover { opacity:0.85; }
#mfm-file-label.uploading { background:#fbbf24; color:#78350f; }
#mfm-file-label.success { background:#d1fae5; color:#065f46; }
#mfm-file-label.error { background:#fee2e2; color:#991b1b; }
#mfm-grid { flex:1; overflow-y:auto; padding:16px 20px; display:grid; grid-template-columns:repeat(4,1fr); gap:12px; align-content:start; }
.mfm-card { position:relative; border:1px solid var(--mfm-border); border-radius:8px; overflow:hidden; cursor:pointer; width:100%; max-width:220px; align-self:start; background:var(--mfm-card-bg); transition:box-shadow 0.2s; }
.mfm-card:hover { box-shadow:0 4px 12px rgba(0,0,0,0.15); }
.mfm-card img { width:100%; height:112px; object-fit:cover; display:block; }
.mfm-card-name { padding:4px 8px; font-size:11px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--mfm-text-muted); background:var(--mfm-card-bg); }
.mfm-card-overlay { position:absolute; inset:0; background:rgba(0,0,0,0.5); opacity:0; transition:opacity 0.2s; display:flex; align-items:center; justify-content:center; gap:8px; }
.mfm-card:hover .mfm-card-overlay { opacity:1; }
.mfm-card-overlay button { padding:6px 12px; border:none; border-radius:4px; font-size:12px; cursor:pointer; color:#fff; }
.mfm-select-btn { background:#3b82f6; }
.mfm-del-btn { background:#ef4444; }
#mfm-pagination { padding:12px 20px; border-top:1px solid var(--mfm-border); display:flex; align-items:center; justify-content:space-between; font-size:13px; }
#mfm-pagination span { color:var(--mfm-text-muted); }
.mfm-page-btn { padding:4px 8px; border:none; border-radius:4px; font-size:12px; cursor:pointer; background:var(--mfm-bg-hover); color:var(--mfm-text); }
.mfm-page-btn.active { background:#2563eb; color:#fff; }
.mfm-empty { grid-column:1/-1; text-align:center; padding:60px 20px; color:var(--mfm-grid-empty); }
</style>

<div id="mfm-modal">
    <div id="mfm-panel">
        <div id="mfm-header">
            <h2>Media Library</h2>
            <button id="mfm-close-btn" onclick="MFM.close()">&times;</button>
        </div>
        <div id="mfm-toolbar">
            <input id="mfm-search-input" type="text" placeholder="Search files..." oninput="MFM.debouncedSearch()">
            <input id="mfm-file" type="file" accept="image/*" style="display:none;" onchange="MFM.fileSelected(this)">
            <span id="mfm-file-label" onclick="document.getElementById('mfm-file').click()">Upload Image</span>
        </div>
        <div id="mfm-grid"></div>
        <div id="mfm-pagination"></div>
    </div>
</div>

<script>
window.MFM = {
    isOpen: false,
    page: 1,
    search: '',
    pagination: {},
    media: [],
    debounceTimer: null,
    csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',

    open() {
        document.getElementById('mfm-modal').style.display = 'flex';
        this.isOpen = true;
        this.page = 1;
        this.load();
    },

    close() {
        document.getElementById('mfm-modal').style.display = 'none';
        this.isOpen = false;
    },

    load() {
        const params = new URLSearchParams({ per_page: 20, mediaPage: this.page });
        if (this.search) params.append('search', this.search);
        fetch('/media-library?' + params)
            .then(r => r.json())
            .then(res => { this.renderGrid(res); this.renderPagination(); })
            .catch(() => {});
    },

    renderGrid(res) {
        this.media = res.data || [];
        this.pagination = res.pagination || {};
        const grid = document.getElementById('mfm-grid');
        if (!grid) return;
        if (this.media.length === 0) {
            grid.innerHTML = '<div class="mfm-empty">No media found. Upload some images!</div>';
        } else {
            grid.innerHTML = this.media.map(m =>
                '<div class="mfm-card">' +
                '<img src="' + m.url + '" alt="">' +
                '<div class="mfm-card-name" title="' + m.original_name + '">' + m.original_name + '</div>' +
                '<div class="mfm-card-overlay">' +
                '<button class="mfm-select-btn" onclick="event.stopPropagation();MFM.select(\'' + m.url + '\')">Select</button>' +
                '<button class="mfm-del-btn" onclick="event.stopPropagation();MFM.del(' + m.id + ')">Delete</button>' +
                '</div></div>'
            ).join('');
        }
    },

    renderPagination() {
        const el = document.getElementById('mfm-pagination');
        if (!el) return;
        if (!this.pagination.last_page || this.pagination.last_page <= 1) { el.innerHTML = ''; return; }
        let html = '<span>Page ' + this.pagination.current_page + ' of ' + this.pagination.last_page + '</span><div style="display:flex;gap:4px;">';
        for (let i = 1; i <= this.pagination.last_page; i++) {
            html += '<button class="mfm-page-btn' + (i === this.page ? ' active' : '') + '" onclick="MFM.page=' + i + ';MFM.load()">' + i + '</button>';
        }
        html += '</div>';
        el.innerHTML = html;
    },

    debouncedSearch() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.search = document.getElementById('mfm-search-input').value;
            this.page = 1;
            this.load();
        }, 400);
    },

    fileSelected(input) {
        const label = document.getElementById('mfm-file-label');
        if (input.files[0]) {
            label.textContent = 'Uploading ' + input.files[0].name + '...';
            label.className = 'uploading';
            this._uploadFile(input.files[0], input, label);
        }
    },

    _uploadFile(file, input, label) {
        const fd = new FormData();
        fd.append('file', file);
        fetch('/media-library/upload', { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf }, body: fd })
            .then(r => r.json())
            .then(() => {
                input.value = '';
                label.textContent = 'Upload Image';
                label.className = 'success';
                setTimeout(() => { label.textContent = 'Upload Image'; label.className = ''; }, 2000);
                this.load();
            })
            .catch(() => {
                label.className = 'error';
                setTimeout(() => { label.textContent = 'Upload Image'; label.className = ''; }, 2000);
            });
    },

    del(id) {
        if (!confirm('Delete this image permanently?')) return;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || '';
        fetch('/media-library/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf } })
            .then(() => this.load());
    },

    select(url) {
        if (window._tinymceCb) { window._tinymceCb(url); window._tinymceCb = null; }
        this.close();
    }
};

window.addEventListener('open-media-manager', () => MFM.open());
</script>
