<?php
namespace Config\Security\Csrf;

class CsrfTokenGenerator
{
    /**
     * @var int $entropy The amount of entropy collected for each token (in bits)
     */
    private $entropy = 256;

    public function generate()
    {
        // Generate an URI safe base64 encoded string that does not contain "+",
        // "/" or "=" which need to be URL encoded and make URLs unnecessarily
        // longer.
        $bytes = random_bytes($this->entropy / 8);

        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
