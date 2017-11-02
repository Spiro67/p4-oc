<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 30/10/2017
 * Time: 15:53
 */

namespace AppBundle\Service;

use Stripe\Charge;
use Stripe\Error\Card;

class Stripe
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $apiToken;

    /**
     * Stripe constructor.
     *
     * @param string $apiKey
     * @param string $apiToken
     */
    public function __construct(
        $apiKey,
        $apiToken
    ) {
        $this->apiKey = $apiKey;
        $this->apiToken = $apiToken;
    }

    /** @return string */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /** @return string */
    public function getApiToken()
    {
        return $this->apiToken;
    }
}