<?php

/** @var \Pimple $container */

/**
 * Services
 */
$container['pygmentize'] = $container->share(
	function () {
		return new \Bit3\Contao\Pygments\Pygmentize();
	}
);
