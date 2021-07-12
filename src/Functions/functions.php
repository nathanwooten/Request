<?php

namespace nathanwooten\Request\Functions;

function request_get_arguments( $requestType, $args = [] ) {

	$arguments = [];

	if ( ! class_exists( $requestType ) ) {
		throw new Exception( 'Provided request type class does not exist' );
	}

	$argsDefaults = [
		'method' = $_SERVER[ 'REQUEST_METHOD' ],
		'uri' = $_SERVER[ 'REQUEST_URI' ],
		'headers' = [],
		'body' = null,
		'version' = '1.1'
	];

	foreach ( [ 'method', 'uri', 'headers', 'body', 'version' ] as $constructorArg ) {
		$$constructorArg = isset( $args[ $constructorArg ] ) ? $args[ $constructorArg ] : $argDefaults[ $constructorArg ];
	}

	$rc = new ReflectionClass( $requestType );
	$rm = $rc->getConstructor();

	foreach ( $rm->getParameters() as $rp ) {
		$arguments[] = ${$rp->getName()};
	}

	return $arguments;

}
