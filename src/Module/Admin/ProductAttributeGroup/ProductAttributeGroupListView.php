<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Admin\ProductAttributeGroup;

use App\Entity\ShopCategoryMap;
use App\Module\Admin\ProductAttributeGroup\Form\GridForm;
use App\Repository\ProductAttributeGroupRepository;
use Lyrasoft\Luna\Entity\Category;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Form\FormFactory;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\ORM\ORM;

/**
 * The ProductAttributeGroupListView class.
 */
#[ViewModel(
    layout: [
        'default' => 'product-attribute-group-list',
        'modal' => 'product-attribute-group-modal',
    ],
    js: 'product-attribute-group-list.js'
)]
class ProductAttributeGroupListView implements ViewModelInterface
{
    use TranslatorTrait;

    public function __construct(
        protected ORM $orm,
        #[Autowire]
        protected ProductAttributeGroupRepository $repository,
        protected FormFactory $formFactory
    ) {
    }

    /**
     * Prepare view data.
     *
     * @param  AppContext  $app   The request app context.
     * @param  View        $view  The view object.
     *
     * @return  array
     */
    public function prepare(AppContext $app, View $view): array
    {
        $type = 'attribute';
        $state = $this->repository->getState();

        // Prepare Items
        $page = $state->rememberFromRequest('page');
        $limit = $state->rememberFromRequest('limit');
        $filter = (array) $state->rememberFromRequest('filter');
        $search = (array) $state->rememberFromRequest('search');
        $ordering = $state->rememberFromRequest('list_ordering') ?? static::getDefaultOrdering();

        $items = $this->repository->getListSelector()
            ->setFilters($filter)
            ->searchTextFor(
                $search['*'] ?? '',
                static::getSearchFields()
            )
            ->addFilter('category.type', $type)
            ->ordering($ordering)
            ->page($page)
            ->limit($limit);

        $pagination = $items->getPagination();

        $items = $items->all();

        // Prepare categories
        $ids = $items->column('id')->dump();

        $categoryGroup = $this->orm->from(Category::class)
            ->leftJoin(ShopCategoryMap::class, 'map', 'map.category_id', 'category.id')
            ->where('map.target_id', $ids ?: [0])
            ->where('map.type', 'attribute_group')
            ->order('map.category_id')
            ->groupByJoins()
            ->all(Category::class)
            ->groupBy(
                function (Category $item) {
                    return $item->map?->target_id ?? 0;
                }
            );

        // Prepare Form
        $form = $this->formFactory->create(GridForm::class);
        $form->fill(compact('search', 'filter'));

        $showFilters = $this->showFilterBar($filter);

        $this->prepareMetadata($app, $view);

        return compact(
            'items',
            'pagination',
            'form',
            'showFilters',
            'ordering',
            'type',
            'categoryGroup'
        );
    }

    public function prepareItem(Collection $item): object
    {
        return $this->repository->getEntityMapper()->toEntity($item);
    }

    /**
     * Get default ordering.
     *
     * @return  string
     */
    public function getDefaultOrdering(): string
    {
        return 'category.lft ASC';
    }

    /**
     * Get search fields.
     *
     * @return  string[]
     */
    public function getSearchFields(): array
    {
        return [
            'category.id',
            'category.title',
            'category.alias',
        ];
    }

    /**
     * Is reorder enabled.
     *
     * @param  string  $ordering
     *
     * @return  bool
     */
    public function reorderEnabled(string $ordering): bool
    {
        return $ordering === 'category.ordering ASC';
    }

    /**
     * Can show Filter bar
     *
     * @param  array  $filter
     *
     * @return  bool
     */
    public function showFilterBar(array $filter): bool
    {
        foreach ($filter as $value) {
            if ($value !== null && (string) $value !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Prepare Metadata and HTML Frame.
     *
     * @param  AppContext  $app
     * @param  View        $view
     *
     * @return  void
     */
    protected function prepareMetadata(AppContext $app, View $view): void
    {
        $view->getHtmlFrame()
            ->setTitle(
                $this->trans('unicorn.title.grid', title: $this->trans('shopgo.product.attribute.group.title'))
            );
    }
}
