function byId(id) { return document.getElementById(id); }

function toggleNav(forceClose) {
    const l = byId('navLinks');
    if (!l) return;
    const opening = forceClose ? false : !l.classList.contains('open');
    l.classList.toggle('open', opening);
    byId('navToggle')?.classList.toggle('active', opening);
    byId('navOverlay')?.classList.toggle('active', opening);
    document.body.style.overflow = opening ? 'hidden' : '';
}

function openApartmentDetail(id) {
    const apt = apartments.find(a => a.id === id);
    if (!apt) return;
    const modal = byId('apartmentModal');
    if (!modal) return;
    byId('modalImage').src = apt.images[0].startsWith('http') ? apt.images[0] + '&w=900&q=80' : apt.images[0];
    byId('modalTitle').textContent = apt.name;
    byId('modalLocationText').textContent = apt.location;
    byId('modalPrice').textContent = '$' + apt.price_per_night;
    byId('modalDescription').textContent = apt.description;
    byId('modalAmenities').innerHTML = apt.amenities.map(a =>
        `<div class="modal-amenity"><i class="fas fa-check-circle"></i> ${a}</div>`
    ).join('');
    if (apt.coordinates) {
        byId('modalMap').innerHTML =
            `<iframe src="https://www.google.com/maps?q=${apt.coordinates[0]},${apt.coordinates[1]}&z=15&output=embed" allowfullscreen loading="lazy" title="${apt.name} location"></iframe>`;
    }
    modal.dataset.aptId = apt.id;
    modal.dataset.price = apt.price_per_night;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    const wCI = byId('checkIn')?.value;
    const wCO = byId('checkOut')?.value;
    if (wCI) byId('modalCheckInInput').value = wCI;
    if (wCO) byId('modalCheckOutInput').value = wCO;
    updateModalSummary();
}

function closeModal() { byId('apartmentModal')?.classList.remove('active'); document.body.style.overflow = ''; }

function handleBookingSearch(e) {
    e.preventDefault();
    const checkIn = byId('checkIn').value;
    const checkOut = byId('checkOut').value;
    const guests = byId('guests').value;
    const location = byId('location').value;
    if (!checkIn || !checkOut) { alert('Please select check-in and check-out dates.'); return false; }
    if (checkOut <= checkIn) { alert('Check-out date must be after check-in date.'); return false; }
    const available = apartments.filter(a => {
        if (location && a.location !== location) return false;
        if (a.max_guests < parseInt(guests)) return false;
        return true;
    });
    if (available.length === 0) { alert('No apartments available for your criteria.'); return false; }
    byId('apartments').scrollIntoView({ behavior: 'smooth' });
    document.querySelectorAll('.apartment-card').forEach((card, i) => {
        card.style.transition = 'all 0.5s ease';
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => { card.style.opacity = '1'; card.style.transform = 'translateY(0)'; }, i * 100);
    });
    return false;
}

function updateModalSummary() {
    const modal = byId('apartmentModal');
    if (!modal) return;
    const checkIn = byId('modalCheckInInput').value;
    const checkOut = byId('modalCheckOutInput').value;
    const price = parseInt(modal.dataset.price);
    const bf = byId('modalBookingForm');
    if (checkIn && checkOut) {
        const nights = Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
        if (nights > 0) {
            const total = price * nights;
            byId('modalNights').textContent = nights + ' nights';
            byId('modalTotal').textContent = '$' + total.toLocaleString();
            modal.dataset.nights = nights;
            modal.dataset.total = total;
            bf.style.display = 'block';
            return;
        }
    }
    byId('modalNights').textContent = '-';
    byId('modalTotal').textContent = '-';
    bf.style.display = 'none';
}

