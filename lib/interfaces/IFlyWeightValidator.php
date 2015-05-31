<<<<<<< HEAD
<?php
/**
 * User: j
 * Date: 11.02.14
 * Time: 10:52
 */

namespace chilimatic\lib\route\validator;
namespace chilimatic\lib\interfaces;
/**
 * Interface ValidatorInterface
 *
 * @package chilimatic\lib\route\validator
 */
interface IFlyWeightValidator {

    /**
     * abstract validation
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function __invoke( $value );

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function validate($value);
=======
<?php
/**
 * User: j
 * Date: 11.02.14
 * Time: 10:52
 */

namespace chilimatic\lib\route\validator;
namespace chilimatic\lib\interfaces;
/**
 * Interface ValidatorInterface
 *
 * @package chilimatic\lib\route\validator
 */
interface IFlyWeightValidator {

    /**
     * abstract validation
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function __invoke( $value );

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function validate($value);
>>>>>>> 336a13ce09982d8ab14d6fadbb1d8dd97927b344
}