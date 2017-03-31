<?php

// require_once 'Zend/Console/Getopt.php';
try {
	$rules = array(
			'help|h'        => 'Get usage message',
			'library|l-s'   => 'Library to parse; if none provided, assumes current directory',
			'output|o-s'    => 'Where to write autoload file; if not provided, assumes "autoload_classmap.php" in library directory',
			'append|a'    => 'Append to autoload file if it exists',
			'overwrite|w'   => 'Whether or not to overwrite existing autoload file',
			'ignore|i-s'  => 'Comma-separated namespaces to ignore',
	);

	$opts = new Zend_Console_Getopt($rules);
	$opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
	echo $e->getUsageMessage();
	exit(2);
}

if ($opts->getOption('h')) {
	echo $opts->getUsageMessage();
	exit(0);
}