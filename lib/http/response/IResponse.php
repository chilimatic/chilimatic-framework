<?php

namespace chilimatic\lib\http\response;

/**
 *
 * @author j
 * Date: 4/13/15
 * Time: 11:33 PM
 *
 * File: IResponse.phtml
 */

Interface IResponse {
    /**
     * suffix to identify callback functions
     *
     * @var string
     */
    const CALLBACK_PREFIX = 'cb_';

    /**
     * prefix to identify callback functions
     *
     * @var string
     */
    const CALLBACK_SUFFIX = '_cb';
}