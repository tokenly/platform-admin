<?php

namespace Tokenly\PlatformAdmin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use TKAccounts\Models\Address;
use Tokenly\PlatformAdmin\Controllers\Controller;
use Tokenly\PlatformAdmin\Meta\PlatformAdminMeta;

class XChainBalancesController extends Controller
{


    public function index()
    {
        return view('platformAdmin::xchain.balances.index', [
            'xchain_balances' => PlatformAdminMeta::get('xchain_balances'),
        ]);
    }

    public function edit($id)
    {
        PlatformAdminMeta::get('xchain_balances');

        return view('platformAdmin::xchain.balances.edit', [
            'balance_entry' => $this->requireBalanceByID($id),
        ]);
    }

    public function update($id, Request $request)
    {
        $balances = $request->input('balances');
        $balances = json_decode($balances, true);
        if (!is_array($balances)) {
            return $this->buildFailedValidationResponse($request, ['balances' => "Balances was invalid. Please check the JSON format"]);
        }

        $xchain_balances = PlatformAdminMeta::get('xchain_balances');
        if (!isset($xchain_balances[$id])) { throw new HttpResponseException(response('Balance not found', 404)); }

        $xchain_balances[$id]['balances'] = $balances;
        PlatformAdminMeta::set('xchain_balances', $xchain_balances);

        // refresh all balance
        app('Tokenly\PlatformAdmin\XChainHooks\XChainHooksManager')->updateXChainMockBalances();
        $this->refreshAllBalances();

        return view('platformAdmin::xchain.balances.update', []);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // delete
        $xchain_balances = PlatformAdminMeta::get('xchain_balances');
        if (!isset($xchain_balances[$id])) { throw new HttpResponseException(response('Balance not found', 404)); }
        unset($xchain_balances[$id]);
        PlatformAdminMeta::set('xchain_balances', $xchain_balances);

        // refresh all balance
        $this->refreshAllBalances();

        return view('platformAdmin::xchain.balances.destroy', []);
    }
    // ------------------------------------------------------------------------

    protected function refreshAllBalances() {
        // find all addresses by id
        $xchain = app('Tokenly\XChainClient\Client');
        $xchain_balances = PlatformAdminMeta::get('xchain_balances');
        foreach($xchain_balances as $xchain_balance) {
            Log::debug("refreshAllBalances \$xchain_balance['id']=".json_encode($xchain_balance['id'], 192));
            foreach (Address::where('xchain_address_id', $xchain_balance['id'])->get() as $address) {
                // delete all balances
                Log::debug("deleting from address_balances WHERE address_id=$address->id");
                DB::table('address_balances')->where('address_id', $address->id)->delete();

                // update balances
                $balances = $xchain->getBalances($address->address, true);
                echo "\$balances: ".json_encode($balances, 192)."\n";
                if($balances AND count($balances) > 0){
                    Address::updateAddressBalances($address->id, $balances);
                }
            }
        }
    }

    protected function requireBalanceByID($id) {
        $all_balances = PlatformAdminMeta::get('xchain_balances');
        if (!isset($all_balances[$id])) {
            throw new HttpResponseException(response('Resource not found', 404));
        }
        return $all_balances[$id];
    }

    protected function getValidationRules() {
        return [
            'xchainMockActive' => 'boolean',
        ];
    }    
}
