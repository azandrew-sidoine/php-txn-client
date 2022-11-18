<?php

namespace Drewlabs\Psr7;

use Psr\Http\Client\NetworkExceptionInterface;

class NetworkException extends RequestException implements NetworkExceptionInterface
{
}
