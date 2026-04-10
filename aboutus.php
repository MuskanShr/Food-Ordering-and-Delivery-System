<?php
$pageTitle = 'About Us';
require_once 'includes/auth.php';
include 'includes/header.php';
?>

<style>
.about-hero {
    padding: 4rem 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 3rem;
    background: linear-gradient(135deg, #FFF8F0, #FAF0E6);
    min-height: 420px;
}
.about-img-wrap {
    flex-shrink: 0;
    width: 380px;  
}

.about-img-wrap img {
    width: 480px;   
    height: 480px;  
    object-fit: contain;
    transform: rotate(-8deg);
    filter: drop-shadow(0 20px 40px rgba(0,0,0,0.15));
    transition: transform 0.4s ease;
}


.about-img-wrap img:hover {
    transform: rotate(-4deg) scale(1.03);
}

.about-text-card {
    background: white;
    border-radius: 20px;
    padding: 2rem 2.5rem;
    box-shadow: var(--shadow-lg);
    max-width: 800px;
}

.about-text-card h1 {
    font-family: 'Playfair Display', serif;
    font-size: 1.7rem; font-weight: 800;
    margin-bottom: 1rem;
    color: var(--charcoal);
}

.about-text-card h1 span { color: var(--orange); }

.about-text-card p {
    font-size: 0.9rem;
    color: var(--gray);
    line-height: 1.8;
    margin-bottom: 0.6rem;
}

.about-divider {
    width: 40px; height: 3px;
    background: var(--orange);
    border-radius: 2px;
    margin-bottom: 1rem;
}

.values-section { padding: 4rem 3rem; }
.values-section h2 {
    font-family: 'Playfair Display', serif;
    font-size: 2rem; font-weight: 800;
    text-align: center; margin-bottom: 3rem;
}
.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem; max-width: 900px; margin: 0 auto;
}
.value-card {
    background: white; border-radius: 16px;
    padding: 2rem; text-align: center;
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    transition: all 0.3s;
}
.value-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
.value-num {
    font-size: 2rem; font-weight: 900;
    color: rgba(237, 115, 54, 0.92);
    margin-bottom: 0.8rem;
    font-family: 'Playfair Display', serif;
}
.value-card h3 { font-weight: 700; font-size: 1.05rem; margin-bottom: 0.5rem; }
.value-card p  { font-size: 0.88rem; color: var(--gray); line-height: 1.6; }
</style>

<section class="about-hero">
    <div class="about-img-wrap">
        <img src="/foodbyte/uploads/burger.png" alt="Food">
    </div>

    <div class="about-text-card">
        <h1>About <span>FoodByte</span></h1>
        <div class="about-divider"></div>
        <p>At FoodByte, we believe great food should come to you fast, fresh, and without the fuss — connecting food lovers with the best local restaurants every day.</p>
        <p>We partner with carefully selected restaurants to make sure every order feels like it was made just for you. Our riders are real people committed to getting your meal to your door warm and on time.</p>
        <p>Whether ordering for one or feeding a crowd, FoodByte makes every meal effortless.</p>
    </div>
</section>

<section class="values-section">
    <h2>Why Choose FoodByte?</h2>
    <div class="values-grid">
        <div class="value-card">
            <div class="value-num">01</div>
            <h3>Fast Delivery</h3>
            <p>We prioritize speed without compromising on the quality of your food.</p>
        </div>
        <div class="value-card">
            <div class="value-num">02</div>
            <h3>Quality Partners</h3>
            <p>Every restaurant on our platform is carefully vetted for quality and hygiene.</p>
        </div>
        <div class="value-card">
            <div class="value-num">03</div>
            <h3>Made with Care</h3>
            <p>Our riders treat every delivery like it's for their own family.</p>
        </div>
        <div class="value-card">
            <div class="value-num">04</div>
            <h3>Safe & Secure</h3>
            <p>Your payment and personal information is always protected.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>