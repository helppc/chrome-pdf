<?php declare(strict_types=1);

namespace HelpPC\ChromePdf\Driver;

use HelpPC\ChromePdf\Exception\APIException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SelfHostedBrowserless extends Browserless
{
    public function __construct(HttpClientInterface $client, string $apiUrl, ?string $apiKey = NULL)
    {
        $this->apiUrl = $apiUrl;
        parent::__construct($client, $apiKey);
    }
}