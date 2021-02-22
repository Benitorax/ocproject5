<?php
namespace Config\Router;

use Config\Router\FlashMessages;

class Session implements \IteratorAggregate, \Countable
{
    private $session;
    private $flashes;

    public function __construct($session)
    {
        foreach($session as $key => $value) {
            $this->session[$key] = $value;
        }
        $this->flashes = new FlashMessages();
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
        if (\array_key_exists($name, $this->name)) {
            $retval = $this->name[$name];
            unset($this->name[$name]);
        }

        return $retval;
    }

    public function clear()
    {
        $return = $this->session;
        $this->session = [];

        return $return;
    }

    // public function show($name)
    // {
    //     if(isset($_SESSION[$name]))
    //     {
    //         $key = $this->get($name);
    //         $this->remove($name);
    //         return $key;
    //     }
    // }

    /**
     * Returns an iterator for session.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->session);
    }

    /**
     * Returns the number of session.
     *
     * @return int The number of session
     */
    public function count()
    {
        return \count($this->session);
    }

    // public function remove($name)
    // {
    //     unset($_SESSION[$name]);
    // }

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
}