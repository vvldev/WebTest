<?php

declare(strict_types=1);

use App\Database\PdoFactory;
use App\Repository\PdoCategoryRepository;
use App\Repository\PdoPostRepository;
use App\Repository\RowMapper;
use App\Seed\BlogSeeder;
use App\Seed\PostImageGenerator;
use Faker\Factory;

$config = require dirname(__DIR__) . '/bootstrap.php';

$pdo = (new PdoFactory(
    $config['db']['host'],
    $config['db']['port'],
    $config['db']['database'],
    $config['db']['user'],
    $config['db']['password'],
))->create();

$rowMapper = new RowMapper();

$seeder = new BlogSeeder(
    Factory::create('ru_RU'),
    new PdoCategoryRepository($pdo, $rowMapper),
    new PdoPostRepository($pdo, $rowMapper),
    new PostImageGenerator($config['blog']['upload_dir'], $config['blog']['upload_public_path']),
);

$seeder->run(
    $config['seed']['categories'],
    $config['seed']['posts'],
    $config['seed']['max_categories_per_post'],
);

printf("Создано категорий: %d, статей: %d\n", $config['seed']['categories'], $config['seed']['posts']);
