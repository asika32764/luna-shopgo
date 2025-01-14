<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Field;

use App\Entity\Product;
use App\Entity\ProductVariant;
use Unicorn\Field\ModalField;
use Unicorn\Image\ImagePlaceholder;
use Windwalker\DI\Attributes\Inject;
use Windwalker\Query\Query;

use function Windwalker\Query\val;

/**
 * The ProductModalField class.
 */
class ProductModalField extends ModalField
{
    #[Inject]
    protected ImagePlaceholder $imagePlaceholder;

    protected function configure(): void
    {
        $this->route('product_list');
        $this->table(Product::class);
        $this->imageField('cover');
    }

    protected function prepareQuery(Query $query): void
    {
        parent::prepareQuery($query);

        $query->select('variant.cover AS cover');
        $query->leftJoin(
            fn(Query $query) => $query->select('cover')
                ->select('product_id')
                ->from(ProductVariant::class)
                ->where('primary', 1),
            'variant',
            'variant.product_id',
            'product.id'
        );
    }

    protected function getItemTitle(): ?string
    {
        return $this->getItem()['title'] ?? '';
    }

    protected function getItemImage(): ?string
    {
        return $this->getItem()['cover'] ?? $this->imagePlaceholder->placeholderSquare();
    }
}