function handleBookingFromModal() {
    const modal = byId('apartmentModal');
    if (!modal) return;
    const aptId = modal.dataset.aptId;
    const aptName = byId('modalTitle').textContent;
    const price = parseInt(modal.dataset.price);
    const checkIn = byId('modalCheckInInput').value;
    const checkOut = byId('modalCheckOutInput').value;
    const guestName = byId('modalGuestName').value.trim();
    const guestEmail = byId('modalGuestEmail').value.trim();
    const guestPhone = byId('modalGuestPhone').value.trim();
    const st = byId('modalBookingStatus');
    const btn = byId('modalBookingBtn');
    if (!checkIn || !checkOut) { st.style.cssText = 'display:block;background:rgba(220,38,38,0.1);color:#DC2626;'; st.textContent = 'Please select check-in and check-out dates.'; return; }
    if (checkOut <= checkIn) { st.style.cssText = 'display:block;background:rgba(220,38,38,0.1);color:#DC2626;'; st.textContent = 'Check-out must be after check-in.'; return; }
    if (!guestName || !guestEmail) { st.style.cssText = 'display:block;background:rgba(220,38,38,0.1);color:#DC2626;'; st.textContent = 'Please enter your name and email.'; return; }
    const nights = parseInt(modal.dataset.nights) || Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
    const total = parseInt(modal.dataset.total) || (price * nights);
    btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    st.style.cssText = 'display:block;background:rgba(201,168,76,0.1);color:#C9A84C;'; st.textContent = 'Processing your booking...';
    fetch('/api/booking.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ apartment_id: aptId, apartment_name: aptName, check_in: checkIn, check_out: checkOut, guests: 1, guest_name: guestName, guest_email: guestEmail, guest_phone: guestPhone, total: total, nights: nights })
    }).then(r => r.json()).then(data => {
        if (data.success) { st.style.cssText = 'display:block;background:rgba(5,150,105,0.1);color:#059669;'; st.textContent = 'Booking confirmed! Check your email for details.'; btn.innerHTML = '<i class="fas fa-check-circle"></i> Booked Successfully'; btn.disabled = false; setTimeout(() => { closeModal(); }, 2500); }
        else { st.style.cssText = 'display:block;background:rgba(220,38,38,0.1);color:#DC2626;'; st.textContent = data.error || 'Booking failed.'; btn.disabled = false; btn.innerHTML = '<i class="fas fa-check-circle"></i> Try Again'; }
    }).catch(() => { st.style.cssText = 'display:block;background:rgba(5,150,105,0.1);color:#059669;'; st.textContent = 'Submitted! We will confirm shortly.'; btn.innerHTML = '<i class="fas fa-check-circle"></i> Submitted'; btn.disabled = false; setTimeout(() => { closeModal(); }, 2000); });
}

let calendarMonth = new Date().getMonth();
let calendarYear = new Date().getFullYear();

function getBookedDates(apartmentId) {
    const dates = [];
    (bookingsData || []).forEach(b => {
        if ((!apartmentId || b.apartment_id === apartmentId) && (b.status === 'confirmed' || b.status === 'pending')) {
            let d = new Date(b.check_in);
            const end = new Date(b.check_out);
            while (d < end) { dates.push(d.toISOString().split('T')[0]); d.setDate(d.getDate() + 1); }
        }
    });
    return dates;
}

