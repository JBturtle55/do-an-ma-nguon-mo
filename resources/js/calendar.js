import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

let _hideTimer = null;
function scheduleHide() { _hideTimer = setTimeout(() => { const p = document.getElementById('fc-event-popup'); if (p) p.style.display = 'none'; }, 150); }
function cancelHide()   { clearTimeout(_hideTimer); }

const STATUS_MAP = {
    pending:   { label: 'Chờ duyệt', color: '#b45309', bg: '#fef3c7' },
    approved:  { label: 'Đã duyệt',  color: '#065f46', bg: '#d1fae5' },
    rejected:  { label: 'Từ chối',   color: '#991b1b', bg: '#fee2e2' },
    cancelled: { label: 'Đã huỷ',    color: '#374151', bg: '#f3f4f6' },
};

function pad(n) { return String(n).padStart(2, '0'); }

function fmtDT(d) {
    if (!d) return '';
    return `${pad(d.getDate())}/${pad(d.getMonth()+1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

function fmtTime(d) {
    if (!d) return '';
    return `${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

function createPopup() {
    const el = document.createElement('div');
    el.id = 'fc-event-popup';
    el.style.cssText = [
        'position:fixed', 'z-index:9999', 'background:#fff',
        'border:1px solid #e5e7eb', 'border-radius:10px',
        'box-shadow:0 4px 20px rgba(0,0,0,0.15)', 'width:290px',
        'display:none', 'font-family:inherit',
    ].join(';');

    el.innerHTML = `
        <div style="padding:10px 14px;border-bottom:1px solid #f3f4f6;">
            <span id="fc-popup-status" style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:999px;"></span>
        </div>
        <div style="padding:12px 14px;">
            <p id="fc-popup-title" style="font-weight:600;color:#111827;font-size:14px;margin:0 0 10px;line-height:1.4;"></p>
            <div id="fc-popup-meta" style="font-size:12px;color:#6b7280;line-height:2;"></div>
            <div id="fc-popup-purpose" style="font-size:12px;color:#374151;margin-top:8px;padding:6px 8px;background:#f9fafb;border-radius:6px;line-height:1.5;display:none;"></div>
        </div>
        <div style="padding:8px 14px;border-top:1px solid #f3f4f6;text-align:right;">
            <a id="fc-popup-link" href="#" style="font-size:12px;color:#2563eb;text-decoration:none;font-weight:500;">Xem chi tiết →</a>
        </div>
    `;

    document.body.appendChild(el);

    // Keep popup visible while mouse is over it
    el.addEventListener('mouseenter', cancelHide);
    el.addEventListener('mouseleave', scheduleHide);

    return el;
}

function showEventPopup(event, jsEvent) {
    const popup = document.getElementById('fc-event-popup') || createPopup();
    const props  = event.extendedProps || {};
    const status = STATUS_MAP[props.status] || { label: props.status || '', color: '#6b7280', bg: '#f3f4f6' };

    const statusEl = document.getElementById('fc-popup-status');
    statusEl.textContent = status.label;
    statusEl.style.color = status.color;
    statusEl.style.background = status.bg;

    document.getElementById('fc-popup-title').textContent = event.title;

    let meta = '';
    if (props.user)    meta += `<div>👤 ${props.user}</div>`;
    if (props.bookable) meta += `<div>📍 ${props.bookable}</div>`;
    if (event.start)   meta += `<div>🕐 ${fmtDT(event.start)}${event.end ? ' – ' + fmtTime(event.end) : ''}</div>`;
    document.getElementById('fc-popup-meta').innerHTML = meta;

    const purposeEl = document.getElementById('fc-popup-purpose');
    if (props.purpose) {
        purposeEl.textContent = props.purpose;
        purposeEl.style.display = 'block';
    } else {
        purposeEl.style.display = 'none';
    }

    document.getElementById('fc-popup-link').href = event.url || '#';

    // Show off-screen first to measure height
    popup.style.display = 'block';
    popup.style.top = '-9999px';
    popup.style.left = '0';

    const h = popup.offsetHeight;
    const w = popup.offsetWidth;
    let x = jsEvent.clientX + 14;
    let y = jsEvent.clientY - 20;

    if (x + w > window.innerWidth - 10)  x = jsEvent.clientX - w - 14;
    if (y + h > window.innerHeight - 10) y = jsEvent.clientY - h;
    if (y < 10) y = 10;
    if (x < 10) x = 10;

    popup.style.left = x + 'px';
    popup.style.top  = y + 'px';
}

export function initCalendar(elementId, eventsUrl, options = {}) {
    const el = document.getElementById(elementId);
    if (!el) return null;

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView: 'timeGridWeek',
        locale: 'vi',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
        },
        slotMinTime: '06:30:00',
        slotMaxTime: '21:00:00',
        allDaySlot: false,
        events: {
            url: eventsUrl,
            method: 'GET',
            extraParams: {
                _token: document.querySelector('meta[name="csrf-token"]')?.content,
            },
            failure: () => {
                console.error('Failed to load calendar events');
            },
        },
        eventMouseEnter: (info) => {
            cancelHide();
            showEventPopup(info.event, info.jsEvent);
        },
        eventMouseLeave: () => {
            scheduleHide();
        },
        eventClick: (info) => {
            info.jsEvent.preventDefault();
            if (info.event.url) window.location.href = info.event.url;
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false,
            hour12: false,
        },
        buttonText: {
            today: 'Hôm nay',
            month: 'Tháng',
            week: 'Tuần',
            day: 'Ngày',
            list: 'Danh sách',
        },
        ...options,
    });

    calendar.render();
    return calendar;
}
