<?php
// Template: About (Trayectoria)
// Fields: history_title, history_text, mission_title, mission_text, image_url
$data = json_decode($page['content'], true) ?? [];
?>
<section class="section-padding">
    <div class="container grid-2-col align-center">
        <div class="content-block">
            <span class="subtitle">Nuestra Historia</span>
            <h2><?php echo htmlspecialchars($data['history_title'] ?? ''); ?></h2>
            <div class="dynamic-content">
                <p><?php echo nl2br(htmlspecialchars($data['history_text'] ?? '')); ?></p>
            </div>

            <?php if (!empty($data['mission_title'])): ?>
                <div class="policy-box mt-4">
                    <h3><?php echo htmlspecialchars($data['mission_title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($data['mission_text'] ?? '')); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <div class="image-block">
            <?php if (!empty($data['image_url'])): ?>
                <img src="<?php echo htmlspecialchars($data['image_url']); ?>" alt="Trayectoria" class="rounded-img">
            <?php else: ?>
                <div class="img-placeholder">Imagen</div>
            <?php endif; ?>
        </div>
    </div>
</section>