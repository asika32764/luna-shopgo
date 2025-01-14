<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Console;

use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\DI\Container;

/**
 * The Application class.
 */
class Application extends ConsoleApplication
{
    /**
     * Your booting logic.
     *
     * @param  Container  $container
     *
     * @return  void
     */
    protected function booting(Container $container): void
    {
        $this->prepareWebSimulator();
    }

    /**
     * Your Terminating logic.
     *
     * @param  Container  $container
     *
     * @return  void
     */
    protected function terminating(Container $container): void
    {
        //
    }
}
