<?php

namespace Drewlabs\TxnClient\Http;

use Psr\Http\Client\NetworkExceptionInterface;

class NetworkException extends RequestException implements NetworkExceptionInterface
{
}
