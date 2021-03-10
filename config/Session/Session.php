<?php
namespace Config\Session;

use Exception;
use ArrayIterator;
use Config\Session\FlashMessages;

class Session implements \IteratorAggregate, \Countable
{
    private array $session;
    //private $flashName = 'flashes';
    private FlashMessages $flashes;

    public function __construct()
    {
        if (\PHP_SESSION_ACTIVE === session_status()) {
            throw new Exception('Failed to start the session: already started by PHP.');
        }

        if (\PHP_SESSION_NONE === session_status()) {
            $this->start();
        }

        $this->flashes = new FlashMessages();

        $this->loadSession();
    }

    public function set($name, $value)
    {
        $this->session[$name] = $value;
    }

    public function get($name, $default = null)
    {
        return \array_key_exists($name, $this->session) ? $this->session[$name] : $default;
    }

    public function has($name)
    {
        return \array_key_exists($name, $this->session);
    }

    public function all()
    {
        return $this->session;
    }

    public function replace(array $session)
    {
        $this->session = [];
        foreach ($session as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function remove($name)
    {
        $retval = null;
        if (\array_key_exists($name, $this->session)) {
            $retval = $this->session[$name];
            unset($this->session[$name]);
        }

        return $retval;
    }

    public function clear()
    {
        $return = $this->session;
        $this->session = [];

        return $return;
    }

    /**
     * Returns an iterator for session.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->session);
    }

    /**
     * Returns the number of session.
     */
    public function count(): int
    {
        return \count($this->session);
    }

    public function start()
    {
        session_start();
    }
    
    public function stop()
    {
        session_destroy();
    }

    public function getFlashes(): FlashMessages
    {
        return $this->flashes;
    }

    public function loadSession()
    {
        $session = &$_SESSION;
        $this->session = &$session;
        $this->flashes->initialize($session['flashes']);
    }
}
