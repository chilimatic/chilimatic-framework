<?php
/**
 *
 * @author j
 * Date: 2/22/15
 * Time: 12:33 PM
 *
 * File: observersubject.class.php
 */
namespace chilimatic\lib\traits;

trait ObserverSubject
{

    /**
     * @var \SplObjectStorage
     */
    private $observerList;

    /**
     * works only once
     */
    public function initTrait()
    {
        if ($this->observerList) {
            return;
        }

        $this->observerList = new \SplObjectStorage();
    }

    /**
     * @param \SplObserver $observer
     */
    public function attach(\SplObserver $observer)
    {
        $this->observerList->attach($observer);
    }

    /**
     * @param \SplObserver $observer
     */
    public function detach(\SplObserver $observer)
    {
        $this->observerList->detach($observer);
    }

    /**
     * @var
     */
    public function notify()
    {
        foreach ($this->observerList as $observer) {
            $observer->update($this);
        }
    }
}