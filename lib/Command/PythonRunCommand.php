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

namespace OCA\Watermark\Command;

use Exception;
use OCA\Watermark\Service\PythonService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PythonRunCommand extends Command {
	public const ARGUMENT_SCRIPT_PATH = 'script_path';

	/** @var PythonService */
	private $pythonService;

	public function __construct(PythonService $pythonService) {
		parent::__construct();

		$this->pythonService = $pythonService;
	}

	protected function configure(): void {
		$this->setName("watermark:python:run");
		$this->setDescription("Executes given python script");
		$this->addArgument(self::ARGUMENT_SCRIPT_PATH, InputArgument::REQUIRED);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		try {
			$scriptPath = $input->getArgument(self::ARGUMENT_SCRIPT_PATH);
			$result = $this->pythonService->run($scriptPath);
			$output->writeln(json_encode($result));
			return 0;
		} catch (Exception $e) {
			$output->writeln($e->getMessage());
			$output->writeln($e->getTraceAsString());
			return 1;
		}
		return 1;
	}
}
