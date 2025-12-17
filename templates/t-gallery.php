<?php
// Template: Gallery
// Fields: images (array of URLs)
$data = json_decode($page['content'], true) ?? [];
$images = $data['images'] ?? [];
?>
<section class="section-padding">
    <div class="container">
        <div class="section-header text-center">
            <span class="subtitle">Galería</span>
            <h2><?php echo htmlspecialchars($page['title']); ?></h2>
        </div>
        <div class="gallery-grid mt-5">
            <?php foreach ($images as $img): ?>
                <?php if (!empty($img)): ?>
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="Galería" onerror="this.style.display='none'">
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>