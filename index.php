<?php
require_once __DIR__ . '/includes/config.php';
$page_title = SITE_NAME;
$page_desc = SITE_DESC;
require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO SECTION -->
<section class="hero" id="home">
    <div class="hero-bg" id="heroSlider">
        <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=1600&q=80" alt="Tunu Apartments Zanzibar - Luxury Beachfront" loading="eager" onerror="handleHeroImgError(this)">
    </div>
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content animate-on-scroll visible">
            <div class="hero-badge"><i class="fas fa-star"></i> Premier Zanzibar Accommodation</div>
            <h1>Your Perfect Stay in Zanzibar Starts Here.</h1>
            <p>Modern serviced apartments designed for comfort, privacy and unforgettable stays. Book directly with us for the best rates.</p>
            <div class="hero-buttons">
                <a href="#booking" class="btn btn-primary btn-lg"><i class="fas fa-calendar-check"></i> Check Availability</a>
                <a href="#apartments" class="btn btn-outline btn-lg"><i class="fas fa-building"></i> View Apartments</a>
                <a href="https://wa.me/<?= SITE_WHATSAPP ?>?text=Hello%20Tunu%20Apartments!%20I%27d%20like%20to%20inquire%20about%20a%20stay." target="_blank" rel="noopener" class="btn btn-whatsapp btn-lg"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
            </div>
            <div class="hero-trust">
                <div class="hero-trust-item"><i class="fas fa-star"></i> <strong>4.9</strong> (128 reviews)</div>
                <div class="hero-trust-item"><i class="fas fa-headset"></i> <strong>24/7</strong> Guest Support</div>
                <div class="hero-trust-item"><i class="fas fa-lock"></i> <strong>Secure</strong> Booking</div>
                <div class="hero-trust-item"><i class="fas fa-check-circle"></i> <strong>Best Price</strong> Guaranteed</div>
            </div>
        </div>
    </div>
    <div class="hero-slider-dots">
        <button class="active" data-slide="0"></button>
        <button data-slide="1"></button>
        <button data-slide="2"></button>
    </div>
</section>

<!-- BOOKING WIDGET -->
<section class="booking-widget" id="booking">
    <div class="container">
        <div class="booking-widget-inner glass">
            <form class="booking-form" id="bookingForm" onsubmit="return handleBookingSearch(event)">
                <div class="booking-field">
                    <label><i class="fas fa-calendar"></i> Check-in</label>
                    <input type="date" id="checkIn" name="check_in" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="booking-field">
                    <label><i class="fas fa-calendar"></i> Check-out</label>
                    <input type="date" id="checkOut" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                </div>
                <div class="booking-field">
                    <label><i class="fas fa-users"></i> Guests</label>
                    <select id="guests" name="guests">
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'Guest' : 'Guests' ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="booking-field">
                    <label><i class="fas fa-map-marker-alt"></i> Location</label>
                    <select id="location" name="location">
                        <option value="">All Locations</option>
                        <option value="Chukwani">Chukwani</option>
                        <option value="Mbweni">Mbweni</option>
                        <option value="Paje">Paje</option>
                        <option value="Kiembe Samaki">Kiembe Samaki</option>
                        <option value="Michenzani">Michenzani</option>
                    </select>
                </div>
                <button type="submit" class="booking-submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
    </div>
</section>

