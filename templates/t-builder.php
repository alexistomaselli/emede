<?php
// Template: Page Builder (Dynamic Blocks)
// Blocks: text, image, columns_2, cta
$blocks = json_decode($page['content'], true) ?? [];
?>
<section class="section-padding">
    <div class="container">
        <!-- Page Title -->
        <h1 class="mb-5"><?php echo htmlspecialchars($page['title']); ?></h1>

        <!-- Blocks Render -->
        <div class="builder-content">
            <?php if (empty($blocks)): ?>
                <p>Esta página aún no tiene contenido.</p>
            <?php else: ?>
                <?php foreach ($blocks as $block): ?>

                    <!-- BLOCK: TEXT -->
                    <?php if ($block['type'] === 'text'): ?>
                        <div class="block-text mb-4">
                            <?php echo nl2br(htmlspecialchars($block['content'])); ?>
                        </div>

                        <!-- BLOCK: IMAGE -->
                    <?php elseif ($block['type'] === 'image'): ?>
                        <div class="block-image mb-4">
                            <img src="<?php echo htmlspecialchars($block['url']); ?>" alt="Imagen"
                                style="max-width: 100%; height: auto; border-radius: 8px;">
                            <?php if (!empty($block['caption'])): ?>
                                <p class="text-sm text-gray mt-1"><em><?php echo htmlspecialchars($block['caption']); ?></em></p>
                            <?php endif; ?>
                        </div>

                        <!-- BLOCK: 2 COLUMNS -->
                    <?php elseif ($block['type'] === 'columns_2'): ?>
                        <div class="grid-2-col mb-4 gap-4">
                            <div class="col-1">
                                <?php if ($block['col1_type'] === 'text'): ?>
                                    <?php echo nl2br(htmlspecialchars($block['col1_content'])); ?>
                                <?php elseif ($block['col1_type'] === 'image'): ?>
                                    <img src="<?php echo htmlspecialchars($block['col1_url']); ?>" alt="Col 1"
                                        style="max-width: 100%; border-radius: 8px;">
                                <?php endif; ?>
                            </div>
                            <div class="col-2">
                                <?php if ($block['col2_type'] === 'text'): ?>
                                    <?php echo nl2br(htmlspecialchars($block['col2_content'])); ?>
                                <?php elseif ($block['col2_type'] === 'image'): ?>
                                    <img src="<?php echo htmlspecialchars($block['col2_url']); ?>" alt="Col 2"
                                        style="max-width: 100%; border-radius: 8px;">
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- BLOCK: CTA -->
                    <?php elseif ($block['type'] === 'cta'): ?>
                        <div class="block-cta mb-4 text-center p-5 bg-light rounded">
                            <h3 class="mb-3"><?php echo htmlspecialchars($block['title']); ?></h3>
                            <a href="<?php echo htmlspecialchars($block['url']); ?>" class="btn">
                                <?php echo htmlspecialchars($block['btn_text']); ?>
                            </a>
                        </div>

                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>