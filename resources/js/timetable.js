// University timetable view — rows: periods (Tiết 1-15), cols: weekdays

const PERIODS = [
    { no: 1,  start: '06:45', end: '07:30' },
    { no: 2,  start: '07:30', end: '08:15' },
    { no: 3,  start: '08:15', end: '09:00' },
    { no: 4,  start: '09:20', end: '10:05' },
    { no: 5,  start: '10:05', end: '10:50' },
    { no: 6,  start: '10:50', end: '11:35' },
    { no: 7,  start: '12:30', end: '13:15' },
    { no: 8,  start: '13:15', end: '14:00' },
    { no: 9,  start: '14:00', end: '14:45' },
    { no: 10, start: '15:05', end: '15:50' },
    { no: 11, start: '15:50', end: '16:35' },
    { no: 12, start: '16:35', end: '17:20' },
    { no: 13, start: '18:00', end: '18:45' },
    { no: 14, start: '18:45', end: '19:30' },
    { no: 15, start: '19:30', end: '20:15' },
];

// Group boundaries for visual separator rows
const GROUP_AFTER = new Set([3, 6, 9, 12]); // add separator line after these periods

const DAY_NAMES = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];

const STATUS_LABELS = {
    pending: 'Chờ duyệt', approved: 'Đã duyệt',
    rejected: 'Từ chối',  cancelled: 'Đã huỷ',
};

const COLORS = {
    pending:   { bg: '#fffbeb', border: '#f59e0b', text: '#78350f', badge: '#f59e0b' },
    approved:  { bg: '#f0fdf4', border: '#16a34a', text: '#14532d', badge: '#16a34a' },
    rejected:  { bg: '#fef2f2', border: '#ef4444', text: '#7f1d1d', badge: '#ef4444' },
    cancelled: { bg: '#f9fafb', border: '#9ca3af', text: '#374151', badge: '#9ca3af' },
};

