import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

Alpine.plugin(focus);
window.Alpine = Alpine;
Alpine.start();

// Lazy-load FullCalendar only on pages that need it
window.initCalendar = (...args) =>
    import('./calendar.js').then(m => m.initCalendar(...args));

// Lazy-load timetable (period-based weekly view)
window.initTimetable = (...args) =>
    import('./timetable.js').then(m => m.initTimetable(...args));
