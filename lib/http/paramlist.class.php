<?php
/**
 * Created by PhpStorm.
 * User: j
 * Date: 21.01.14
 * Time: 19:32
 *
 *
 * HTTP_ParamList based on the w3 rfc2616
 */

namespace chilimatic\lib\http;

/**
 * Class HTTP_ParamList
 *
 * @package chilimatic\http
 */
class HTTP_ParamList {

    /**
     * Accept param
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     *
     * filled with default value it accepts everything that's text
     * @example Accept: text/*, text/html, text/html;level=1
     *
     * @var array
     */
    public $accept = array(
        array('value' => "text/*", "q" => 0)
    );

    /**
     * Accept-Charset
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     *
     * @example Accept-Charset: iso-8859-5, unicode-1-1;q=0.8
     *
     * the key represents the charset the value the "quality" (preference)
     * filled with default charset utf8 q=0
     *
     * @var array
     */
    public $accept_charset = array(
        'utf8' => 0
    );

    /**
     * Accept-Encoding
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Accept-Encoding: compress, gzip
     *
     * key represents the value and the value the quality setting
     *
     * @var array
     */
    public $accept_encoding = array(
        'gzip' => 0,
        'compress' => 0
    );

    /**
     * Accept-Language
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Accept-Language: da, en-gb;q=0.8, en;q=0.7
     *
     * key represents the value and the value the quality setting
     *
     * @var array
     */
    public $accept_language = array(
        'en' => 0
    );

    /**
     * Accept-Ranges
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Accept-Ranges: bytes
     *
     *
     * @var array
     */
    public $accept_ranges = array();

    /**
     * Age
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Age: 2147483648
     *
     * at least 2^31 Bits HTTP 1.1
     *
     * age must contain a lifetime value
     * based on a unix timestamp
     *
     * @var int
     */
    public $age = null;

    /**
     * Allow
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Allow: GET, DELETE, PUT
     *
     * allowed Method
     *
     * @var null
     */
    public $allow = null;

    /**
     * Authorization
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Autorization: credentials
     *
     * for couchdb they are encrypted
     *
     * @var string
     */
    public $authorization = '';


    /**
     * Cache-Control
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Cache-Control: private, community="UCI"
     *
     * cache-directive = cache-request-directive | cache-response-directive
     *
     * @var array
     */
    public $cache_control = array(
        'no-cache' => 0
    );

    public $charset = '';

    /**
     * Connection
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Connection: close
     *
     * @var string
     */
    public $connection = '';

    /**
     * Content-Encoding
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Content-Encoding: gzip
     *
     *
     * @var string
     */
    public $content_encoding = '';


    /**
     * Content-Language
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Content-Language: da
     *
     * @var string
     */
    public $content_language = '';

    /**
     * Content-Length
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Content-Length: 3495
     *
     * @var int
     */
    public $content_length = '';



    /**
     * Content-Location
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Content-Location: /path1/path2/filename
     *
     * absoluteURI | relativeURI
     *
     * @var string
     */
    public $content_location = '';


    /**
     * Content-MD5
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Content-MD5 : e4e6ca42342f95978a17c6257593c1e1
     *
     * is an MD5 digest of the entity-body
     *
     * Content-MD5   = "Content-MD5" ":" md5-digest
     * md5-digest   = <base64 of 128 bit MD5 digest as per RFC 1864>
     *
     * @var string
     */
    public $content_md5 = '';


    /**
     * Content-Range
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Content-Range : bytes 21010-47021/47022
     *
     * @var string
     */
    public $content_range = '';


    /**
     * Content-Type
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Content-Type: text/html; charset=ISO-8859-4
     *
     * @var string
     */
    public $content_type = array(
        'value' => 'text/*', 'charset' => 'utf-8'
    );

    /**
     * Date
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Date: Tue, 15 Nov 1994 08:12:31 GMT
     *
     * @var string
     */
    public $date = '';


    /**
     * Expect
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Expect: 101-continue
     *
     * array(
     *       'value' => '101-continue' , 'token' => 'tokenvalue'
     *       );
     *
     * @var array
     */
    public $expect = array();


    /**
     * Expires
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Expires: Thu, 01 Dec 1994 16:00:00 GMT
     *
     *
     * @var string
     */
    public $expires = '';