<!-- FEATURED APARTMENTS -->
<section class="section" id="apartments">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Our Collection</div>
            <h2 class="section-title">Featured Apartments</h2>
            <p class="section-subtitle">Discover our hand-picked selection of premium serviced apartments across Zanzibar's most beautiful locations.</p>
        </div>
        <div class="apartments-grid" id="apartmentsGrid">
            <?php foreach ($apartments as $apt): ?>
            <div class="apartment-card" data-id="<?= $apt['id'] ?>" onclick="openApartmentDetail('<?= $apt['id'] ?>')">
                <div class="apartment-card-image">
                    <img src="<?= $apt['images'][0] ?>" alt="<?= htmlspecialchars($apt['name']) ?> - Tunu Apartments Zanzibar" loading="lazy">
                    <?php if ($apt['featured']): ?>
                    <div class="apartment-card-badge"><i class="fas fa-star"></i> Featured</div>
                    <?php endif; ?>
                    <button class="apartment-card-favorite" onclick="event.stopPropagation();this.querySelector('i').classList.toggle('fas');this.querySelector('i').classList.toggle('far');this.querySelector('i').classList.toggle('fas')?'':''"><i class="far fa-heart"></i></button>
                </div>
                <div class="apartment-card-body">
                    <h3 class="apartment-card-title"><?= htmlspecialchars($apt['name']) ?></h3>
                    <p class="apartment-card-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($apt['location']) ?></p>
                    <div class="apartment-card-price"><?= format_price($apt['price_per_night']) ?> <span>/ night</span></div>
                    <div class="apartment-card-amenities">
                        <span><i class="fas fa-bed"></i> <?= $apt['bedrooms'] ?> Bed</span>
                        <span><i class="fas fa-bath"></i> <?= $apt['bathrooms'] ?> Bath</span>
                        <span><i class="fas fa-users"></i> <?= $apt['max_guests'] ?> Guests</span>
                        <span><i class="fas fa-wifi"></i> WiFi</span>
                        <span><i class="fas fa-car"></i> Parking</span>
                        <span><i class="fas fa-snowflake"></i> A/C</span>
                    </div>
                    <div class="apartment-card-actions">
                        <button class="btn btn-primary btn-sm" onclick="event.stopPropagation();openApartmentDetail('<?= $apt['id'] ?>')"><i class="fas fa-check"></i> Book Now</button>
                        <button class="btn btn-dark btn-sm" onclick="event.stopPropagation();openApartmentDetail('<?= $apt['id'] ?>')">View Details</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- WHY CHOOSE TUNU -->