function toMin(t) {
    const [h, m] = t.split(':').map(Number);
    return h * 60 + m;
}
function pad(n) { return String(n).padStart(2, '0'); }
function isoDate(d) { return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`; }
function fmtDate(d) { return `${pad(d.getDate())}/${pad(d.getMonth()+1)}`; }

function getWeekStart(d) {
    const dow = d.getDay();
    const diff = dow === 0 ? -6 : 1 - dow;
    const mon = new Date(d);
    mon.setDate(d.getDate() + diff);
    mon.setHours(0, 0, 0, 0);
    return mon;
}

function getWeekDates(ws) {
    return Array.from({ length: 7 }, (_, i) => {
        const d = new Date(ws);
        d.setDate(ws.getDate() + i);
        return d;
    });
}

// Returns periods that overlap with the event's time (by hour:minute, ignoring date)
function getSpanningPeriods(evStart, evEnd) {
    const s = evStart.getHours() * 60 + evStart.getMinutes();
    const e = evEnd.getHours()   * 60 + evEnd.getMinutes();
    return PERIODS.filter(p => toMin(p.start) < e && toMin(p.end) > s);
}

function buildGrid(weekDates, events) {
    const grid = {};
    for (const d of weekDates) {
        grid[isoDate(d)] = Object.fromEntries(PERIODS.map(p => [p.no, { type: 'empty' }]));
    }
    for (const ev of events) {
        const evStart = new Date(ev.start);
        const evEnd   = new Date(ev.end);
        const key     = isoDate(evStart);
        if (!grid[key]) continue;

        const periods = getSpanningPeriods(evStart, evEnd);
        if (!periods.length) continue;

        const first = periods[0].no;
        const last  = periods[periods.length - 1].no;
        grid[key][first] = { type: 'event', ev, rowspan: periods.length, firstTiet: first, lastTiet: last };
        for (let i = 1; i < periods.length; i++) {
            grid[key][periods[i].no] = { type: 'span' };
        }
    }
    return grid;
}

// ---------- Popup ----------

let _hideTimer = null;
function scheduleHide() { _hideTimer = setTimeout(hidePopup, 150); }
function cancelHide()   { clearTimeout(_hideTimer); }
function hidePopup() {
    const el = document.getElementById('tt-event-popup');
    if (el) el.style.display = 'none';
}

function ensurePopup() {
    const ID = 'tt-event-popup';
    let el = document.getElementById(ID);
    if (el) return el;

    el = document.createElement('div');
    el.id = ID;
    el.style.cssText = 'position:fixed;z-index:9999;background:#fff;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 4px 24px rgba(0,0,0,.14);width:290px;display:none;font-family:inherit;';
    el.innerHTML = `
        <div style="padding:10px 14px;border-bottom:1px solid #f3f4f6;">
            <span id="tt-pop-status" style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:999px;"></span>
        </div>
        <div style="padding:12px 14px;">
            <p id="tt-pop-title" style="font-weight:700;color:#111827;font-size:14px;margin:0 0 8px;line-height:1.4;"></p>
            <div id="tt-pop-meta" style="font-size:12px;color:#6b7280;line-height:2;"></div>
            <div id="tt-pop-purpose" style="font-size:12px;color:#374151;margin-top:8px;padding:6px 8px;background:#f9fafb;border-radius:6px;line-height:1.5;display:none;"></div>
        </div>`;
    document.body.appendChild(el);

    el.addEventListener('mouseenter', cancelHide);
    el.addEventListener('mouseleave', scheduleHide);
    return el;
}

function showPopup(ev, jsEvent) {
    const popup  = ensurePopup();
    const props  = ev.extendedProps || {};
    const c      = COLORS[props.status] || COLORS.cancelled;
    const stEl   = document.getElementById('tt-pop-status');
    stEl.textContent = STATUS_LABELS[props.status] || props.status;
    stEl.style.color = c.text;
    stEl.style.background = c.bg;
    stEl.style.border = `1px solid ${c.border}`;

    document.getElementById('tt-pop-title').textContent = ev.title;

    const s = new Date(ev.start), e = new Date(ev.end);
    let meta = '';
    if (props.user)     meta += `<div>👤 ${props.user}</div>`;
    if (props.bookable) meta += `<div>📍 ${props.bookable}</div>`;
    meta += `<div>🕐 ${fmtDate(s)} ${pad(s.getHours())}:${pad(s.getMinutes())} – ${pad(e.getHours())}:${pad(e.getMinutes())}</div>`;
    document.getElementById('tt-pop-meta').innerHTML = meta;

    const purpEl = document.getElementById('tt-pop-purpose');
    if (props.purpose) { purpEl.textContent = props.purpose; purpEl.style.display = 'block'; }
    else purpEl.style.display = 'none';

    popup.style.display = 'block';
    popup.style.top = '-9999px'; popup.style.left = '0';
    const h = popup.offsetHeight, w = popup.offsetWidth;
    let x = jsEvent.clientX + 14, y = jsEvent.clientY - 20;
    if (x + w > window.innerWidth  - 10) x = jsEvent.clientX - w - 14;
    if (y + h > window.innerHeight - 10) y = jsEvent.clientY - h;
    if (y < 10) y = 10;
    if (x < 10) x = 10;
    popup.style.left = x + 'px';
    popup.style.top  = y + 'px';
}

// ---------- Event cell ----------

function makeEventTd(cell) {
    const { ev, rowspan, firstTiet, lastTiet } = cell;
    const props = ev.extendedProps || {};
    const c = COLORS[props.status] || COLORS.cancelled;

    const td = document.createElement('td');
    td.rowSpan = rowspan;
    td.style.cssText = `background:${c.bg};border:1px solid #e5e7eb;border-left:3px solid ${c.border};padding:5px 8px;vertical-align:top;cursor:pointer;`;

    td.innerHTML = `
        <div style="font-weight:600;font-size:12px;color:${c.text};line-height:1.4;">${ev.title}</div>
        ${props.bookable ? `<div style="font-size:11px;color:${c.text};opacity:.8;margin-top:2px;">📍 ${props.bookable}</div>` : ''}
        ${props.user    ? `<div style="font-size:11px;color:${c.text};opacity:.8;">👤 ${props.user}</div>` : ''}
        <div style="font-size:10px;color:${c.text};opacity:.65;margin-top:3px;">Tiết ${firstTiet}–${lastTiet}</div>
        <span style="font-size:10px;font-weight:600;color:${c.badge};">${STATUS_LABELS[props.status] || ''}</span>
    `;
    td.addEventListener('mouseenter', e => { cancelHide(); showPopup(ev, e); });
    td.addEventListener('mouseleave', scheduleHide);
    td.addEventListener('click', () => { if (ev.url) window.location.href = ev.url; });
    return td;
}

// ---------- Main ----------

