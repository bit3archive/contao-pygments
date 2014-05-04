<?php

namespace Bit3\Contao\Pygments\Content;

use Bit3\Contao\Pygments\Pygmentize;

class PygmentsElement extends \ContentElement
{
	protected $strTemplate = 'ce_pygments';

	/**
	 * Compile the content element
	 */
	protected function compile()
	{
		global $container;

		/** @var Pygmentize $pygmentize */
		$pygmentize = $container['pygmentize'];

		$this->Template->pygmentizedCode = $pygmentize->pygmentize($this->code, $this->pygmentsSyntax, $this->pygmentsFormatter);

		if (!$GLOBALS['TL_CONFIG']['pygments_skipGlobalStyle'] && $this->pygmentsStyle) {
			$key = 'pygments-style-' . standardize($this->pygmentsFormatter, $this->pygmentsStyle);
			if (!isset($GLOBALS['TL_CSS'][$key])) {
				$GLOBALS['TL_CSS'][$key] = $pygmentize->getStylesFile($this->pygmentsFormatter, $this->pygmentsStyle);
			}
		}
	}
}