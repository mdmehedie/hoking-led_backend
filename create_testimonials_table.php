<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    \DB::statement('CREATE TABLE IF NOT EXISTS testimonials (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        client_name VARCHAR(255) NOT NULL,
        client_position VARCHAR(255) NULL,
        client_company VARCHAR(255) NULL,
        testimonial TEXT NOT NULL,
        rating INT NULL DEFAULT 5,
        is_visible BOOLEAN DEFAULT TRUE,
        sort_order INT DEFAULT 0,
        meta_title VARCHAR(255) NULL,
        meta_description TEXT NULL,
        meta_keywords TEXT NULL,
        created_at TIMESTAMP NULL,
        updated_at TIMESTAMP NULL
    )');

    echo "Testimonials table created successfully!\n";
} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