function renderCalendar() {
    const grid = byId('calendarGrid');
    const monthYear = byId('calendarMonthYear');
    if (!grid || !monthYear) return;
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const aptId = byId('calendarApartment')?.value || '';
    monthYear.textContent = monthNames[calendarMonth] + ' ' + calendarYear;
    const headers = grid.querySelectorAll('.calendar-day-header');
    grid.innerHTML = '';
    headers.forEach(h => grid.appendChild(h));
    const firstDay = new Date(calendarYear, calendarMonth, 1).getDay();
    const daysInMonth = new Date(calendarYear, calendarMonth + 1, 0).getDate();
    const today = new Date().toISOString().split('T')[0];
    const bookedDates = getBookedDates(aptId);
    for (let i = 0; i < firstDay; i++) { const e = document.createElement('div'); e.className = 'calendar-day other-month'; grid.appendChild(e); }
    for (let d = 1; d <= daysInMonth; d++) {
        const ds = calendarYear + '-' + String(calendarMonth + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
        const day = document.createElement('div');
        day.className = 'calendar-day'; day.textContent = d;
        if (ds === today) day.classList.add('today');
        if (bookedDates.includes(ds)) day.classList.add('booked');
        else if (ds >= today) day.classList.add('available');
        day.addEventListener('click', () => {
            if (day.classList.contains('booked')) return;
            document.querySelectorAll('.calendar-day.selected').forEach(el => el.classList.remove('selected'));
            day.classList.add('selected');
            byId('checkIn').value = ds;
        });
        grid.appendChild(day);
    }
}

function changeMonth(dir) { calendarMonth += dir; if (calendarMonth > 11) { calendarMonth = 0; calendarYear++; } if (calendarMonth < 0) { calendarMonth = 11; calendarYear--; } renderCalendar(); }

let reviewIndex = 0;
function slideReviews(dir) {
    const track = byId('reviewsTrack');
    if (!track) return;
    const cards = track.querySelectorAll('.review-card');
    if (!cards.length) return;
    const cw = cards[0].offsetWidth + 24;
    const visible = window.innerWidth >= 992 ? 3 : window.innerWidth >= 768 ? 2 : 1;
    const maxI = Math.max(0, cards.length - visible);
    reviewIndex += dir;
    if (reviewIndex < 0) reviewIndex = maxI;
    if (reviewIndex > maxI) reviewIndex = 0;
    track.style.transform = `translateX(-${reviewIndex * cw}px)`;
}

const galleryItems = [
    { type: 'image', src: '/assets/images/gallery-1.jpg' },
    { type: 'image', src: '/assets/images/gallery-2.jpg' },
    { type: 'image', src: '/assets/images/gallery-3.jpg' },
    { type: 'video', src: '/assets/videos/gallery-video-2.mp4' },
    { type: 'video', src: '/assets/videos/gallery-video-3.mp4' },
    { type: 'video', src: '/assets/videos/gallery-video-4.mp4' }
];
let currentLightboxIndex = 0;

function openLightbox(index) {
    currentLightboxIndex = index;
    const item = galleryItems[index];
    const img = byId('lightboxImage');
    const vid = byId('lightboxVideo');
    if (item.type === 'video') {
        img.style.display = 'none';
        vid.style.display = 'block';
        vid.src = item.src;
        vid.play();
    } else {
        vid.style.display = 'none';
        vid.pause();
        img.style.display = 'block';
        img.src = item.src;
    }
    byId('lightbox').classList.add('active');
    byId('lightboxCounter').textContent = `${index + 1} / ${galleryItems.length}`;
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    byId('lightbox').classList.remove('active');
    byId('lightboxVideo')?.pause();
    document.body.style.overflow = '';
}
function changeLightboxImage(dir) {
    currentLightboxIndex += dir;
    if (currentLightboxIndex < 0) currentLightboxIndex = galleryItems.length - 1;
    if (currentLightboxIndex >= galleryItems.length) currentLightboxIndex = 0;
    openLightbox(currentLightboxIndex);
}

function handleContactForm(e) {
    e.preventDefault();
    const name = byId('contactName').value;
    const email = byId('contactEmail').value;
    const subject = byId('contactSubject').value;
    const message = byId('contactMessage').value;
    if (!name || !email || !message) { alert('Please fill in all required fields.'); return false; }
    fetch('/api/contact.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name, email, subject, message })
    }).then(r => r.json()).then(data => {
        if (data.success) { alert('Thank you! We will get back to you shortly.'); byId('contactForm').reset(); }
        else { alert('Failed to send message. Please try again.'); }
    }).catch(() => { alert('Thank you! We will get back to you shortly.'); byId('contactForm').reset(); });
    return false;
}

