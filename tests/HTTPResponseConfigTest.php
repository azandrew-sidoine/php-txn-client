<?php

namespace Drewlabs\TxnClient\Tests;

use Drewlabs\TxnClient\HTTPResponseConfig;
use Drewlabs\TxnClient\HTTPResponseRequestOption;
use PHPUnit\Framework\TestCase;

class HTTPResponseConfigTest extends TestCase
{

    public function test_http_response_config_static_create_returns_instanceof_http_response_config()
    {
        $response = HTTPResponseConfig::create([]);
        $this->assertInstanceOf(HTTPResponseConfig::class, $response);
    }

    public function test_http_response_config_static_create_set_required_attributes_if_provided()
    {
        $response = HTTPResponseConfig::create([
            'txn_reference_key' => 't_ref',
            'txn_time_key' => 't_time',
            'txn_amount_key' => 't_montant',
            'txn_id_key' => 't_id',
            'txn_processor_key' => 't_processor',
            'request_options' => ['azlabsapi', 'Zwdhdw2nxl6HigJ688IGtrw5cqQQKbiF', 2]
        ]);

        $this->assertEquals('t_ref', $response->getTxnReferenceKey());
        $this->assertEquals('t_time', $response->getTxnTimeKey());
        $this->assertEquals('t_montant', $response->getTxnAmountKey());
        $this->assertEquals('t_id', $response->getTxnIdKey());
        $this->assertEquals('t_processor', $response->getTxnProcessorKey());
        $this->assertEquals([['key' => 'azlabsapi', 'value' => 'Zwdhdw2nxl6HigJ688IGtrw5cqQQKbiF', 'type' => 2]], array_map(function (HTTPResponseRequestOption $option) {
            return $option->toArray();
        }, $response->getRequestOptions()));
    }

    public function test_http_response_config_fluent_methods()
    {
        $response = new HTTPResponseConfig;

        $response->setTxnReferenceKey('t_reference')
            ->setTxnTimeKey('t_datetime')
            ->setTxnAmountKey('t_amount')
            ->setTxnIdKey('t_txn_id')
            ->setTxnProcessorKey('t_processor')
            ->setRequestOptions(new HTTPResponseRequestOption('sedanaapi', '9ZDrXR2iAMo1hvVz2OXWGWkX3W6lo19Q', 1));

        $this->assertEquals('t_reference', $response->getTxnReferenceKey());
        $this->assertEquals('t_datetime', $response->getTxnTimeKey());
        $this->assertEquals('t_amount', $response->getTxnAmountKey());
        $this->assertEquals('t_txn_id', $response->getTxnIdKey());
        $this->assertEquals('t_processor', $response->getTxnProcessorKey());
        $this->assertEquals([['key' => 'sedanaapi', 'value' => '9ZDrXR2iAMo1hvVz2OXWGWkX3W6lo19Q', 'type' => 1]], array_map(function (HTTPResponseRequestOption $option) {
            return $option->toArray();
        }, $response->getRequestOptions()));
    }
}
