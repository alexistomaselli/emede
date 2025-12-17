<?php
// Template: Start (Inicio)
// Content: Intro Text, Stats (1-4), Testimonials (1-4)
// Note: Hero title/desc still comes from Global Settings for consistency with header, 
// but we could override it here if we wanted. For now, let's keep Hero structure here but use Global vars.
$data = json_decode($page['content'], true) ?? [];
?>

<!-- SECTION: HERO -->
<section id="inicio" class="hero">
    <div class="container hero-container">
        <div class="hero-content">
            <span class="subtitle"><?php echo htmlspecialchars(get_setting('site_title')); ?></span>
            <h1 class="title"><?php echo get_setting('hero_title'); ?></h1>
            <p class="description"><?php echo htmlspecialchars(get_setting('hero_desc')); ?></p>
            <div class="hero-btns">
                <a href="#contacto" class="btn btn-primary"><?php echo htmlspecialchars(get_setting('hero_cta')); ?></a>
                <a href="#trayectoria" class="btn btn-outline">Conocé más</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="extracted_assets/image_p1_0.png" alt="Hero Image"
                onerror="this.style.display='none'; document.getElementById('hero-placeholder').style.display='flex'">
            <div id="hero-placeholder" class="img-placeholder" style="display:none;">Imagen Principal</div>
        </div>
    </div>
</section>

<!-- SECTION: INTRO & STATS -->
<section class="features section-padding">
    <div class="container text-center mb-5">
        <span class="subtitle"><?php echo htmlspecialchars($data['intro_title'] ?? 'Sobre nosotros'); ?></span>
        <p class="max-w-600 mx-auto mt-3"><?php echo htmlspecialchars($data['intro_text'] ?? ''); ?></p>
    </div>

    <div class="container features-grid">
        <?php for ($i = 1; $i <= 4; $i++): ?>
            <?php if (!empty($data["stat_{$i}_number"])): ?>
                <div class="feature-card <?php echo $i == 2 ? 'active' : ''; ?>">
                    <div class="icon-box"><i
                            class="<?php echo htmlspecialchars($data["stat_{$i}_icon"] ?? 'fas fa-star'); ?>"></i></div>
                    <h3><?php echo htmlspecialchars($data["stat_{$i}_number"]); ?></h3>
                    <p><?php echo htmlspecialchars($data["stat_{$i}_label"]); ?></p>
                </div>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
</section>

<!-- SECTION: TESTIMONIALS -->
<section class="testimonials section-padding bg-light">
    <div class="container text-center">
        <span class="subtitle">Reseñas</span>
        <h2>Lo que dicen nuestros clientes</h2>
        <div class="testimonials-grid mt-5">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <?php if (!empty($data["testimonial_{$i}_text"])): ?>
                    <div class="testimonial-item">
                        <p>"<?php echo htmlspecialchars($data["testimonial_{$i}_text"]); ?>"</p>
                        <?php if (!empty($data["testimonial_{$i}_author"])): ?>
                            <h4><?php echo htmlspecialchars($data["testimonial_{$i}_author"]); ?></h4>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>
</section>