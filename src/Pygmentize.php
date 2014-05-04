<?php

namespace Bit3\Contao\Pygments;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ProcessBuilder;

class Pygmentize
{
	public function __construct()
	{
		if (!is_dir(TL_ROOT . '/system/cache/pygments')) {
			\Files::getInstance()->mkdir('system/cache/pygments');
		}
	}

	public function getCacheKey($code, $lexer, $formatter = 'html')
	{
		return substr(md5($code), 0, 8) . '-' . standardize($lexer) . '-' . standardize($formatter);
	}

	public function getCacheFile($code, $lexer, $formatter = 'html')
	{
		$cacheKey = $this->getCacheKey($code, $lexer, $formatter);
		return sprintf('system/cache/pygments/%s.html', $cacheKey);
	}

	/**
	 * Pygmentize the code with the given lexer and style. If the pygmentized code already exists in cache,
	 * return the cache entry, otherwise generate the pygmentized code.
	 *
	 * @param string $code
	 * @param string $lexer
	 * @param bool   $style
	 *
	 * @return string
	 */
	public function pygmentize($code, $lexer, $formatter = 'html')
	{
		try {
			$cacheFile = $this->getCacheFile($code, $lexer, $formatter);
			$file      = new \File($cacheFile, true);

			if ($file->exists()) {
				return $file->getContent();
			}

			$pygmentized = $this->pygmentizeNoCache($code, $lexer, $formatter);

			$file->write($pygmentized);
			$file->close();

			return $pygmentized;
		}
		catch (\Exception $e) {
			return 'Could not pygmentize: [' . get_class($e) . '] ' . '<pre>' . htmlentities($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
		}
	}

	/**
	 * Pygmentize the code with the given lexer and style and return the pygmentized code and store the result
	 * in the cache.
	 *
	 * @param string $code
	 * @param string $lexer
	 * @param bool   $style
	 *
	 * @return string
	 */
	public function pygmentizeCache($code, $lexer, $formatter = 'html')
	{
		try {
			$cacheFile = $this->getCacheFile($code, $lexer, $formatter);
			$file      = new \File($cacheFile, true);

			$pygmentized = $this->pygmentizeNoCache($code, $lexer, $formatter);

			$file->write($pygmentized);
			$file->close();

			return $pygmentized;
		}
		catch (\Exception $e) {
			return 'Could not pygmentize: [' . get_class($e) . '] ' . '<pre>' . htmlentities($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
		}
	}

	/**
	 * Pygmentize the code with the given lexer and style and return the pygmentized code without storing it
	 * in the cache.
	 *
	 * @param string $code
	 * @param string $lexer
	 * @param bool   $style
	 *
	 * @return string
	 */
	public function pygmentizeNoCache($code, $lexer, $formatter = 'html')
	{
		try {
			return $this->executePygmentize($code, $lexer, $formatter);
		}
		catch (\Exception $e) {
			return 'Could not pygmentize: [' . get_class($e) . '] ' . '<pre>' . htmlentities($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
		}
	}

	protected function executePygmentize($code, $lexer, $formatter = 'html')
	{
		$processBuilder = new ProcessBuilder();

		// set the pygmentize executable path
		$processBuilder->setPrefix($GLOBALS['TL_CONFIG']['pygments_pygmentizePath']);

		// set the lexer
		$processBuilder->add('-l')->add($lexer);

		// set the formatter
		$processBuilder->add('-f')->add($formatter);

		$processBuilder->setInput($code);

		$process = $processBuilder->getProcess();
		$process->run();

		if (!$process->isSuccessful() || strlen($process->getErrorOutput())) {
			throw new \RuntimeException($process->getCommandLine() . PHP_EOL . $process->getErrorOutput(), $process->getStopSignal());
		}

		return $process->getOutput();
	}

	public function getStylesCacheKey($formatter = 'html', $style = 'default')
	{
		return 'pygments-' . standardize($formatter) . '-' . standardize($style);
	}

	public function getStylesCacheFile($formatter = 'html', $style = 'default')
	{
		$cacheKey = $this->getStylesCacheKey($formatter, $style);
		return sprintf('assets/css/%s.css', $cacheKey);
	}

	/**
	 * Generate styles for the given formatter and return the pathname to the css file instead of the styles itself.
	 *
	 * @param string $formatter
	 * @param string $style
	 *
	 * @return string
	 */
	public function getStylesFile($formatter = 'html', $style = 'default')
	{
		try {
			$cacheFile = $this->getStylesCacheFile($formatter, $style);
			$file      = new \File($cacheFile, true);

			if (!$file->exists()) {
				$styles = $this->getStylesNoCache($formatter, $style);

				$file->write($styles);
				$file->close();
			}

			return $cacheFile;
		}
		catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Generate and return the styles for the given formatter, if it already exists in the cache return it from there.
	 *
	 * @param string $formatter
	 * @param string $style
	 *
	 * @return string
	 */
	public function getStyles($formatter = 'html', $style = 'default')
	{
		try {
			$cacheFile = $this->getStylesCacheFile($formatter, $style);
			$file      = new \File($cacheFile, true);

			if ($file->exists()) {
				return $file->getContent();
			}

			$styles = $this->getStylesNoCache($formatter, $style);

			$file->write($styles);
			$file->close();

			return $styles;
		}
		catch (\Exception $e) {
			return 'Could not get pygments styles: [' . get_class($e) . '] ' . '<pre>' . htmlentities($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
		}
	}

	/**
	 * Generate and return the styles for the given formatter and store it in the cache.
	 *
	 * @param string $formatter
	 * @param string $style
	 *
	 * @return string
	 */
	public function getStylesCache($formatter = 'html', $style = 'default')
	{
		try {
			$cacheFile = $this->getStylesCacheFile($formatter, $style);
			$file      = new \File($cacheFile, true);

			$styles = $this->getStylesNoCache($formatter, $style);

			$file->write($styles);
			$file->close();

			return $styles;
		}
		catch (\Exception $e) {
			return 'Could not get pygments styles: [' . get_class($e) . '] ' . '<pre>' . htmlentities($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
		}
	}

	/**
	 * Generate and return the styles for the given formatter without storing it in the cache.
	 *
	 * @param string $formatter
	 * @param string $style
	 *
	 * @return string
	 */
	public function getStylesNoCache($formatter = 'html', $style = 'default')
	{
		try {
			return $this->generateStyles($formatter, $style);
		}
		catch (\Exception $e) {
			return 'Could not get pygments styles: [' . get_class($e) . '] ' . '<pre>' . htmlentities($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
		}
	}

	protected function generateStyles($formatter = 'html', $style = 'default')
	{
		$processBuilder = new ProcessBuilder();

		// set the pygmentize executable path
		$processBuilder->setPrefix($GLOBALS['TL_CONFIG']['pygments_pygmentizePath']);

		// set the formatter
		$processBuilder->add('-f')->add($formatter);

		// set the style
		$processBuilder->add('-S')->add($style);

		$process = $processBuilder->getProcess();
		$process->run();

		if (!$process->isSuccessful() || strlen($process->getErrorOutput())) {
			throw new \RuntimeException($process->getCommandLine() . PHP_EOL . $process->getErrorOutput(), $process->getStopSignal());
		}

		return $process->getOutput();
	}
}