export function initTimetable(elementId, eventsUrl) {
    const container = document.getElementById(elementId);
    if (!container) return;

    let weekStart = getWeekStart(new Date());

    async function loadAndRender() {
        const weekDates = getWeekDates(weekStart);
        const weekEnd   = new Date(weekStart);
        weekEnd.setDate(weekStart.getDate() + 7);

        container.innerHTML = '<div style="text-align:center;padding:48px 0;color:#6b7280;font-size:14px;">Đang tải lịch...</div>';

        try {
            const params = new URLSearchParams({ start: weekStart.toISOString(), end: weekEnd.toISOString() });
            const res = await fetch(`${eventsUrl}?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            const events = Array.isArray(data) ? data : [];
            render(weekDates, events);
        } catch (err) {
            console.error('Timetable load error:', err);
            container.innerHTML = '<div style="text-align:center;padding:48px 0;color:#ef4444;font-size:14px;">Không thể tải lịch. Vui lòng thử lại.</div>';
        }
    }

    function render(weekDates, events) {
        const grid     = buildGrid(weekDates, events);
        const todayStr = isoDate(new Date());
        const weekLabel = `Tuần ${fmtDate(weekDates[0])} – ${fmtDate(weekDates[6])} / ${weekDates[0].getFullYear()}`;

        const frag = document.createDocumentFragment();

        // Navigation bar
        const nav = document.createElement('div');
        nav.style.cssText = 'display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;';
        nav.innerHTML = `
            <button id="tt-prev" style="padding:5px 14px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;cursor:pointer;font-size:13px;color:#374151;">← Tuần trước</button>
            <span style="font-weight:700;font-size:14px;color:#111827;">${weekLabel}</span>
            <button id="tt-next" style="padding:5px 14px;border:1px solid #e5e7eb;border-radius:6px;background:#fff;cursor:pointer;font-size:13px;color:#374151;">Tuần sau →</button>
        `;
        frag.appendChild(nav);

        // Scrollable wrapper
        const scroll = document.createElement('div');
        scroll.style.cssText = 'overflow-x:auto;';

        const table = document.createElement('table');
        table.style.cssText = 'width:100%;border-collapse:collapse;font-size:12px;min-width:720px;';

        // THEAD
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');

        const thCorner = document.createElement('th');
        thCorner.style.cssText = 'width:64px;padding:8px 4px;background:#eef2ff;border:1px solid #d1d5db;text-align:center;font-weight:700;color:#3730a3;font-size:12px;';
        thCorner.textContent = 'Tiết';
        headerRow.appendChild(thCorner);

        for (const d of weekDates) {
            const th = document.createElement('th');
            const today = isoDate(d) === todayStr;
            th.style.cssText = `padding:8px 4px;background:${today ? '#dbeafe' : '#eef2ff'};border:1px solid #d1d5db;text-align:center;font-weight:600;color:${today ? '#1e40af' : '#3730a3'};font-size:12px;`;
            th.innerHTML = `<div>${DAY_NAMES[d.getDay()]}</div><div style="font-weight:400;color:#6b7280;font-size:11px;margin-top:1px;">${fmtDate(d)}</div>`;
            headerRow.appendChild(th);
        }
        thead.appendChild(headerRow);
        table.appendChild(thead);

        // TBODY
        const tbody = document.createElement('tbody');

        for (const period of PERIODS) {
            const tr = document.createElement('tr');

            // Period label cell
            const separator = GROUP_AFTER.has(period.no - 1);
            const tdLabel = document.createElement('td');
            tdLabel.style.cssText = `padding:3px 4px;text-align:center;background:#f8fafc;border:1px solid #e5e7eb;${separator ? 'border-top:2px solid #c7d2fe;' : ''}color:#4b5563;font-size:11px;white-space:nowrap;`;
            tdLabel.innerHTML = `<div style="font-weight:700;">Tiết ${period.no}</div><div style="color:#9ca3af;font-size:10px;">${period.start}</div>`;
            tr.appendChild(tdLabel);

            for (const d of weekDates) {
                const cell = grid[isoDate(d)]?.[period.no];
                if (!cell || cell.type === 'empty') {
                    const td = document.createElement('td');
                    td.style.cssText = `border:1px solid #e5e7eb;${separator ? 'border-top:2px solid #c7d2fe;' : ''}min-width:110px;height:34px;background:${isoDate(d) === todayStr ? '#fafcff' : '#fff'};`;
                    tr.appendChild(td);
                } else if (cell.type === 'event') {
                    const td = makeEventTd(cell);
                    if (separator) td.style.borderTop = '2px solid #c7d2fe';
                    tr.appendChild(td);
                }
                // type === 'span' → skipped (covered by rowspan above)
            }

            tbody.appendChild(tr);
        }

        table.appendChild(tbody);
        scroll.appendChild(table);
        frag.appendChild(scroll);

        container.innerHTML = '';
        container.appendChild(frag);

        document.getElementById('tt-prev').addEventListener('click', () => {
            weekStart = new Date(weekStart);
            weekStart.setDate(weekStart.getDate() - 7);
            loadAndRender();
        });
        document.getElementById('tt-next').addEventListener('click', () => {
            weekStart = new Date(weekStart);
            weekStart.setDate(weekStart.getDate() + 7);
            loadAndRender();
        });
    }

    loadAndRender();
}