let exitFired = false;
function closeExitPopup() { byId('exitPopup')?.classList.remove('active'); }

function showToast(name, location, apartment) {
    const c = byId('toastContainer');
    if (!c) return;
    const t = document.createElement('div');
    t.className = 'toast';
    const imgs = ['https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=80&h=80&fit=crop&crop=face','https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&h=80&fit=crop&crop=face','https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=80&h=80&fit=crop&crop=face','https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=80&h=80&fit=crop&crop=face','https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=80&h=80&fit=crop&crop=face'];
    t.innerHTML = `<img src="${imgs[Math.floor(Math.random()*imgs.length)]}"><div><strong>${name}</strong> from <strong>${location}</strong> just booked <span>${apartment}</span></div>`;
    c.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; t.style.transform = 'translateX(100%)'; setTimeout(() => t.remove(), 400); }, 3500);
}

const socialProofData = [
    { n:'Sarah',l:'United Kingdom',a:'Chukwani Garden Studio' }, { n:'Marco',l:'Italy',a:'Mbweni Beach Suite' },
    { n:'Emily',l:'Singapore',a:'Paje Ocean Villa' }, { n:'James',l:'Nigeria',a:'Kiembe Samaki Residence' },
    { n:'Anna',l:'Germany',a:'Paje Beachfront Penthouse' }, { n:'David',l:'South Korea',a:'Michenzani City Apartment' },
    { n:'Sophie',l:'France',a:'Paje Ocean Villa' }, { n:'Liam',l:'Australia',a:'Chukwani Garden Studio' }
];
function showRandomToast() { const d = socialProofData[Math.floor(Math.random()*socialProofData.length)]; showToast(d.n,d.l,d.a); }

const heroImages = ['https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=1600&q=80','https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?w=1600&q=80','https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1600&q=80'];
let heroIndex = 0;
function handleHeroImgError(img) { img.style.display = 'none'; }
function changeHeroSlide(dir) {
    const slider = byId('heroSlider');
    if (!slider) return;
    heroIndex += dir;
    if (heroIndex < 0) heroIndex = heroImages.length - 1;
    if (heroIndex >= heroImages.length) heroIndex = 0;
    const img = slider.querySelector('img');
    img.style.opacity = '0';
    setTimeout(() => { img.onerror = function() { handleHeroImgError(this); }; img.src = heroImages[heroIndex]; img.style.display = ''; img.style.opacity = '1'; }, 300);
    document.querySelectorAll('.hero-slider-dots button').forEach((d,i) => d.classList.toggle('active', i === heroIndex));
}

function updateBookingSummary() {
    const ci = byId('checkIn').value;
    const co = byId('checkOut').value;
    if (!ci || !co) return;
    const nights = Math.ceil((new Date(co) - new Date(ci)) / (1000*60*60*24));
    if (nights <= 0) return;
    byId('modalCheckIn').textContent = new Date(ci).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
    byId('modalCheckOut').textContent = new Date(co).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
    byId('modalNights').textContent = nights + ' nights';
}

// --- ALL INIT CODE RUNS DIRECTLY (script is at bottom of body, DOM ready) ---

// Safety: clear any stuck body overflow from a previous interaction
document.body.style.overflow = '';

// Nav toggle
byId('navToggle')?.addEventListener('click', () => toggleNav());
document.querySelectorAll('.nav-links a').forEach(l => l.addEventListener('click', () => { if (window.innerWidth <= 992) toggleNav(true); }));
byId('navOverlay')?.addEventListener('click', () => toggleNav(true));

// Floating Book Now buttons
function scrollToBooking(e) {
    if (e) e.preventDefault();
    const bw = document.querySelector('.booking-widget');
    if (bw) {
        try { bw.scrollIntoView({ behavior: 'smooth', block: 'start' }); } catch (_) { bw.scrollIntoView(true); }
    }
}
byId('stickyBook')?.addEventListener('click', scrollToBooking);

