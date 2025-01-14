<?php

declare(strict_types=1);

namespace App\view;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        object          The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

use App\Entity\Order;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

/**
 * @var Order $order
 */

?>

<div class="card">
    <div class="card-body">
        <dl class="row p-2 mb-0">
            <dt class="col-4">
                @lang('shopgo.order.field.shipping')
            </dt>
            <dd class="col-8">
                {{ $order->getShipping()->getTitle() }}
            </dd>

            <dt class="col-4">
                @lang('shopgo.order.field.payment')
            </dt>
            <dd class="col-8">
                {{ $order->getPayment()->getTitle() }}
            </dd>

            <dt class="col-4">
                @lang('shopgo.order.field.invoice.no')
            </dt>
            <dd class="col-8">
                {{ $order->getInvoiceNo() ?: '-' }}
            </dd>
        </dl>
    </div>
</div>