<section class="section section-beige" id="about">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Why Choose Us</div>
            <h2 class="section-title">The Tunu Experience</h2>
            <p class="section-subtitle">We go beyond accommodation. Every detail is designed to make your Zanzibar stay extraordinary.</p>
        </div>
        <div class="amenities-grid">
            <div class="amenity-card animate-on-scroll visible">
                <div class="amenity-card-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h4>5 Prime Locations</h4>
                <p>Strategically situated across Zanzibar's most sought-after areas — <strong>Chukwani, Mbweni, Paje, Kiembe Samaki &amp; Michenzani</strong>. Whether you want beachfront serenity or city convenience, we have you covered.</p>
            </div>
            <div class="amenity-card animate-on-scroll visible">
                <div class="amenity-card-icon"><i class="fas fa-building"></i></div>
                <h4>Studio to 5-Bedroom</h4>
                <p>From cozy studio apartments for solo travellers to spacious <strong>5-bedroom</strong> homes for families and groups. Every unit is fully furnished with modern interiors and premium appliances.</p>
            </div>
            <div class="amenity-card animate-on-scroll visible">
                <div class="amenity-card-icon"><i class="fas fa-tv"></i></div>
                <h4>Free Netflix &amp; WiFi</h4>
                <p>All apartments come with <strong>high-speed fiber WiFi</strong> and complimentary <strong>Netflix</strong> on smart TVs. Perfect for relaxing after a day exploring Zanzibar or catching up on work.</p>
            </div>
            <div class="amenity-card animate-on-scroll visible">
                <div class="amenity-card-icon"><i class="fas fa-tshirt"></i></div>
                <h4>Washing Machine</h4>
                <p>Each apartment is equipped with a <strong>washing machine</strong> — ideal for long stays. Travel light and fresh, whether you're staying a week or a month.</p>
            </div>
            <div class="amenity-card animate-on-scroll visible">
                <div class="amenity-card-icon"><i class="fas fa-utensils"></i></div>
                <h4>Fully Equipped Kitchen</h4>
                <p>Cook your own meals in modern kitchens with <strong>fridge, stove, oven, microwave</strong> and all cookware. Save money on dining out or hire a <strong>private chef</strong> for a Swahili feast.</p>
            </div>
            <div class="amenity-card animate-on-scroll visible">
                <div class="amenity-card-icon"><i class="fas fa-swimming-pool"></i></div>
                <h4>Pool &amp; Ocean Views</h4>
                <p>Select apartments feature <strong>swimming pool access</strong> and breathtaking <strong>Indian Ocean views</strong>. End your day with a sunset swim — exactly as featured on our Instagram.</p>
            </div>
            <div class="amenity-card animate-on-scroll visible">
                <div class="amenity-card-icon"><i class="fas fa-bolt"></i></div>
                <h4>Generator Backup</h4>
                <p>Never worry about Zanzibar's occasional power interruptions. All locations are equipped with <strong>automatic backup generators</strong> for uninterrupted comfort.</p>
            </div>
            <div class="amenity-card animate-on-scroll visible">
                <div class="amenity-card-icon"><i class="fas fa-plane-arrival"></i></div>
                <h4>Airport Pickup</h4>
                <p>Stress-free arrival with our <strong>reliable airport transfer service</strong>. We'll pick you up from Abeid Amani Karume International Airport and get you settled in no time.</p>
            </div>
            <div class="amenity-card animate-on-scroll visible">
                <div class="amenity-card-icon"><i class="fas fa-headset"></i></div>
                <h4>24/7 Local Support</h4>
                <p>Run by local Tanzanian team led by <strong>Fredy Winston Kachonga</strong>. We're available around the clock to assist with anything — from restaurant recommendations to tour bookings.</p>
            </div>
            <div class="amenity-card animate-on-scroll visible">
                <div class="amenity-card-icon"><i class="fas fa-tag"></i></div>
                <h4>Best Price Guarantee</h4>
                <p>Book directly with us for the <strong>lowest rates</strong> — guaranteed. Plus enjoy <strong>free cancellation</strong> up to 7 days before check-in and discounted rates for weekly and monthly stays.</p>
            </div>
        </div>
    </div>
</section>

