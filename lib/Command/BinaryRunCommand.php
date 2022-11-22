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
use OCA\Watermark\AppInfo\Application;
use OCA\Watermark\Service\UtilsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BinaryRunCommand extends Command {
	public const ARGUMENT_BINARY_PATH = 'binary_path';
	public const ARGUMENT_BINARY_PARAM = 'binary_param';

	/** @var string */
	private $cwd;

	/** @var UtilsService */
	private $utils;

	public function __construct(UtilsService $utils) {
		parent::__construct();

		$this->utils = $utils;
		$this->cwd = $this->utils->getCustomAppsDirectory() . Application::APP_ID;
	}

	protected function configure(): void {
		$this->setName("watermark:binary:run");
		$this->setDescription("Executes given binary file");
		$this->addArgument(self::ARGUMENT_BINARY_PATH, InputArgument::REQUIRED);
		$this->addArgument(self::ARGUMENT_BINARY_PARAM, InputArgument::OPTIONAL);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		try {
			$binaryPath = $input->getArgument(self::ARGUMENT_BINARY_PATH);
			$binaryParam = $input->getArgument(self::ARGUMENT_BINARY_PARAM);
			$cmd = $this->cwd . $binaryPath;
			if (isset($binaryParam)) {
				$cmd .= ' ' . $binaryParam;
			}
			exec($cmd, $output, $result_code);
			$result = [
				'cmd' => $cmd,
				'output' => $output,
				'result_code' => $result_code,
			];
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
