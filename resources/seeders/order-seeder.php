<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2023 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use App\Cart\CartItem;
use App\Cart\CartService;
use App\Entity\Address;
use App\Entity\Location;
use App\Entity\Order;
use App\Entity\OrderHistory;
use App\Entity\OrderItem;
use App\Entity\OrderState;
use App\Entity\OrderTotal;
use App\Entity\Payment;
use App\Entity\Product;
use App\Entity\ProductVariant;
use App\Entity\Shipping;
use App\Enum\InvoiceType;
use App\Service\CheckoutService;
use App\Service\LocationService;
use App\Service\OrderStateService;
use App\ShopGoPackage;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

use function Windwalker\chronos;

/**
 * Order Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (
        ShopGoPackage $shopGo,
        CheckoutService $checkoutService,
        CartService $cartService,
        OrderStateService $orderStateService,
        LocationService $locationService,
    ) use (
        $seeder,
        $orm,
        $db
    ) {
        $faker = $seeder->faker('en_US');

        /** @var EntityMapper<Order> $mapper */
        $mapper = $orm->mapper(Order::class);

        $states = $orm->findList(OrderState::class)->all()->dump();
        $addresses = $orm->findList(Address::class)->all()->dump();
        $products = $orm->findList(Product::class)->all()->dump();
        $payments = $orm->findList(Payment::class)->all()->dump();
        $shippings = $orm->findList(Shipping::class)->all()->dump();
        $variantGroups = $orm->findList(ProductVariant::class)->all()->groupBy('productId');

        // $useFullName = $shopGo->useFullName();
        // $useFullAddress = $shopGo->useFullAddress();

        $created = chronos('-2months');

        $users = $orm->findList(User::class)->all()->dump();

        foreach (range(1, 50) as $i) {
            /** @var User $user */
            $user = $faker->randomElement($users);

            // Prepare Product / Variants

            /** @var Product[] $products */
            $chosenProducts = $faker->randomElements($products, random_int(3, 5));
            $productVariants = [];

            foreach ($chosenProducts as $product) {
                $variants = $variantGroups[$product->getId()] ?? [];

                if (count($variants) > 0) {
                    $variant = $faker->randomElement($variants);
                    $productVariants[] = $variant;
                }
            }

            $cartItems = [];

            /** @var ProductVariant $productVariant */
            foreach ($productVariants as $productVariant) {
                $cartItem = new CartItem();
                $cartItem->setVariant($productVariant)
                    ->setQuantity(random_int(1, 5))
                    ->setPriceSet($productVariant->getPriceSet())
                    ->setCover($productVariant->getCover())
                    ->setLink('#');

                $cartItems[] = $cartItem;
            }

            // Create Cart Data
            $cartData = $cartService->createCartDataFromItems($cartItems);

            // Start Create Order
            $item = $mapper->createEntity();

            $item->setUserId($user->getId());

            // Payment

            /** @var Payment $payment */
            $payment = $faker->randomElement($payments);
            /** @var Address $paymentAddress */
            $paymentAddress = $faker->randomElement($addresses);

            $location = $orm->mustFindOne(Location::class, $paymentAddress->getLocationId());
            [$country, $state, $city] = $locationService->getPathFromLocation($location);

            $item->setPaymentId($payment->getId());

            $paymentData = $item->getPaymentData()
                ->setFullName($user->getName())
                ->setEmail($user->getEmail())
                ->setAddress1($paymentAddress->getAddress1())
                ->setAddress2($paymentAddress->getAddress2())
                ->setAddressId($paymentAddress->getId())
                ->setCountry($country?->getTitle() ?: '')
                ->setState($state?->getTitle() ?: '')
                ->setCity($city?->getTitle() ?: '')
                ->setPhone($paymentAddress->getPhone())
                ->setMobile($paymentAddress->getMobile())
                ->setCompany($paymentAddress->getCompany())
                ->setVat($paymentAddress->getVat());

            // Shipping

            /** @var Shipping $shipping */
            $shipping = $faker->randomElement($shippings);
            /** @var Address $shippingAddress */
            $shippingAddress = $faker->randomElement($addresses);

            $location = $orm->mustFindOne(Location::class, $shippingAddress->getLocationId());
            [$country, $state, $city] = $locationService->getPathFromLocation($location);

            $item->setShippingId($shipping->getId());

            $firstName = $shippingAddress->getFirstname();
            $lastName = $shippingAddress->getLastname();

            $item->getShippingData()
                ->setFullName($firstName . ' ' . $lastName)
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setAddressId($shippingAddress->getId())
                ->setAddress1($shippingAddress->getAddress1())
                ->setAddress2($shippingAddress->getAddress2())
                ->setCountry($country?->getTitle() ?: '')
                ->setState($state?->getTitle() ?: '')
                ->setCity($city?->getTitle() ?: '')
                ->setPhone($shippingAddress->getPhone())
                ->setMobile($shippingAddress->getMobile())
                ->setNote($faker->sentence());

            // Invoice
            $item->setInvoiceType($faker->randomElement(InvoiceType::cases()));

            if ($item->getInvoiceType() === InvoiceType::COMPANY()) {
                $item->getInvoiceData()
                    ->setTitle($user->getName());
            } else {
                $item->getInvoiceData()
                    ->setTitle($paymentData->getCompany())
                    ->setVat($paymentData->getVat())
                    ->setMobile($paymentData->getMobile());
            }

            // Date
            $hrOffsets = random_int(8, 36);
            $created = $created->modify("+{$hrOffsets}hours");
            $item->setCreated($created);

            // Create Order
            $order = $checkoutService->createOrder($item, $cartData);

            // A workaround to prevent relations create twice.
            $order = $orm->findOne(Order::class, $order->getId());

            // Use State

            /** @var OrderState $state */
            $state = $faker->randomElement($states);

            $order->setState($state);

            $orderStateService->mutateOrderByState(
                $order,
                $state,
                $faker->dateTimeBetween('-1years', 'now')
            );

            $orm->updateOne(Order::class, $order);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Order::class, OrderItem::class, OrderTotal::class, OrderHistory::class);
    }
);
