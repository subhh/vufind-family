<?php

/*
 * This file is part of VuFind Family.
 *
 * VuFind Family is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * VuFind Family is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with VuFind Family. If not, see <https://www.gnu.org/licenses/>.
 *
 * @author    David Maus <david.maus@sub.uni-hamburg.de>
 * @copyright (c) 2023 by Staats- und Universitätsbibliothek Hamburg
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace SUBHH\VuFind\Family;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

final class ControllerFactory implements FactoryInterface
{
    /**
     * @phan-suppress PhanUnusedPublicFinalMethodParameter
     * @param ?array<mixed> $options
     */
    public function __invoke (ContainerInterface $container, $requestedName, array $options = null)
    {
        $search = $container->get('VuFindSearch\Service');
        return new Controller($search);
    }
}