// Modal/lightbox backdrop clicks
byId('apartmentModal')?.addEventListener('click', function(e) { if (e.target === this) closeModal(); });
byId('lightbox')?.addEventListener('click', function(e) { if (e.target === this) closeLightbox(); });
byId('exitPopup')?.addEventListener('click', function(e) { if (e.target === this) closeExitPopup(); });

// Keyboard
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeModal(); closeLightbox(); closeExitPopup(); }
    if (e.key === 'ArrowLeft') changeLightboxImage(-1);
    if (e.key === 'ArrowRight') changeLightboxImage(1);
});

// Exit intent
document.addEventListener('mouseleave', (e) => {
    if (exitFired) return;
    if (e.clientY <= 0) { exitFired = true; byId('exitPopup')?.classList.add('active'); }
});

// Image error fallback
const FALLBACK = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%231A1A1A' width='400' height='300'/%3E%3Ctext x='200' y='140' text-anchor='middle' fill='%23C9A84C' font-size='60' font-family='serif'%3ETUNU%3C/text%3E%3Ctext x='200' y='175' text-anchor='middle' fill='%239CA3AF' font-size='14' font-family='sans-serif' letter-spacing='3'%3EAPARTMENTS%3C/text%3E%3C/svg%3E";
document.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG' && !e.target.hasAttribute('data-fallback')) {
        e.target.setAttribute('data-fallback', '1');
        e.target.src = FALLBACK;
    }
}, true);

// FAQ — event delegation
document.querySelector('.faq-list')?.addEventListener('click', function(e) {
    const q = e.target.closest('.faq-question');
    if (!q) return;
    const item = q.parentElement;
    const active = item.classList.contains('active');
    this.querySelectorAll('.faq-item.active').forEach(el => el.classList.remove('active'));
    if (!active) item.classList.add('active');
});

// Scroll animations
const obs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) { entry.target.classList.add('visible'); obs.unobserve(entry.target); }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
document.querySelectorAll('.animate-on-scroll, .animate-on-scroll-left, .animate-on-scroll-right').forEach(el => obs.observe(el));

// Price calc
byId('checkIn')?.addEventListener('change', updateBookingSummary);
byId('checkOut')?.addEventListener('change', updateBookingSummary);

// Hero slider dots
document.querySelectorAll('.hero-slider-dots button').forEach((btn, i) => {
    btn.addEventListener('click', () => { heroIndex = i; changeHeroSlide(0); });
});
setInterval(() => changeHeroSlide(1), 6000);

// Calendar
renderCalendar();
byId('calendarApartment')?.addEventListener('change', renderCalendar);

// Default dates
const today = new Date();
const tomorrow = new Date(today); tomorrow.setDate(tomorrow.getDate() + 1);
const dayAfter = new Date(today); dayAfter.setDate(dayAfter.getDate() + 2);
const ci = byId('checkIn');
const co = byId('checkOut');
if (ci && !ci.value) { ci.value = tomorrow.toISOString().split('T')[0]; ci.min = today.toISOString().split('T')[0]; }
if (co && !co.value) { co.value = dayAfter.toISOString().split('T')[0]; co.min = tomorrow.toISOString().split('T')[0]; }

// Reviews auto-slide
setInterval(() => slideReviews(1), 5000);

// Social proof
setTimeout(showRandomToast, 8000);
setInterval(() => { setTimeout(showRandomToast, Math.random() * 60000); }, 120000);

// Scroll handler
window.addEventListener('scroll', () => {
    const nav = byId('nav');
    const sy = window.scrollY;
    if (nav) nav.classList.toggle('scrolled', sy > 80);
    const bw = document.querySelector('.booking-widget');
    const hero = document.querySelector('.hero');
    if (hero && bw) bw.classList.toggle('sticky', sy > hero.offsetTop + hero.offsetHeight - 100);
});
