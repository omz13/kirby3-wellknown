<?php

Kirby::plugin(
    'omz13/wellknown',
    [
      'root'       => dirname( __FILE__, 2 ),
      'options'    => [
        'disable'          => false,
        'not-txt-notfound' => true,
        'notfound'         => true,
        'fromSite'         => false, // if true, allows site()->content('whatever') to override omz13.wellknown.the-whatever
        'x-ping'           => true,
      ],
      'blueprints' => [
        'omz13/wellknown' => dirname( __FILE__, 2 ) . '/blueprints/wellknown.yml',
    //        'wellknown' => __DIR__ . '/wellknown.yml',
      ],

      /* https://www.iana.org/assignments/well-known-uris/well-known-uris.xhtml */

      'routes'     => [
        [
          'pattern' => [
            '.well-known/(:any)\.(:any)',
            '.well-known/(:any)',
          ],
          'action'  => function ( string $whatever, string $filetype = "" ) {
            return omz13\K3WellKnown::processRequest( $whatever, $filetype );
          },
        ],
        [
          'pattern' => 'robots.txt',
          'action'  => function () {
            return omz13\K3WellKnown::processRequest( "robots", "txt" );
          },
        ],
        [
          'pattern' => 'humans.txt',
          'action'  => function () {
            return omz13\K3WellKnown::processRequest( "humans", "txt" );
          },
        ],
      ],
    ]
);

require_once __DIR__ . '/wellknown.php';
