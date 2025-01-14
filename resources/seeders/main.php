<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 LYRASOFT.
 * @license    __LICENSE__
 */

declare(strict_types=1);

\Lyrasoft\Luna\Faker\FakerHelper::registerMoreLoremClasses();

return [
    __DIR__ . '/user-seeder.php',
    __DIR__ . '/config-seeder.php',
    __DIR__ . '/language-seeder.php',
    __DIR__ . '/category-seeder.php',
    __DIR__ . '/tag-seeder.php',
    __DIR__ . '/article-seeder.php',
    __DIR__ . '/page-seeder.php',
    // __DIR__ . '/menu-seeder.php',
    __DIR__ . '/widget-seeder.php',
    __DIR__ . '/payment-seeder.php',
    __DIR__ . '/shipping-seeder.php',
    __DIR__ . '/manufacturer-seeder.php',
    __DIR__ . '/product-feature-seeder.php',
    __DIR__ . '/product-attribute-seeder.php',
    __DIR__ . '/product-tab-seeder.php',
    __DIR__ . '/product-seeder.php',
    __DIR__ . '/discount-seeder.php',
    __DIR__ . '/address-seeder.php',
    __DIR__ . '/additional-purchase-seeder.php',
    __DIR__ . '/order-seeder.php',
];
