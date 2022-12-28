<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Menu;

use Windwalker\Core\Application\AppContext;
use Lyrasoft\Luna\Menu\MenuBuilder;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Language\LangService;

/**
 * @var MenuBuilder $menu
 * @var AppContext $app
 * @var Navigator $nav
 * @var LangService $lang
 */

// Category
$menu->link($lang('luna.article.category.list'))
    ->to($nav->to('category_list', ['type' => 'article']));

// Article
$menu->link($lang('unicorn.title.grid', title: $lang('luna.article.title')))
    ->to($nav->to('article_list'));

// Page
$menu->link($lang('unicorn.title.grid', title: $lang('luna.page.title')))
    ->to($nav->to('page_list'));

// Tag
$menu->link($lang('unicorn.title.grid', title: $lang('luna.tag.title')))
    ->to($nav->to('tag_list'));

// Widget
$menu->link($lang('unicorn.title.grid', title: $lang('luna.widget.title')))
    ->to($nav->to('widget_list'));

// Menu
$menu->link($lang('luna.menu.manager.title', title: $lang('luna.menu.type.mainmenu')))
    ->to($nav->to('menu_list', ['type' => 'mainmenu']));

// User
$menu->link($lang('unicorn.title.grid', title: $lang('luna.user.title')))
    ->to($nav->to('user_list'));

// Language
$menu->link($lang('unicorn.title.grid', title: $lang('luna.language.title')))
    ->to($nav->to('language_list'));

// Config Core
$menu->link($lang('luna.config.title', $lang('luna.config.type.core')))
    ->to($nav->to('config_core'));

// Currency
$menu->link($lang('unicorn.title.grid', title: $lang('luna-shopgo.currency.title')))
    ->to($nav->to('currency_list'));