<!-- AVAILABILITY CALENDAR -->
<section class="section section-dark" id="availability">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Live Availability</div>
            <h2 class="section-title">Check Real-Time Availability</h2>
            <p class="section-subtitle">Green dates are available, red dates are booked. Select your apartment and find your perfect stay dates.</p>
        </div>
        <div class="calendar-container" style="background:var(--black-soft);margin-top:48px;">
            <div style="display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap;">
                <select id="calendarApartment" style="padding:12px 16px;border:2px solid rgba(255,255,255,0.15);border-radius:var(--radius-sm);background:transparent;color:var(--white);font-size:14px;flex:1;min-width:200px;">
                    <option value="">All Apartments</option>
                    <?php foreach ($apartments as $apt): ?>
                    <option value="<?= $apt['id'] ?>"><?= htmlspecialchars($apt['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div style="display:flex;gap:24px;">
                    <div class="calendar-legend-item" style="color:var(--gray-400);">
                        <div class="calendar-legend-dot available"></div> Available
                    </div>
                    <div class="calendar-legend-item" style="color:var(--gray-400);">
                        <div class="calendar-legend-dot booked"></div> Booked
                    </div>
                </div>
            </div>
            <div class="calendar-header">
                <h3 style="color:var(--white);" id="calendarMonthYear"></h3>
                <div class="calendar-nav">
                    <button onclick="changeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                    <button onclick="changeMonth(1)"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="calendar-grid" id="calendarGrid">
                <div class="calendar-day-header">Sun</div>
                <div class="calendar-day-header">Mon</div>
                <div class="calendar-day-header">Tue</div>
                <div class="calendar-day-header">Wed</div>
                <div class="calendar-day-header">Thu</div>
                <div class="calendar-day-header">Fri</div>
                <div class="calendar-day-header">Sat</div>
            </div>
            <div class="calendar-legend" style="border-top-color:rgba(255,255,255,0.08);">
                <div class="calendar-legend-item" style="color:var(--gray-400);">
                    <div class="calendar-legend-dot available"></div> Available
                </div>
                <div class="calendar-legend-item" style="color:var(--gray-400);">
                    <div class="calendar-legend-dot booked"></div> Booked
                </div>
                <div class="calendar-legend-item" style="color:var(--gray-400);">
                    <div class="calendar-legend-dot" style="background:var(--gold);"></div> Today
                </div>
            </div>
        </div>
    </div>
</section>

<!-- GUEST REVIEWS -->
<section class="section" id="reviews">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Testimonials</div>
            <h2 class="section-title">What Our Guests Say</h2>
            <p class="section-subtitle">Real reviews from real guests. We take pride in every stay.</p>
        </div>
        <div class="reviews-slider" id="reviewsSlider">
            <div class="reviews-track" id="reviewsTrack">
                <?php foreach ($reviews as $rev): ?>
                <div class="review-card">
                    <div class="review-card-header">
                        <img class="review-card-avatar" src="<?= $rev['guest_image'] ?>" alt="<?= htmlspecialchars($rev['guest_name']) ?>" loading="lazy">
                        <div class="review-card-info">
                            <h4><?= htmlspecialchars($rev['guest_name']) ?></h4>
                            <span><i class="fas fa-map-marker-alt" style="color:var(--gold);"></i> <?= htmlspecialchars($rev['country']) ?></span>
                            <span class="review-card-verified"><i class="fas fa-check-circle"></i> Verified Stay</span>
                        </div>
                    </div>
                    <div class="review-card-stars">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                        <i class="<?= $i < $rev['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="review-card-text"><?= htmlspecialchars($rev['text']) ?></p>
                    <div class="review-card-date"><?= date('F Y', strtotime($rev['date'])) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="reviews-nav">
            <button onclick="slideReviews(-1)" aria-label="Previous reviews"><i class="fas fa-arrow-left"></i></button>
            <button onclick="slideReviews(1)" aria-label="Next reviews"><i class="fas fa-arrow-right"></i></button>
        </div>
    </div>
</section>

<!-- GALLERY -->
<section class="section section-beige" id="gallery">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Gallery</div>
            <h2 class="section-title">A Visual Journey</h2>
            <p class="section-subtitle">Explore the beauty of Tunu Apartments through our curated photo collection.</p>
        </div>
        <div class="gallery-grid" id="galleryGrid">
            <div class="gallery-item" onclick="openLightbox(0)">
                <img src="/assets/images/gallery-1.jpg" alt="Tunu Apartments - Modern Living Room" loading="lazy">
                <div class="gallery-item-overlay"><i class="fas fa-expand"></i></div>
            </div>
            <div class="gallery-item" onclick="openLightbox(1)">
                <img src="/assets/images/gallery-2.jpg" alt="Tunu Apartments - Cozy Interior" loading="lazy">
                <div class="gallery-item-overlay"><i class="fas fa-expand"></i></div>
            </div>
            <div class="gallery-item" onclick="openLightbox(2)">
                <img src="/assets/images/gallery-3.jpg" alt="Tunu Apartments - Modern Design" loading="lazy">
                <div class="gallery-item-overlay"><i class="fas fa-expand"></i></div>
            </div>
            <div class="gallery-item" onclick="openLightbox(3)">
                <video src="/assets/videos/gallery-video-2.mp4" muted loop playsinline preload="metadata" loading="lazy"></video>
                <div class="gallery-item-play"><i class="fas fa-play"></i></div>
                <div class="gallery-item-overlay"><i class="fas fa-expand"></i></div>
            </div>
            <div class="gallery-item" onclick="openLightbox(4)">
                <video src="/assets/videos/gallery-video-3.mp4" muted loop playsinline preload="metadata" loading="lazy"></video>
                <div class="gallery-item-play"><i class="fas fa-play"></i></div>
                <div class="gallery-item-overlay"><i class="fas fa-expand"></i></div>
            </div>
            <div class="gallery-item" onclick="openLightbox(5)">
                <video src="/assets/videos/gallery-video-4.mp4" muted loop playsinline preload="metadata" loading="lazy"></video>
                <div class="gallery-item-play"><i class="fas fa-play"></i></div>
                <div class="gallery-item-overlay"><i class="fas fa-expand"></i></div>
            </div>
        </div>
    </div>
</section>

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox">
    <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
    <button class="lightbox-prev" onclick="changeLightboxImage(-1)"><i class="fas fa-chevron-left"></i></button>
    <img id="lightboxImage" src="" alt="" style="display:none;">
    <video id="lightboxVideo" src="" controls playsinline style="display:none;max-width:90%;max-height:90%;border-radius:12px;"></video>
    <button class="lightbox-next" onclick="changeLightboxImage(1)"><i class="fas fa-chevron-right"></i></button>
    <div class="lightbox-counter" id="lightboxCounter"></div>
</div>

<!-- LOCATION -->
<section class="section" id="location">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Find Us</div>
            <h2 class="section-title">Our Locations</h2>
            <p class="section-subtitle">Perfectly situated across Zanzibar's most desirable destinations. Click a marker to explore.</p>
        </div>
        <div class="location-map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d505966.0894427969!2d39.14853735!3d-6.16539255!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x185cd123456789ab%3A0x1234567890abcdef!2sZanzibar%2C+Tanzania!5e0!3m2!1sen!2sus!4v1" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Tunu Apartments Zanzibar Locations"></iframe>
        </div>
    </div>
</section>

<!-- BOOKING PROCESS -->
<section class="section section-beige">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">How It Works</div>
            <h2 class="section-title">Book in 3 Simple Steps</h2>
            <p class="section-subtitle">Reserving your perfect Zanzibar apartment is quick and easy.</p>
        </div>
        <div class="process-grid" style="margin-top:48px;">
            <div class="process-card animate-on-scroll visible">
                <div class="process-card-number">1</div>
                <div class="process-card-icon"><i class="fas fa-building"></i></div>
                <h4>Browse &amp; Select</h4>
                <p>Explore our collection of premium serviced apartments across Zanzibar. Use the <strong>booking widget</strong> below to filter by location, check real-time availability, and compare prices — all in one place.</p>
                <div class="process-arrow"><i class="fas fa-arrow-right"></i></div>
                <a href="#apartments" class="btn btn-sm btn-outline-dark" style="margin-top:16px;display:inline-block;">View Apartments &rarr;</a>
            </div>
            <div class="process-card animate-on-scroll visible">
                <div class="process-card-number">2</div>
                <div class="process-card-icon"><i class="fas fa-calendar-alt"></i></div>
                <h4>Pick Your Dates</h4>
                <p>Choose check-in and check-out dates using our interactive <strong>availability calendar</strong>. The system automatically calculates the total cost, including any long-stay discounts for weekly or monthly bookings.</p>
                <div class="process-arrow"><i class="fas fa-arrow-right"></i></div>
                <a href="#booking" class="btn btn-sm btn-outline-dark" style="margin-top:16px;display:inline-block;">Check Availability &rarr;</a>
            </div>
            <div class="process-card animate-on-scroll visible">
                <div class="process-card-number">3</div>
                <div class="process-card-icon"><i class="fas fa-check-circle"></i></div>
                <h4>Book &amp; Relax</h4>
                <p>Fill in your details and pay securely via <strong>credit card, M-Pesa, Airtel Money, or bank transfer</strong>. Your booking is confirmed instantly and you'll receive a confirmation email with all the details.</p>
                <a href="#booking" class="btn btn-sm btn-primary" style="margin-top:16px;display:inline-block;">Book Now &rarr;</a>
            </div>
        </div>
        <div style="text-align:center;margin-top:48px;">
            <a href="#booking" class="btn btn-primary btn-lg"><i class="fas fa-calendar-check"></i> Start Your Booking</a>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="section" id="faq">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">FAQ</div>
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Everything you need to know before booking your stay at Tunu Apartments.</p>
        </div>
        <div class="faq-list">
            <div class="faq-item active">
                <div class="faq-question" >
                    <span>Can I pay online?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">Yes! We accept Visa, Mastercard, American Express, Apple Pay, Google Pay, M-Pesa, Airtel Money, Tigo Pesa, and bank transfers. Your payment is processed securely and your booking is confirmed instantly.</div>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question" >
                    <span>Do you provide airport pickup?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">Absolutely. We offer airport pickup and drop-off services from Abeid Amani Karume International Airport. Simply let us know your flight details when booking, and our driver will be waiting for you.</div>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question" >
                    <span>Can I cancel my booking?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">Yes, we offer free cancellation up to <?= CANCELLATION_DAYS ?> days before check-in. Cancellations within <?= CANCELLATION_DAYS ?> days may incur a fee. Please refer to our cancellation policy for full details.</div>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question" >
                    <span>Is WiFi included?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">All our apartments come with complimentary high-speed fiber WiFi. The connection is reliable and suitable for video calls, streaming, and remote work.</div>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question" >
                    <span>Do you offer long stays?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">Yes! We offer discounted rates for weekly and monthly stays. Long-term guests enjoy additional amenities including weekly cleaning, laundry service, and dedicated support. Contact us for custom pricing.</div>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question" >
                    <span>Is there a security deposit?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <div class="faq-answer-inner">A refundable security deposit of $100 is required at check-in. This is fully refunded upon check-out provided there is no damage to the property or its contents.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CONTACT -->
<section class="section section-beige" id="contact-section">
    <div class="container">
        <div class="section-header">
            <div class="section-tag">Get in Touch</div>
            <h2 class="section-title">Contact Us</h2>
            <p class="section-subtitle">Have a question? We'd love to hear from you. Send us a message and we'll respond promptly.</p>
        </div>
        <div class="contact-grid">
            <div class="contact-info">
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <h4>Phone</h4>
                        <p><a href="tel:<?= SITE_PHONE ?>"><?= SITE_PHONE ?></a></p>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fab fa-whatsapp"></i></div>
                    <div>
                        <h4>WhatsApp</h4>
                        <p><a href="https://wa.me/<?= SITE_WHATSAPP ?>" target="_blank" rel="noopener">Chat with us on WhatsApp</a></p>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fab fa-instagram"></i></div>
                    <div>
                        <h4>Instagram</h4>
                        <p><a href="https://instagram.com/<?= SITE_INSTAGRAM ?>" target="_blank" rel="noopener">@<?= SITE_INSTAGRAM ?></a></p>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="far fa-envelope"></i></div>
                    <div>
                        <h4>Email</h4>
                        <p><a href="mailto:<?= SITE_EMAIL ?>"><?= SITE_EMAIL ?></a></p>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <h4>Address</h4>
                        <p><?= SITE_ADDRESS ?></p>
                    </div>
                </div>
            </div>
            <div class="contact-form">
                <h3>Send Us a Message</h3>
                <form id="contactForm" onsubmit="return handleContactForm(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contactName">Full Name</label>
                            <input type="text" id="contactName" placeholder="Your name" required>
                        </div>
                        <div class="form-group">
                            <label for="contactEmail">Email Address</label>
                            <input type="email" id="contactEmail" placeholder="your@email.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contactSubject">Subject</label>
                        <select id="contactSubject">
                            <option value="Booking Inquiry">Booking Inquiry</option>
                            <option value="General Question">General Question</option>
                            <option value="Long Stay Request">Long Stay Request</option>
                            <option value="Group Booking">Group Booking</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contactMessage">Message</label>
                        <textarea id="contactMessage" placeholder="Tell us how we can help..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="fas fa-paper-plane"></i> Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
