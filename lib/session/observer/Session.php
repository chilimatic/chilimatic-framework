<?php
/**
 *
 * @author j
 * Date: 2/22/15
 * Time: 12:27 PM
 *
 * File: session.class.php
 */

namespace chilimatic\lib\session\observer;

class Session implements \SplObserver
{

    /**
     * @var \chilimatic\lib\session\handler\Session $sessionHandler
     */
    private $sessionHandler;

    /**
     * @param \chilimatic\lib\session\handler\Session $sessionHandler
     */
    public function __construct(\chilimatic\lib\session\handler\Session $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * this is triggered in the destruct method of the session engine
     * -> to not lose the session data abstraction we need to write it to the
     * session before the engine is gone.
     *
     * @param \SplSubject $splSubject
     */
    public function update(\SplSubject $splSubject)
    {
        $this->sessionHandler->save();
    }


}