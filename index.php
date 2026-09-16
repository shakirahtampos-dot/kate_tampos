<?php
$navLinks = ['Menu', 'About', 'Order','Login'];

$products = [
    [
        'name' => 'Butter Croissant',
        'desc' => 'Laminated with cultured butter, 72-hour cold proof, flaky on the outside — impossibly soft within.',
        'price' => '$4.50',
        'tag' => 'Bestseller',
        'img' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=600&h=700&fit=crop&auto=format'
    ],
    [
        'name' => 'Morning Pastry Box',
        'desc' => 'A curated selection of four freshly baked pastries — different every morning, always made with care.',
        'price' => '$18.00',
        'tag' => 'Daily Pick',
        'img' => 'https://images.unsplash.com/photo-1483695028939-5bb13f8648b0?w=600&h=700&fit=crop&auto=format'
    ],
    [
        'name' => 'Country Sourdough',
        'desc' => 'Stone-milled flour, wild starter, wood-fired crust. Baked before dawn so it is warm when you arrive.',
        'price' => '$12.00',
        'tag' => 'Limited',
        'img' => 'https://images.unsplash.com/photo-1591458736923-c06a260e3412?w=600&h=700&fit=crop&auto=format'
    ]
];

$values = [
    ['label' => 'Honesty', 'detail' => 'No hidden ingredients. No shortcuts. Just real food.'],
    ['label' => 'Warmth', 'detail' => 'Every loaf is made like it is going to someone we love.'],
    ['label' => 'Craft', 'detail' => 'Techniques perfected over years, never rushed, never automated.'],
    ['label' => 'Community', 'detail' => 'We source local, hire local, and give back to the neighbourhood.']
];

