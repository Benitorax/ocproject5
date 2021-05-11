<?php

namespace Framework\Response;

use Exception;
use ArrayObject;
use Framework\Response\Response;

class JsonResponse extends Response
{
    protected string $data;

    // Encode <, >, ', &, and " characters in the JSON, making it also safe to be embedded into HTML.
    // 15 === JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
    public const DEFAULT_ENCODING_OPTIONS = 15;

    protected int $encodingOptions = self::DEFAULT_ENCODING_OPTIONS;

    /**
     * @param mixed $data
     */
    public function __construct($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        parent::__construct('', $status, $headers);

        if ($json && !\is_string($data) && !is_numeric($data) && !\is_callable([$data, '__toString'])) {
            throw new Exception(sprintf(
                '%s: If $json is set to true, argument $data must be a string'
                    . ' or object implementing __toString(), "%s" given.',
                __METHOD__,
                gettype($data)
            ));
        }

        if (null === $data) {
            $data = new ArrayObject();
        }

        $json ? $this->setJson($data) : $this->setData($data);
    }

    /**
     * Sets a raw string containing a JSON document to be sent.
     *
     * @param string $json
     */
    public function setJson($json): self
    {
        $this->data = $json;

        return $this->update();
    }

    /**
     * Sets the data to be sent as JSON.
     *
     * @param mixed $data
     * @throws Exception
     */
    public function setData($data = []): self
    {
        try {
            $data = json_encode($data, $this->encodingOptions);
        } catch (Exception $e) {
            if ('Exception' === \get_class($e) && 0 === strpos($e->getMessage(), 'Failed calling ')) {
                throw $e->getPrevious() ?: $e;
            }
            throw $e;
        }

        if (\PHP_VERSION_ID >= 70300 && (\JSON_THROW_ON_ERROR & $this->encodingOptions)) {
            return $this->setJson((string) $data);
        }

        if (\JSON_ERROR_NONE !== json_last_error()) {
            throw new Exception(json_last_error_msg());
        }

        return $this->setJson((string) $data);
    }

    /**
     * Updates the content and headers according to the JSON data.
     */
    protected function update(): self
    {
        $this->headers->set('Content-Type', 'application/json');
        $this->setContent($this->data);

        return $this;
    }
}
