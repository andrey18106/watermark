<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2022 Andrey Borysenko <andrey18106x@gmail.com>
 *
 * @copyright Copyright (c) 2022 Alexander Piskun <bigcat88@icloud.com>
 *
 * @author 2022 Andrey Borysenko <andrey18106x@gmail.com>
 *
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Watermark\Tests\Integration\Service;

use OCA\Watermark\Controller\PythonController;
use OCP\AppFramework\App;
use OCP\AppFramework\Http\JSONResponse;

use PHPUnit\Framework\TestCase;

class PythonControllerIntegrationTest extends TestCase {
	/** @var PythonController */
	private $controller;

	/** @var string */
	private $userId = 'admin';

	public function setUp(): void {
		$app = new App('watermark');
		$container = $app->getContainer();

		$container->registerService('userId', function () {
			return $this->userId;
		});

		$container->registerService(\OCP\IRequest::class, function () {
			return $this->createMock(\OCP\IRequest::class);
		});

		$this->controller = $container->get(PythonController::class);
	}

	public function testCheck(): void {
		// Temporal for workflow passing
		$result = $this->controller->check();
		$this->assertTrue($result instanceof JSONResponse);
	}
}