    /**
     * From
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example From : sender@example.com
     *
     * @var string
     */
    public $from = '';

    /**
     * Host
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Host: www.example.com | www.example.com:80
     *
     * @var string
     */
    public $host = '';

    /**
     * If-Match
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example If-Match: "xyzzy"
     *
     *
     * @var string
     */
    public $if_match = '';


    /**
     * If-Modified-Since
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example If-Modified-Since : Sat, 29 Oct 1994 19:43:31 GMT
     *
     * @var string
     */
    public $if_modified_since = '';

    /**
     * If-None-Match
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example If-None-Match: "xyzzy"
     *
     * @var string
     */
    public $if_none_match = '';


    /**
     * If-Range
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example If-Range: Sat, 29 Oct 1994 19:43:31 GMT
     *
     * @var string
     */
    public $if_range = '';

    /**
     * If-Unmodified-Since
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example If-Unmodified-Since : Sat, 29 Oct 1994 19:43:31 GMT
     *
     * @var string
     */
    public $if_unmodified_since = '';


    /**
     * Last-Modified
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Last-Modified : Sat, 29 Oct 1994 19:43:31 GMT
     *
     * @var string
     */
    public $last_modified = '';


    /**
     * Location
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Location : http://www.example.com/new_path
     *
     * @var string
     */
    public $location = '';

    /**
     * Max-Forwards
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Max-Forwards : 1
     *
     * @var int
     */
    public $max_forwards = 0;


    /**
     * Pragma
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Pragma : no-cache
     *
     * @var array
     */
    public $pragma = array();


    /**
     * Proxy-Authenticate
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Proxy-Authenticate : 1#challenge
     *
     * @var string
     */
    public $proxy_authenticate = '';


    /**
     * Proxy-Authorization
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Proxy-Authenticate : credentials
     *
     * @var string
     */
    public $proxy_authorization = '';

    /**
     * Range
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Range : bytes=0-499
     *
     * array ('0-499');
     *
     * @var string
     */
    public $range = array ();

    /**
     * @var int
     */
    public $delta_seconds = '';


    /**
     * Referer
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Referer : http://www.example.com/some/parts?param=1
     *
     * @var string
     */
    public $referer = '';

    /**
     * Retry-After
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Retry-After : 120
     * @example Retry-After: Fri, 31 Dec 1999 23:59:59 GMT
     *
     *
     * @var string
     */
    public $retry_after = '';

    /**
     * Server
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Server : CERN/3.0 libwww/2.17
     *
     * @var string
     */
    public $server = '';


    /**
     * TE
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example TE: deflate
     *
     * array('deflate' => 0 )
     *
     * @var string
     */
    public $te = array();



    /**
     * Trailer
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Trailer : 1#field-name
     *
     * @var string
     */
    public $trailer = '';


    /**
     * Transfer-Encoding
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Transfer-Encoding: chunked
     *
     * @var string
     */
    public $transfer_encoding = '';

    /**
     * Upgrade
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Upgrade : HTTP/2.0, SHTTP/1.3, IRC/6.9, RTA/x11
     *
     * array('HTTP/2.0', 'SHTTP/1.3')
     *
     * @var array
     */
    public $upgrade = array();

    /**
     * User-Agent
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example User-Agent: CERN-LineMode/2.15 libwww/2.17b3
     *
     * array('CERN-LineMode/2.15', 'libwww/2.17b3');
     *
     * @var array
     */
    public $user_agent = array();


    /**
     * Vari
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Vary : #fieldname
     *
     * @var string
     */
    public $vari = '';


    /**
     * Via
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Via: 1.0 fred, 1.1 nowhere.com (Apache/1.1)
     *
     * array('1.0 fred','1.1 nowhere.com (Apache/1.1)')
     * @var array
     */
    public $via = array();

    /**
     * Warning
     *
     * @source http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     * @example Warning: You suck ;)
     *
     * @var string
     */
    public $warning = '';


    /**
     * WWW-Authenticate
     *
     * @example  WWW-Authenticate: 1#challenge
     * @var string
     */
    public $www_authenticate = '';


    public $comma_sep_list = array('');
}