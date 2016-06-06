<?php

namespace Tokenly\PlatformAdmin\XChainHooks;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tokenly\PlatformAdmin\Meta\PlatformAdminMeta;

class XChainHooksManager
{

    static $XCHAIN_CLIENT_MOCK_BUILDER;

    protected $xchain_client_mock_builder = null;

    public function init($xchain_client_mock_builder) {
        self::$XCHAIN_CLIENT_MOCK_BUILDER = $xchain_client_mock_builder;

        Event::listen('xchainMock.callBegin', function($data) {
            Log::debug("xchainMock.callBegin: ".json_encode($data, 192));
        });

        Event::listen('xchainMock.callEnd', function($return_value, $call_data) {
            // Log::debug("xchainMock.callEnd: ".json_encode($return_value, 192));

            $sample_method_name = 'hook_'.strtolower($call_data['method']).'_'.preg_replace('![^a-z0-9]+!i', '_', trim($call_data['path'], '/'));
            if (method_exists($this, $sample_method_name)) {
                call_user_func([$this, $sample_method_name], $call_data, $return_value);
            }

        });

        // set balances in memory
        $this->updateXChainMockBalances();

        return $this;
    }

    protected function hook_post_unmanaged_addresses($call_data, $return_value) {
        $address_id = $return_value['id'];
        Log::debug("hook_unmanaged_addresses \$address_id=$address_id");

        $xchain_balances = PlatformAdminMeta::get('xchain_balances');
        if (!$xchain_balances) { $xchain_balances = []; }
        $default_balances = ['BTC' => 0];
        $xchain_balances[$address_id] = ['id' => $address_id, 'address' => $return_value['address'], 'balances' => $default_balances];

        // set and update mock balances
        PlatformAdminMeta::set('xchain_balances', $xchain_balances);

        $this->updateXChainMockBalances();
    }


    public function updateXChainMockBalances() {
        $balances_by_address = [];
        $xchain_balances = PlatformAdminMeta::get('xchain_balances', []);
        foreach($xchain_balances as $address_id => $xchain_balance_entry) {
            self::$XCHAIN_CLIENT_MOCK_BUILDER->setBalances($xchain_balance_entry['balances'], $xchain_balance_entry['id']);
            $balances_by_address[$xchain_balance_entry['address']] = $xchain_balance_entry['balances'];
        }

        self::$XCHAIN_CLIENT_MOCK_BUILDER->setBalancesByAddress($balances_by_address);
    }

}
