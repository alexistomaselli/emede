<?php
// Template: Services (Packaging, Posavasos, Comercial)
// Fields: subtitle, intro, service_1_title/image, service_2..., service_3..., extra_title_1/text, extra_title_2/text
$data = json_decode($page['content'], true) ?? [];
?>
<section class="section-padding bg-light">
    <div class="container">
        <div class="section-header text-center">
            <span class="subtitle text-primary"><?php echo htmlspecialchars($data['subtitle'] ?? 'Servicios'); ?></span>
            <h2><?php echo htmlspecialchars($page['title']); ?></h2>
            <p class="max-w-600 mx-auto"><?php echo nl2br(htmlspecialchars($data['intro'] ?? '')); ?></p>
        </div>

        <!-- Service Grid -->
        <div class="grid-3-col mt-5">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <?php if (!empty($data["service_{$i}_title"])): ?>
                    <div class="service-box">
                        <?php if (!empty($data["service_{$i}_image"])): ?>
                            <img src="<?php echo htmlspecialchars($data["service_{$i}_image"]); ?>" alt="Service <?php echo $i; ?>">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($data["service_{$i}_title"]); ?></h3>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>

        <!-- Extra Info (Optional) -->
        <?php if (!empty($data['extra_title_1'])): ?>
            <div class="grid-2-col mt-5">
                <div class="info-card">
                    <h3><i class="fas fa-handshake"></i> <?php echo htmlspecialchars($data['extra_title_1']); ?></h3>
                    <p><?php echo htmlspecialchars($data['extra_text_1'] ?? ''); ?></p>
                </div>
                <?php if (!empty($data['extra_title_2'])): ?>
                    <div class="info-card">
                        <h3><i class="fas fa-ruler-combined"></i> <?php echo htmlspecialchars($data['extra_title_2']); ?></h3>
                        <p><?php echo htmlspecialchars($data['extra_text_2'] ?? ''); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>