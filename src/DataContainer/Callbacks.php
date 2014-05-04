<?php

namespace Bit3\Contao\Pygments\DataContainer;

use Symfony\Component\Process\ProcessBuilder;

class Callbacks
{
	public function getLexerOptions()
	{
		$processBuilder = new ProcessBuilder();

		// set the pygmentize executable path
		$processBuilder->setPrefix($GLOBALS['TL_CONFIG']['pygments_pygmentizePath']);

		// list lexers
		$processBuilder->add('-L')->add('lexers');

		$process = $processBuilder->getProcess();
		$process->run();

		if ($process->isSuccessful()) {
			$options = array();

			$lines = explode("\n", $process->getOutput());

			while (count($lines)) {
				$line = array_shift($lines);

				// line must start with an asterisk
				if ($line[0] == '*') {
					$lexer       = preg_replace('~^[^\w]*([^,:]+).*:$~', '$1', trim($line));
					$description = trim(array_shift($lines));
					$description = preg_replace('~\(filenames[^\)]+\)~', '', $description);

					$options[$lexer] = sprintf('[%s] %s', $lexer, htmlentities($description, ENT_QUOTES, 'UTF-8'));
				}
			}

			return $options;
		}

		return array();
	}

	public function getFormatterOptions()
	{
		$processBuilder = new ProcessBuilder();

		// set the pygmentize executable path
		$processBuilder->setPrefix($GLOBALS['TL_CONFIG']['pygments_pygmentizePath']);

		// list lexers
		$processBuilder->add('-L')->add('formatters');

		$process = $processBuilder->getProcess();
		$process->run();

		if ($process->isSuccessful()) {
			$options = array();

			$lines = explode("\n", $process->getOutput());

			while (count($lines)) {
				$line = array_shift($lines);

				// line must start with an asterisk
				if ($line[0] == '*') {
					$style       = preg_replace('~^[^\w]*([^,:]+).*:$~', '$1', trim($line));
					$description = trim(array_shift($lines));

					$options[$style] = sprintf('[%s] %s', $style, htmlentities($description, ENT_QUOTES, 'UTF-8'));
				}
			}

			return $options;
		}

		return array();
	}

	public function getStyleOptions()
	{
		$processBuilder = new ProcessBuilder();

		// set the pygmentize executable path
		$processBuilder->setPrefix($GLOBALS['TL_CONFIG']['pygments_pygmentizePath']);

		// list lexers
		$processBuilder->add('-L')->add('styles');

		$process = $processBuilder->getProcess();
		$process->run();

		if ($process->isSuccessful()) {
			$options = array();

			$lines = explode("\n", $process->getOutput());

			while (count($lines)) {
				$line = array_shift($lines);

				// line must start with an asterisk
				if ($line[0] == '*') {
					$style       = preg_replace('~^[^\w]*([^,:]+).*:$~', '$1', trim($line));
					$description = trim(array_shift($lines));

					$options[$style] = sprintf('[%s] %s', $style, htmlentities($description, ENT_QUOTES, 'UTF-8'));
				}
			}

			return $options;
		}

		return array();
	}

	/**
	 * Dynamically set the ace syntax
	 *
	 * @param mixed
	 * @param \DataContainer
	 *
	 * @return string
	 */
	public function setRteSyntax($varValue, \DataContainer $dc)
	{
		switch ($dc->activeRecord->pygmentsSyntax) {
			case 'c':
			case 'cpp':
				$syntax = 'c_cpp';
				break;

			default:
				$syntax = strtolower($dc->activeRecord->pygmentsSyntax);
				break;
		}

		$GLOBALS['TL_DCA']['tl_content']['fields']['code']['eval']['rte'] = 'ace|' . $syntax;
		return $varValue;
	}
}