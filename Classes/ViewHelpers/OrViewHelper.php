<?php
namespace FluidTYPO3\Vhs\ViewHelpers;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * If content is empty use alternative text (can also be LLL:labelname shortcut or LLL:EXT: file paths).
 *
 * @author Danilo Bürger <danilo.buerger@hmspl.de>, Heimspiel GmbH
 * @package Vhs
 * @subpackage ViewHelpers
 */
class OrViewHelper extends AbstractViewHelper {

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('alternative', 'mixed', 'Alternative if content is empty, can use LLL: shortcut', FALSE, '');
		$this->registerArgument('arguments', 'array', 'Arguments to be replaced in the resulting string', FALSE, NULL);
		$this->registerArgument('extensionName', 'string', 'UpperCamelCase extension name without vendor prefix', FALSE, NULL);
	}

	/**
	 * @param $content string
	 * @return string
	 */
	public function render($content = NULL) {
		$alternative = $this->arguments['alternative'];
		$arguments = (array) $this->arguments['arguments'];

		if (0 === count($arguments)) {
			$arguments = NULL;
		}

		if (NULL === $content) {
			$content = $this->renderChildren();
		}

		if (FALSE === empty($content)) {
			return NULL !== $arguments ? vsprintf($content, $arguments) : $content;
		}

		if (FALSE === is_string($alternative) || 0 !== strpos($alternative, 'LLL:')) {
			return $alternative;
		}

		if (0 === strpos($alternative, 'LLL:EXT:')) {
			return LocalizationUtility::translate($alternative, NULL, $arguments);
		}

		$translate = substr($alternative, 4);
		$extensionName = $this->arguments['extensionName'];

		if (NULL === $extensionName) {
			$request = $this->controllerContext->getRequest();
			$extensionName = $request->getControllerExtensionName();
		}

		$content = LocalizationUtility::translate($translate, $extensionName, $arguments);
		if (NULL === $content) {
			$content = $alternative;
		}

		return $content;
	}

}
