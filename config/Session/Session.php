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

    /**
     * @param mixed $value
     */
    public function set(string $name, $value): void
    {
        $this->session[$name] = $value;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return \array_key_exists($name, $this->session) ? $this->session[$name] : $default;
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->session);
    }

    public function all(): array
    {
        return $this->session;
    }

    public function replace(array $session): void
    {
        $this->session = [];
        foreach ($session as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @return mixed
     */
    public function remove(string $name)
    {
        $retval = null;
        if (\array_key_exists($name, $this->session)) {
            $retval = $this->session[$name];
            unset($this->session[$name]);
        }

        return $retval;
    }

    public function clear(): array
    {
        $return = $this->session;
        foreach (array_keys($this->session) as $key) {
            unset($this->session[$key]);
        }
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

    public function start(): void
    {
        session_start();
    }
    
    public function stop(): void
    {
        session_destroy();
    }

    public function getFlashes(): FlashMessages
    {
        return $this->flashes;
    }

    public function loadSession(): void
    {
        $session = &$_SESSION;
        $this->session = &$session;

        $this->flashes->initialize($session['flashes']);
    }
}
