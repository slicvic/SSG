<?php namespace App\Http\Controllers;

use Validator;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Address;
use App\Http\Requests\CustomerAccountFormRequest;

/**
 * CustomerAccountsController
 *
 * @author Victor Lantigua <vmlantigua@gmail.com>
 */
class CustomerAccountsController extends BaseAuthController {

    /**
     * Constructor.
     *
     * @param  Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        parent::__construct($auth);

        $this->middleware('auth.agentOrHigher');
    }

    /**
     * Shows a list of customer accounts.
     *
     * @param  Request  $request
     * @return Response
     */
    public function getIndex(Request $request)
    {
        $accounts = Account::customers()->mine()->get();

        return view('admin.accounts.customers.index', ['accounts' => $accounts]);
    }

    /**
     * Shows the form for creating a new customer account.
     *
     * @return Response
     */
    public function getCreate()
    {
        return view('admin.accounts.customers.create', [
            'account' => new Account,
            'address' => new Address
        ]);
    }

    /**
     * Creates a new customer account.
     *
     * @param  Request  $request
     * @return Redirector
     */
    public function postStore(CustomerAccountFormRequest $request)
    {
        $input = $request->all();

        // Create account
        $account = new Account;
        $account->name = $input['name'];
        $account->email = $input['email'];
        $account->phone = $input['phone'];
        $account->fax = $input['fax'];
        $account->mobile_phone = $input['mobile_phone'];
        $account->type_id = AccountType::CUSTOMER;

        // Create address
        $address = new Address;
        $address->address1 = $input['address1'];
        $address->address2 = $input['address2'];
        $address->city = $input['city'];
        $address->state = $input['state'];
        $address->postal_code = $input['postal_code'];
        $address->country_id = $input['country_id'];
        $address->save();

        $account->shippingAddress()
            ->associate($address)
            ->save();

        return $this->redirectWithSuccess('accounts/customers', 'Customer created.');
    }

    /**
     * Shows the form for editing an customer account.
     *
     * @param  int  $id
     * @return Response
     */
    public function getEdit($id)
    {
        $account = Account::findMineOrFail($id);

        return view('admin.accounts.customers.edit', [
            'account' => $account,
            'address' => $account->shippingAddress ?: new Address
        ]);
    }

    /**
     * Updates a specific customer account.
     *
     * @param  Request  $request
     * @param  int      $id
     * @return Redirector
     */
    public function postUpdate(CustomerAccountFormRequest $request, $id)
    {
        $input = $request->all();

        // Update account
        $account = Account::findMineOrFail($id);
        $account->name = $input['name'];
        $account->email = $input['email'];
        $account->phone = $input['phone'];
        $account->fax = $input['fax'];
        $account->mobile_phone = $input['mobile_phone'];

        // Update address
        $address = ($account->shippingAddress) ?: new Address;
        $address->address1 = $input['address1'];
        $address->address2 = $input['address2'];
        $address->city = $input['city'];
        $address->state = $input['state'];
        $address->postal_code = $input['postal_code'];
        $address->country_id = $input['country_id'];
        $address->save();

        $account->shippingAddress()
            ->associate($address)
            ->save();

        return $this->redirectBackWithSuccess('Customer updated.');
    }
}
