<footer class="footer" id="contact">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="footer-logo-wrap">
                        <img src="/assets/images/logo.jpg" alt="Tunu Apartments Zanzibar" class="footer-logo-img">
                        <div>
                            <span style="font-family:var(--font-heading);font-size:28px;color:var(--gold);display:block;margin-bottom:2px;">TUNU</span>
                            <span style="font-size:11px;color:var(--gold);letter-spacing:3px;display:block;">APARTMENTS ZANZIBAR</span>
                        </div>
                    </div>
                    <p style="margin-top:16px;">Modern serviced apartments designed for comfort, privacy and unforgettable stays. Experience the best of Zanzibar with Tunu Apartments.</p>
                    <div class="footer-social">
                        <a href="https://instagram.com/<?= SITE_INSTAGRAM ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://facebook.com/tunuapartments" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://wa.me/<?= SITE_WHATSAPP ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="mailto:<?= SITE_EMAIL ?>" aria-label="Email"><i class="far fa-envelope"></i></a>
                    </div>
                </div>
                <div>
                    <h4>Quick Links</h4>
                    <div class="footer-links">
                        <a href="/">Home</a>
                        <a href="#apartments">Our Apartments</a>
                        <a href="#availability">Availability</a>
                        <a href="#gallery">Gallery</a>
                        <a href="#reviews">Guest Reviews</a>
                        <a href="#about">About Us</a>
                        <a href="#contact">Contact</a>
                    </div>
                </div>
                <div>
                    <h4>Support</h4>
                    <div class="footer-links">
                        <a href="#faq">FAQ</a>
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms & Conditions</a>
                        <a href="#">Cancellation Policy</a>
                        <a href="#">Booking Guide</a>
                    </div>
                </div>
                <div>
                    <h4>Newsletter</h4>
                    <div class="footer-newsletter">
                        <p>Subscribe for exclusive offers, travel tips, and updates from Zanzibar.</p>
                        <form class="footer-newsletter-form" onsubmit="event.preventDefault();alert('Thank you for subscribing!');this.reset();">
                            <input type="email" placeholder="Enter your email" required>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-arrow-right"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Tunu Apartments Zanzibar. All rights reserved.</p>
                <div class="footer-bottom-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=Hello%20Tunu%20Apartments!%20I%27d%20like%20to%20make%20a%20reservation." target="_blank" rel="noopener" class="sticky-whatsapp" aria-label="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <a href="#booking" class="sticky-book" id="stickyBook">
        <i class="fas fa-calendar-check"></i> Book Now
    </a>

    <div class="toast-container" id="toastContainer"></div>

    <div class="exit-popup" id="exitPopup">
        <div class="exit-popup-content">
            <button class="exit-popup-close" onclick="closeExitPopup()">&times;</button>
            <div class="exit-popup-offer">-15% OFF</div>
            <h3>Don't Miss Out on Your Dream Stay!</h3>
            <p>Book now and get 15% off your first stay at Tunu Apartments. Limited time offer for direct bookings.</p>
            <a href="#booking" class="btn btn-primary btn-lg" onclick="closeExitPopup();document.querySelector('.booking-widget').scrollIntoView({behavior:'smooth'})"><i class="fas fa-calendar-check"></i> Book Now & Save 15%</a>
        </div>
    </div>

    <!-- APARTMENT DETAIL MODAL -->
    <div class="modal-overlay" id="apartmentModal">
        <div class="modal">
            <div class="modal-header">
                <img id="modalImage" src="" alt="">
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <h2 class="modal-title" id="modalTitle"></h2>
                <p class="modal-location" id="modalLocation" style="font-size:14px;color:var(--gray-500);margin-bottom:8px;"><i class="fas fa-map-marker-alt" style="color:var(--gold);"></i> <span id="modalLocationText"></span></p>
                <div class="modal-price"><span id="modalPrice"></span> <span>/ night</span></div>
                <p class="modal-description" id="modalDescription"></p>
                <div class="modal-amenities-grid" id="modalAmenities"></div>
                <div class="modal-map" id="modalMap"></div>
                <div class="modal-booking-summary">
                    <h4>Book This Apartment</h4>
                    <div class="modal-booking-dates">
                        <div class="modal-booking-field">
                            <label>Check-in</label>
                            <input type="date" id="modalCheckInInput" class="modal-date-input" min="<?= date('Y-m-d') ?>" onchange="updateModalSummary()">
                        </div>
                        <div class="modal-booking-field">
                            <label>Check-out</label>
                            <input type="date" id="modalCheckOutInput" class="modal-date-input" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" onchange="updateModalSummary()">
                        </div>
                    </div>
                    <div class="modal-booking-row"><span>Nights</span><span id="modalNights">-</span></div>
                    <div class="modal-booking-row modal-booking-total"><span>Total</span><span id="modalTotal">-</span></div>
                    <div id="modalBookingForm" style="display:none;">
                        <div class="modal-booking-field" style="margin-bottom:12px;">
                            <label>Your Name</label>
                            <input type="text" id="modalGuestName" class="modal-date-input" placeholder="Enter your full name">
                        </div>
                        <div class="modal-booking-field" style="margin-bottom:12px;">
                            <label>Email Address</label>
                            <input type="email" id="modalGuestEmail" class="modal-date-input" placeholder="your@email.com">
                        </div>
                        <div class="modal-booking-field" style="margin-bottom:12px;">
                            <label>Phone (optional)</label>
                            <input type="tel" id="modalGuestPhone" class="modal-date-input" placeholder="+255 7XX XXX XXX">
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block btn-lg" style="margin-top:16px;" id="modalBookingBtn" onclick="handleBookingFromModal()"><i class="fas fa-check-circle"></i> Confirm Reservation</button>
                    <div id="modalBookingStatus" style="display:none;margin-top:12px;padding:12px;border-radius:8px;text-align:center;font-size:14px;"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
    const apartments = <?= json_encode($apartments) ?>;
    const bookingsData = <?= json_encode($bookings) ?>;
    </script>
    <script src="/js/main.js"></script>
</body>
</html>
