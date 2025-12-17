<?php
require_once 'config.php';
$db = getDB();

echo "Migrating Home to CMS Pages...<br>";

// 1. Define Home Content (Stats, Testimonials, Intro from PDF)
$home_content = json_encode([
    // Intro Text (Page 2)
    'intro_title' => 'Sobre nuestra gráfica',
    'intro_text' => 'Nos enorgullece ser parte de la historia en cada uno de los proyectos que se nos confía, demostrando nuestro compromiso con la excelencia, la innovación y una marcada atención personalizada.',

    // Stats (Page 2-3)
    'stat_1_number' => '27.000.000',
    'stat_1_label' => 'Estuches fabricados por año',
    'stat_1_icon' => 'fas fa-boxes',

    'stat_2_number' => '25',
    'stat_2_label' => 'Profesionales a disposición',
    'stat_2_icon' => 'fas fa-users',

    'stat_3_number' => 'ISO 9001:2015',
    'stat_3_label' => 'Certificación de Calidad',
    'stat_3_icon' => 'fas fa-certificate',

    'stat_4_number' => '+450',
    'stat_4_label' => 'Clientes satisfechos / Tintas Eco', // Combined info from PDF
    'stat_4_icon' => 'fas fa-smile',

    // Testimonials (Page 3)
    'testimonial_1_text' => 'Muy profesionales. Atentos al detalle de cada impresión',
    'testimonial_1_author' => 'María Eugenia Vigna - Laboratorio Merlino',

    'testimonial_2_text' => 'Son super prolijos y responsables. Los pedidos cumplieron con los plazos. ¡Súper recomendados!',
    'testimonial_2_author' => 'Florencia Crivella - Silvetex',

    'testimonial_3_text' => 'Valoramos mucho su predisposición para mejorar continuamente y el compromiso que demuestran.',
    'testimonial_3_author' => 'Paula Oliveros - Laboratorio Géminis',

    'testimonial_4_text' => 'Su capacidad de adaptación y compromiso con la calidad los ha convertido en un socio estratégico.',
    'testimonial_4_author' => 'Eliana Paesani - Savant'
]);

// 2. Insert 'inicio' page.
// We make sure it's the FIRST page (ID 1 if possible, but IDs are auto-inc. We can just relying on ORDER BY or fetch order.
// Since we have existing pages, we might just add it. `index.php` loop will pick it up.
// To ensure it's first, we might need to recreate the table or just query ORDER BY ID and assume we update the order later.
// For now, let's just insert it.
$stmt = $db->prepare("INSERT OR REPLACE INTO pages (slug, title, template, content) VALUES (?, ?, ?, ?)");
$stmt->execute(['inicio', 'Inicio', 'start', $home_content]);

echo "Home Page inserted into CMS.<br>";
?>