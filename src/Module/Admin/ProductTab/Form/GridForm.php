<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Module\Admin\ProductTab\Form;

use Lyrasoft\Luna\Field\CategoryModalField;
use Unicorn\Enum\BasicState;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Form\Field\ListField;
use Windwalker\Form\Field\SearchField;
use Windwalker\Form\FieldDefinitionInterface;
use Windwalker\Form\Form;

/**
 * The GridForm class.
 */
class GridForm implements FieldDefinitionInterface
{
    use TranslatorTrait;

    /**
     * Define the form fields.
     *
     * @param  Form  $form  The Windwalker form object.
     *
     * @return  void
     */
    public function define(Form $form): void
    {
        $form->ns(
            'search',
            function (Form $form) {
                $form->add('*', SearchField::class)
                    ->label($this->trans('unicorn.grid.search.label'))
                    ->placeholder($this->trans('unicorn.grid.search.label'))
                    ->onchange('this.form.submit()');
            }
        );

        $form->ns(
            'filter',
            function (Form $form) {
                $form->add('product_tab.state', ListField::class)
                    ->label($this->trans('unicorn.field.state'))
                    ->option($this->trans('unicorn.select.placeholder'), '')
                    ->registerOptions(BasicState::getTransItems($this->lang))
                    ->onchange('this.form.submit()');

                $form->add('category', CategoryModalField::class)
                    ->label($this->trans('shopgo.product.tab.field.category'))
                    ->categoryType('product')
                    ->onchange('this.form.submit()');
            }
        );

        $form->ns(
            'batch',
            function (Form $form) {
                $form->add('state', ListField::class)
                    ->label($this->trans('unicorn.field.state'))
                    ->option($this->trans('unicorn.select.no.change'), '')
                    ->registerOptions(BasicState::getTransItems($this->lang));
            }
        );
    }
}
