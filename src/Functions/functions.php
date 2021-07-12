<?php

namespace nathanwooten\Request\Functions;

/** Example usage:

$requestType = 'GuzzleHttp\Psr7\Request';

$container = new Container;
$container->set( 'Request', function ( Container $c, $args = null ) use ( $requestType ) {
	return new $requestType( ...request_get_arguments( $requestType, $args ) );
};

*/

function request_get_arguments( $requestType, $args = [] ) {

	$arguments = [];

	if ( ! class_exists( $requestType ) ) {
		throw new Exception( 'Provided request type class does not exist' );
	}

	$argsDefaults = [
		'method' => $_SERVER[ 'REQUEST_METHOD' ],
		'uri' => $_SERVER[ 'REQUEST_URI' ],
		'headers' => [],
		'body' => null,
		'version' => '1.1'
	];
	$argsSpecificDefaults = [
		'Laminas\Diactoros\Request' => [
			'body' => 'php://temp'
		]
	];

	foreach ( $argsDefaults as $argName => $v ) {
		if ( isset( $argsSpecificDefaults[ $requestType ][ $argName ] ) ) {
			$argsDefaults[ $argName ] = $argsSpecificDefaults[ $requestType ][ $argName ];
		}
	}

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
