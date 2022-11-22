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

namespace OCA\Watermark\Service;

use OCA\Watermark\AppInfo\Application;
use Psr\Log\LoggerInterface;

class PythonService {
	/** @var string */
	private $cwd;

	/** @var string */
	private $pythonCommand;

	/** @var UtilsService */
	private $utils;

	/** @var LoggerInterface */
	private $logger;

	public function __construct(UtilsService $utils, LoggerInterface $logger) {
		$this->logger = $logger;
		$this->utils = $utils;
		$this->pythonCommand = '/usr/bin/python3';
		$this->cwd = $this->utils->getCustomAppsDirectory() . Application::APP_ID;
	}

	/**
	 * Runs Python script with given script relative path and script params
	 *
	 * @param string $scriptName relative path to the Python script
	 * @param array $scriptParams params to script in array (`['-param1' => value1, '--param2' => value2]`)
	 * @param boolean $nonBlocking flag that determines how to run Python script.
	 * @param array $env env variables for python script
	 *
	 * @return array|void
	 *
	 * If `$nonBlocking = true` - function will not waiting for Python script output and return `void`.
	 * If `$nonBlocking = false` - function will return array with the `result_code`
	 * and `output` of the script after Python script finish executing.
	 */
	public function run($scriptName, $scriptParams = [], $nonBlocking = false, $env = []) {
		if (count($scriptParams) > 0) {
			$params = array_map(function ($key, $value) {
				return $value !== '' ? "$key $value " : "$key";
			}, array_keys($scriptParams), array_values($scriptParams));
			$cmd = $this->pythonCommand . ' ' . $this->cwd . $scriptName . ' ' . join(' ', $params);
		} else {
			$cmd = $this->pythonCommand . ' ' .  $this->cwd . $scriptName;
		}
		if (count($env) > 0) {
			$envVariables = join(' ', array_map(function ($key, $value) {
				return "$key=\"$value\" ";
			}, array_keys($env), array_values($env)));
		} else {
			$envVariables = '';
		}
		if ($nonBlocking) {
			exec($envVariables . 'nohup ' . $cmd . ' > /dev/null 2>&1 &');
		} else {
			$errors = [];
			exec($envVariables . $cmd, $output, $result_code);
			if ($result_code !== 0) {
				if (count($output) > 0) {
					if (isset(json_decode($output[0], true)['errors'])) {
						$errors = json_decode($output[0], true)['errors'];
					} else {
						exec($envVariables . $cmd . ' 2>&1 1>/dev/null', $o_errors, $result_code);
						$errors = array_merge($output, ['', ''], $o_errors);
					}
				} else {
					exec($envVariables . $cmd . ' 2>&1 1>/dev/null', $o_errors, $result_code);
					$errors = $o_errors;
				}
			}
			return [
				'cmd' => $envVariables . $cmd,
				'output' => $output,
				'result_code' => $result_code,
				'errors' => $errors,
			];
		}
	}

	/**
	 * Check server requirements
	 *
	 * @return array check results with errors list
	 */
	private function checkDepsRequirements() {
		$errors = [];
		if (!$this->utils->isFunctionEnabled('exec')) {
			array_push($errors, '`exec` PHP function is not available.');
		}
		$pythonCompatible = $this->utils->isPythonCompatible();
		if (!$pythonCompatible['success']) {
			array_push($errors, 'Python version is lower then 3.6.8 or not available (result_code:' . $pythonCompatible['result_code'] . ')');
		}
		return ['success' => count($errors) === 0, 'errors' => $errors];
	}

	/**
	 * @return array list of uninstalled Python packages
	 */
	public function checkInstallation() {
		$depsCheck = $this->checkDepsRequirements();
		if ($depsCheck['success']) {
			try {
				$pythonResult = $this->run('/main.py', ['--check' => ''], false, ['PHP_PATH' => $this->utils->getPhpInterpreter(), 'SERVER_ROOT' => \OC::$SERVERROOT]);
				$this->logger->warning('[' . self::class . '] python run cmd: ' . $pythonResult['cmd']);
				return $this->parsePythonOutput($pythonResult);
			} catch (\Exception $e) {
				return [
					'success' => false,
					'message' => 'Some error while running the Python script',
				];
			}
		}
		return $depsCheck;
	}

	/**
	 * @param array $pythonResult
	 *
	 * @return array
	 */
	private function parsePythonOutput($pythonResult) {
		return [
			'result_code' => $pythonResult['result_code'],
			'output' => $pythonResult['output'],
		];
	}
}
