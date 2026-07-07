<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($page_desc ?? SITE_DESC) ?>">
    <meta name="keywords" content="Tunu Apartments, Zanzibar apartments, Chukwani apartments, Mbweni apartments, Paje apartments, Kiembe Samaki, Michenzani, book Zanzibar online, serviced apartments Zanzibar, Zanzibar rental">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Tunu Apartments Zanzibar">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_desc ?? SITE_DESC) ?>">
    <meta property="og:url" content="<?= SITE_URL ?>">
    <meta property="og:site_name" content="<?= SITE_NAME ?>">
    <meta property="og:locale" content="en_US">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($page_desc ?? SITE_DESC) ?>">
    <link rel="canonical" href="<?= SITE_URL ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=1600&q=80" fetchpriority="high">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="icon" type="image/jpeg" href="/assets/images/logo.jpg">
    <link rel="apple-touch-icon" href="/assets/images/logo.jpg">
    <title><?= htmlspecialchars($page_title) ?></title>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {"@type": "Question", "name": "Can I pay online?", "acceptedAnswer": {"@type": "Answer", "text": "Yes! We accept Visa, Mastercard, American Express, Apple Pay, Google Pay, M-Pesa, Airtel Money, Tigo Pesa, and bank transfers."}},
            {"@type": "Question", "name": "Do you provide airport pickup?", "acceptedAnswer": {"@type": "Answer", "text": "Absolutely. We offer airport pickup and drop-off services from Abeid Amani Karume International Airport."}},
            {"@type": "Question", "name": "Can I cancel my booking?", "acceptedAnswer": {"@type": "Answer", "text": "Yes, we offer free cancellation up to 7 days before check-in."}},
            {"@type": "Question", "name": "Is WiFi included?", "acceptedAnswer": {"@type": "Answer", "text": "All our apartments come with complimentary high-speed fiber WiFi."}},
            {"@type": "Question", "name": "Do you offer long stays?", "acceptedAnswer": {"@type": "Answer", "text": "Yes! We offer discounted rates for weekly and monthly stays."}}
        ]
    }
    </script>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LodgingBusiness",
        "name": "Tunu Apartments Zanzibar",
        "image": "<?= SITE_URL ?>/assets/images/logo.jpg",
        "description": "<?= SITE_DESC ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Chukwani",
            "addressRegion": "Zanzibar",
            "addressCountry": "TZ"
        },
        "telephone": "<?= SITE_PHONE ?>",
        "priceRange": "$$",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.9",
            "reviewCount": "128",
            "bestRating": "5"
        },
        "amenityFeature": [
            {"@type": "LocationFeatureSpecification", "name": "Free WiFi"},
            {"@type": "LocationFeatureSpecification", "name": "Air Conditioning"},
            {"@type": "LocationFeatureSpecification", "name": "Free Parking"},
            {"@type": "LocationFeatureSpecification", "name": "Airport Shuttle"},
            {"@type": "LocationFeatureSpecification", "name": "Generator Backup"}
        ],
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Apartment Rentals",
            "itemListElement": [
                {"@type": "Offer", "itemOffered": {"@type": "Apartment", "name": "Chukwani Garden Studio", "numberOfBedrooms": 1}},
                {"@type": "Offer", "itemOffered": {"@type": "Apartment", "name": "Mbweni Beach Suite", "numberOfBedrooms": 2}},
                {"@type": "Offer", "itemOffered": {"@type": "Apartment", "name": "Paje Ocean Villa", "numberOfBedrooms": 3}}
            ]
        }
    }
    </script>
</head>
<body>
    <div class="nav-overlay" id="navOverlay"></div>

    <nav class="nav" id="nav">
        <div class="container">
            <a href="/" class="nav-logo">
                <img src="/assets/images/logo.jpg" alt="Tunu Apartments Zanzibar" class="nav-logo-img">
                <span class="nav-logo-text">TUNU <small>APARTMENTS</small></span>
            </a>
            <div class="nav-links" id="navLinks">
                <a href="/">Home</a>
                <a href="#apartments">Apartments</a>
                <a href="#availability">Availability</a>
                <a href="#gallery">Gallery</a>
                <a href="#about">About</a>
                <a href="#reviews">Reviews</a>
                <a href="#contact">Contact</a>
                <a href="#booking" class="nav-cta"><i class="fas fa-calendar-check"></i> Book Now</a>
            </div>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>