$testimonials = [
    ['quote' => 'The croissant changed my entire morning routine. It is the first thing I think about when I wake up.', 'name' => 'Sarah M.', 'role' => 'Regular since 2022'],
    ['quote' => "Kate's Goodies is the only bakery where I actually feel the love in every single bite. Never going anywhere else.", 'name' => 'Daniel R.', 'role' => 'South Melbourne local'],
    ['quote' => 'I ordered the morning box for my team and everyone went quiet for five minutes. That says everything.', 'name' => 'Priya K.', 'role' => 'Office regular']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kates Goodies</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <a href="#" class="brand">
            <span class="logo-mark">Kates</span>
            <span class="brand-name">Kates <span>Goodies</span></span>
        </a>

     <div class="desktop-nav">
    <?php foreach ($navLinks as $link): ?>
        <a href="<?= $link === 'Menu' ? '#menu' : ($link === 'About' ? '#about' : ($link === 'Order' ? '#order' : ($link === 'Login' ? 'login.php' : '#'))) ?>">
            <?= htmlspecialchars($link) ?>
        </a>
    <?php endforeach; ?>

    <a href="register.php" class="nav-button">Order Now</a>
</div>

        <input type="checkbox" id="menu-toggle">
        <label for="menu-toggle" class="hamburger" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </label>
    </div>

  <div class="mobile-nav">
    <?php foreach ($navLinks as $link): ?>
        <a href="<?= $link === 'Menu' ? '#menu' : ($link === 'About' ? '#about' : ($link === 'Order' ? '#order' : ($link === 'Login' ? 'login.php' : '#'))) ?>">
            <?= htmlspecialchars($link) ?>
        </a>
    <?php endforeach; ?>

    <a href="register.php" class="nav-button">Order Now</a>
</div>
</nav>

<section class="hero">
    <div class="decor-circle circle-one"></div>
    <div class="decor-circle circle-two"></div>
    <div class="decor-circle circle-three"></div>

    <div class="hero-container">
        <div class="hero-content">
            <p class="eyebrow">01 — Freshly Baked Everyday</p>
            <h1>Pure<br>Ingredients.</h1>
            <h1 class="accent-heading">Pure<br>Moments.</h1>
            <p class="hero-text">
                Kate's Goodies was born from a simple belief: baked goods made with care and honesty taste different.
                Premium local ingredients, zero preservatives, baked fresh before sunrise.
            </p>

            <div class="hero-buttons">
                <a href="#menu" class="button primary-button">See the Menu</a>
                <a href="#about" class="button secondary-button">Our Story</a>
            </div>
        </div>

        <div class="hero-image-wrapper">
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1623334044303-241021148842?w=800&h=800&fit=crop&auto=format" alt="Golden flaky croissants fresh from the oven">
            </div>
            <div class="love-badge">"Made with so much love."</div>
            <div class="reviews-badge">
                <strong>5★</strong>
                <span>300+ Reviews</span>
            </div>
        </div>
    </div>
</section>

<div class="marquee">
    <div class="marquee-track">
        Freshly Baked Everyday · No Preservatives · Local Ingredients · Pure Moments · Handcrafted with Love · Order Before 10am ·
        &nbsp;&nbsp;&nbsp;
        Freshly Baked Everyday · No Preservatives · Local Ingredients · Pure Moments · Handcrafted with Love · Order Before 10am ·
    </div>
</div>

<section id="menu" class="products-section">
    <div class="section-heading">
        <p class="eyebrow">02 — Today's Bakes</p>
        <div class="heading-row">
            <h2>Made This<br>Morning</h2>
            <a href="#" class="text-link">Full Menu →</a>
        </div>
    </div>

    <div class="products-grid">
        <?php foreach ($products as $product): ?>
            <article class="product-card">
                <div class="product-image">
                    <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
                <div class="product-content">
                    <div class="product-top">
                        <span class="product-tag"><?= htmlspecialchars($product['tag']) ?></span>
                        <span class="product-price"><?= htmlspecialchars($product['price']) ?></span>
                    </div>
                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                    <p><?= htmlspecialchars($product['desc']) ?></p>
                    <button class="add-button">Add to Order</button>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section id="about" class="about-section">
    <div class="about-container">
        <div class="about-images">
            <div class="main-about-image">
                <img src="https://images.unsplash.com/photo-1528699633788-424224dc89b5?w=700&h=900&fit=crop&auto=format" alt="Bread and coffee on a warm morning at the bakery">
            </div>
            <div class="small-about-image">
                <img src="https://images.unsplash.com/photo-1534432182912-63863115e106?w=400&h=300&fit=crop&auto=format" alt="Freshly baked pastry close up">
            </div>
            <div class="accent-square"></div>
        </div>

        <div class="about-content">
            <p class="eyebrow">02 — Our Mission</p>
            <h2>The Bakery<br>That Feels<br><em>Like Home.</em></h2>
            <p class="about-text">
                We source premium local ingredients, skip every preservative, and bake fresh each morning —
                because you deserve something real. Every item that leaves our kitchen carries a piece of us in it.
            </p>

            <div class="values-grid">
                <?php foreach ($values as $value): ?>
                    <div class="value">
                        <p class="value-label"><?= htmlspecialchars($value['label']) ?></p>
                        <p><?= htmlspecialchars($value['detail']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="testimonials-section">
    <div class="section-container">
        <p class="eyebrow centered">03 — What People Say</p>
        <h2 class="centered-title">Real People. Real Moments.</h2>

        <div class="testimonials-grid">
            <?php foreach ($testimonials as $index => $testimonial): ?>
                <article class="testimonial <?= $index === 1 ? 'featured-testimonial' : '' ?>">
                    <p class="quote">"<?= htmlspecialchars($testimonial['quote']) ?>"</p>
                    <div class="testimonial-author">
                        <p><?= htmlspecialchars($testimonial['name']) ?></p>
                        <span><?= htmlspecialchars($testimonial['role']) ?></span>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="order" class="order-section">
    <div class="order-container">
        <p class="eyebrow order-eyebrow">04 — Place Your Order</p>
        <h2>Order Before<br>10am, Pick Up<br>by Noon.</h2>
        <p>
            Pre-order your favourites and skip the queue. Everything is made to order —
            so it is as fresh as it gets.
        </p>
        <a href="#" class="order-button">Start Your Order</a>
    </div>
</section>

<footer class="footer" id="footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="brand footer-logo">
                    <span class="logo-mark">Kates</span>
                    <span class="brand-name">Kates <span>Goodies</span></span>
                </div>
                <p>Freshly baked every morning with premium local ingredients and zero preservatives. The bakery that feels like home.</p>
            </div>

            <div>
                <p class="footer-heading">Explore</p>
                <p>Menu</p>
                <p>Pre-Order</p>
                <p>Gift Boxes</p>
                <p>Catering</p>
            </div>

            <div>
                <p class="footer-heading">Visit</p>
                <p>12 Baker St, South Melbourne</p>
                <p>Mon–Fri 6am – 2pm</p>
                <p>Sat–Sun 7am – 1pm</p>
            </div>

            <div>
                <p class="footer-heading">Follow</p>
                <p>Instagram</p>
                <p>Facebook</p>
                <p>TikTok</p>
                <p>Newsletter</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2025 Kates Goodies. All rights reserved.</p>
            <p>Honesty · Warmth · Craft · Community · Sustainability</p>
        </div>
    </div>
</footer>

</body>
</html>
