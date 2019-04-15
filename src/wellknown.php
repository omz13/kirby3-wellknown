<?php

namespace omz13;

use Kirby;
use Kirby\Toolkit\A;

use const CASE_LOWER;
use const INPUT_SERVER;
use const PHP_EOL;
use const WK_CONFIGURATION_PREFIX;
use const WK_VERSION;

use function array_change_key_case;
use function array_key_exists;
use function class_exists;
use function count;
use function define;
use function filter_input;
use function header;
use function in_array;
use function is_array;
use function is_string;
use function kirby;
use function str_replace;
use function strlen;
use function strtolower;
use function substr;

define( 'WK_VERSION', '0.2.1' );
define( 'WK_CONFIGURATION_PREFIX', 'omz13.wellknown' );

class K3WellKnown
{
  public static $version = WK_VERSION;

  public static function ping() : string {
    return static::class . ' pong ' . static::$version;
  }//end ping()

  private static function getConfigurationForKey( string $key ) : string {
    // Try to pick up configuration when provided in an array (vendor.plugin.array(key=>value))
    $o = kirby()->option( WK_CONFIGURATION_PREFIX );
    if ( $o != null && is_array( $o ) ) {
      $oLC = array_change_key_case( $o, CASE_LOWER );
      if ( array_key_exists( strtolower( $key ) , $oLC ) ) {
        return $oLC[ strtolower( $key ) ];
      }
    }

    // try to pick up configuration as a discrete (vendor.plugin.key=>value)
    $o = kirby()->option( WK_CONFIGURATION_PREFIX . '.' . $key );
    if ( $o != null ) {
      if ( is_string( $o ) ) {
        return $o;
      }
      // array'd string? i.e. [ 'string' ] instead of 'string'
      if ( is_array( $o ) && count( $o ) == 1 && is_string( $o[0] ) ) {
          return $o[0];
      }
    }

    // this should not be reached... because plugin should define defaults for all its options...
    return "";
  }//end getConfigurationForKey()

  /**
   * @SuppressWarnings(PHPMD.CyclomaticComplexity)
   * @SuppressWarnings(PHPMD.NPathComplexity)
   */
  private static function getRobots() : string {
    $r  = '# Any use of this file - robots.txt -  or failure' . "\n";
    $r .= '# to obey the robots exclusion standards set' . "\n";
    $r .= '# forth at http://www.robotstxt.org/ is strictly prohibited' . "\n";
    $r .= "\n";

    $hasSitemap = false;

    if ( class_exists( "omz13\XMLSitemap" ) ) {
      $hasSitemap = XMLSitemap::isEnabled();
      if ( $hasSitemap != true ) {
        $r .= '# Sitemap not enabled' . "\n";
      } else {
        $r .= '# Sitemap' . "\n";
      }
    } else {
      # test for somebody providing a route for /sitemap.xml
      $rr = A::pluck( kirby()->routes(), 'pattern' );
      if ( in_array( 'sitemap.xml', $rr, true ) ) {
        $hasSitemap = true;
        $r         .= '# Sitemap available' . "\n";
      }
    }//end if

    if ( $hasSitemap ) {
      if ( kirby()->multilang() == true ) {
        $r .= '# ML sitemap' . "\n";

        $c = false;
        $l = "";

        foreach ( kirby()->languages() as $lang ) {
          if ( substr( $lang->url(), -strlen( $lang->code() ) ) !== $lang->code() ) {
            $l .= 'Sitemap: ' . $lang->url() . '/sitemap.xml' . "\n";
          } else {
            $c = true;
          }
        }

        if ( $c != false ) {
          $r .= 'Sitemap: ' . kirby()->url() . '/sitemap.xml' . "\n" . $l;
        } else {
          $r .= $l;
        }
      } else {
        $r .= '# SL sitemap' . "\n";
        $r .= 'Sitemap: ' . kirby()->url() . '/sitemap.xml' . "\n";
      }//end if
    } else {
      $r .= '# Sitemap not provided' . "\n";
    }//end if

    $x = static::getConfigurationForKey( 'the-robots' );
    if ( $x == null || $x == "" ) {
      $x = kirby()->site()->content()->get( 'wkRobots' );
    } else {
      // expand \n, etc .
      $x = str_replace( '\n', PHP_EOL, $x );
    }

    if ( $x != null && $x != "" ) {
      return $r . "\n" . $x . "\n";
    }

    return $r;
  }//end getRobots()

  /**
   * @SuppressWarnings(PHPMD.CyclomaticComplexity)
   * @SuppressWarnings(PHPMD.NPathComplexity)
   */
  public static function processRequest( string $whatever, ?string $extension = null ) : ?Kirby\Cms\Response {
    // Guard against being disabled
    if ( static::getConfigurationForKey( 'disable' ) == 'true' ) {
      header( 'x-omz13-wk:DISABLED' );
      return null;
    }

    // header( 'x-omz13-wk-request:' . $_SERVER['REQUEST_URI'] );
    // because mess doesn't like accessing tghe super-global...
    header( 'x-omz13-wk-request:' . filter_input( INPUT_SERVER, 'REQUEST_URI' ) );

    /*
    if ( $whatever == "favicon" && $extension == 'ico' {
        // ??? TODO ???
    }
    */

    // normalize
    $extension = strtolower( $extension );
    $whatever  = strtolower( $whatever );

    if ( $whatever == 'ping' && $extension == 'txt' ) {
      if ( static::getConfigurationForKey( 'x-ping' ) == true ) {
        return new Kirby\Cms\Response( 'pong' , 'text/plain', 200, [ 'X-omz13-WK' => 'ping-pong-enabled' ] );
      } else {
        return new Kirby\Cms\Response( 'Not Found', 'text/plain', 404, [ 'X-omz13-WK' => 'ping-pong-disabled' ] );
      }
    }

    // normalize

    // header( "X-omz13-WK-what:" . $whatever );
    // header( "X-omz13-WK-ext:" . $extension );

    if ( $extension !== "txt" ) {
      if ( static::getConfigurationForKey( 'not-txt-notfound' ) == true ) {
        return new Kirby\Cms\Response( 'Not Found', 'text/plain', 404, [ 'x-omz13-wk' => 'not-txt' ] );
      } else {
        return null;
      }
    }

    if ( $whatever == "robots" ) {
      return new Kirby\Cms\Response( static::getRobots(), 'text/plain', 200, [ 'x-omz13-wk' => 'robots' ] );
    }

    $r = static::getConfigurationForKey( 'the-' . str_replace( '-', '', $whatever ) );
    if ( $r != null && $r != "" ) {
      return new Kirby\Cms\Response( str_replace( '\n', PHP_EOL, $r ), 'text/plain', 200, [ "x-omz13-wk" => "from-c" ] );
    }

    $wants = 'wellknown' . strtolower( str_replace( '-', '', $whatever ) );
    $r     = kirby()->site()->content()->get( $wants );
    if ( $r != null && $r != "" ) {
      return new Kirby\Cms\Response( $r, 'text/plain', 200, [ "x-omz13-wk" => "from-f" ] );
    }

    if ( static::getConfigurationForKey( 'notfound' ) == true ) {
      return new Kirby\Cms\Response( 'Not Found', 'text/plain', 404, [ 'x-omz13-wk' => 'fallthru' ] );
    } else {
      return null;
    }
  }//end processRequest()
}//end class
