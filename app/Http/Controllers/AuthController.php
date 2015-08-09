<?php namespace App\Http\Controllers;

use Validator;
use Event;
use Auth;
use Illuminate\Http\Request;
use View;

use App\Models\User;
use App\Models\Role;
use App\Models\Account;
use App\Models\Address;
use App\Models\Company;
use Flash;
use App\Events\UserLoggedInEvent;
use App\Events\UserRegisteredEvent;
use App\Http\Requests\CustomerRegisterFormRequest;

/**
 * AuthController
 *
 * @author Victor Lantigua <vmlantigua@gmail.com>
 */
class AuthController extends BaseController {

    /**
     * Constructor.
     */
    public function __construct()
    {
        $regCode = ( ! empty($_GET['reg'])) ? $_GET['reg'] : NULL;
        $company = ($regCode) ? Company::where('corp_code', $regCode)->first() : NULL;
        $queryString = ( ! empty($_SERVER['QUERY_STRING'])) ? '/?' . $_SERVER['QUERY_STRING'] : '';

        View::share('company', $company);
        View::share('queryString', $queryString);
    }

    /**
     * Logs out the user.
     *
     * @return Redirector
     */
    public function getLogout()
    {
        Auth::logout();

        return redirect('/');
    }

    /**
     * Shows the login form.
     *
     * @return Response
     */
    public function getLogin()
    {
        return view('site.login');
    }

    /**
     * Logs in a user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postLogin(Request $request)
    {
        $input = $request->only('email', 'password');

        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ];

        $this->validate($input, $rules);

        $user = User::validateCredentials($input['email'], $input['password']);

        if ( ! $user)
        {
            return $this->redirectBackWithError('The email or password you entered is not valid.');
        }

        Event::fire(new UserLoggedInEvent($user));

        return redirect('/dashboard');
    }

    /**
     * Shows the signup form.
     *
     * @return Response
     */
    public function getRegister()
    {
        return view('site.register');
    }

    /**
     * Registers a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postRegister(CustomerRegisterFormRequest $request)
    {
        $input = $request->all();

        $company = Company::where('corp_code', $input['registration_code'])->first();

        // Create user and customer account (See App\Observers\UserObserver)
        $user = new User;
        $user->company_id = $company->id;
        $user->firstname = $input['firstname'];
        $user->lastname = $input['lastname'];
        $user->email = $input['email'];
        $user->password = $input['password'];
        $user->active = TRUE;
        $user->role_id = Role::CUSTOMER;
        $user->save();

        // Create account address
        $address = new Address;
        $address->address1 = $input['address1'];
        $address->address2 = $input['address2'];
        $address->city = $input['city'];
        $address->state = $input['state'];
        $address->postal_code = $input['postal_code'];
        $address->country_id = $input['country_id'];
        $address->save();

        // Update account
        $account = $user->account()->first();
        $account->phone = $input['phone'];
        $account->mobile_phone = $input['mobile_phone'];
        $account->shippingAddress()->associate($address);
        $account->save();

        Event::fire(new UserRegisteredEvent($user));

        return redirect('dashboard');
    }

    /**
     * Shows the form for recovering a user's password.
     *
     * @return Response
     */
    public function getForgotPassword()
    {
        return view('site.forgot_password');
    }

    /**
     * Sends a password recovery token to the user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postForgotPassword(Request $request)
    {
        $input = $request->only('email');

        $this->validate($input, ['email' => 'required|email']);

        $user = User::where(['email', $input['email'], 'active' => TRUE])->first();

        if ( ! $user)
        {
            return $this->redirectBackWithError('The email address you entered isn\'t associated with an active account.');
        }

        // Send password recovery
        //Mailer::sendPasswordRecovery($user);
        // TODO: change message
        // Show success message regardless
        // @TODO: uncomment
        //
        //Flash::success('<a href="/reset-password?email=' . $user->email . '&token=' . $user->makePasswordRecoveryToken() . '">Click here to reset your password</a>');

        return $this->redirectBackWithSuccess('An email with instructions on how to reset your password has been sent.');
    }

    /**
     * Shows the form for resetting a user's password.
     *
     * @return Response
     */
    public function getResetPassword()
    {
        return view('site.reset_password');
    }

    /**
     * Resets a user's password.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postResetPassword(Request $request)
    {
        $input = $request->only('email', 'token', 'password', 'confirm_password');

        $rules = [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password'
        ];

        // Validate input
        $this->validate($input, $rules);

        // Verify user
        $user = User::where('email', $input['email'])->first();

        if ( ! $user || ! $user->verifyPasswordRecoveryToken($input['token']))
        {
            return $this->redirectBackWithError('Password reset failed.');
        }

        // Reset password
        $user->password = $input['password'];
        $user->save();

        return $this->redirectWithSuccess('login', 'Your password was reset successfully.');
    }

    /**
     * Activates a customer account.
     *
     * @param  Request $request
     * @return Response
     */
    public function getActivateAccount(Request $request)
    {
        $input = $request->only('email', 'activation_code');

        $validator = Validator::make($input, [
            'email'           => 'required|email',
            'activation_code' => 'required'
        ]);

        if ($validator->fails())
        {
            Flash::error($validator);
            return view('site.activate');
        }

        $user = User::where(['email' => $input['email'], 'activation_code' => $input['activation_code']])->first();

        if ( ! $user)
        {
            Flash::error('Account not found.');

            return view('site.activate');
        }

        $user->active = TRUE;
        $user->save();

        Auth::login($user);

        $this->redirectWithSuccess('dashboard', 'Your account was successfully activated.');
    }

    /**
     * Activates a customer account.
     *
     * @param  Request $request
     * @return Response
     */
    public function getResendActivationCode(Request $request)
    {
        $input = $request->only('email');

        $validator = Validator::make($input, [
            'email' => 'required|email',
        ]);

        if ($validator->fails())
        {
            Flash::error($validator);

            return view('site.activate');
        }

        $user = User::where(['email' => $input['email']])->first();

        if ( ! $user)
        {
            Flash::error('Account not found.');

            return view('site.activate');
        }

        $user->activation_code = User::makeActivationCode();
        $user->save();

        Flash::success('An activation code was sent to your email.');

        return view('site.activate');
    }
}
