<?php namespace App\Models;

use DB;
use Auth;

use App\Presenters\PresentableTrait;
use App\Observers\AccountObserver;

/**
 * Account
 *
 * @author Victor Lantigua <vmlantigua@gmail.com>
 */
class Account extends BaseSearchable implements ISearchable {

    use CompanyTrait, PresentableTrait, CreatorUpdaterTrait;

    /**
     * Rules for validation.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|min:3|alpha_num_spaces'
    ];

    /**
     * The database table name.
     *
     * @var string
     */
    protected $table = 'accounts';

    /**
     * The presenter path.
     *
     * @var string
     */
    protected $presenter = 'App\Presenters\AccountPresenter';

    /**
     * A list of fillable fields.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'user_id',
        'email',
        'name',
        'firstname',
        'lastname',
        'phone',
        'mobile_phone',
        'fax',
        'autoship'
    ];

    /**
     * A list of sortable fields.
     *
     * {@inheritdoc}
     */
    protected static $sortable = [
        'id',
        'company_id',
        'name',
        'email',
        'phone',
        'mobile_phone',
        'user_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Registers model events.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        Account::observe(new AccountObserver);
    }

    /**
     * Overrides parent method to sanitize attributes.
     *
     * @see parent::setAttribute()
     */
    public function setAttribute($key, $value)
    {
        switch ($key)
        {
            case 'firstname':
            case 'lastname':
                $value = ucwords(strtolower(trim($value)));
                break;
            case 'name':
                $value = ucwords($value);
                break;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Gets the account types.
     *
     * @return Role
     */
    public function tags()
    {
        return $this->belongsToMany('App\Models\AccountTag', 'accounts_tags', 'account_id', 'tag_id');
    }

    /**
     * Gets the shipping address.
     *
     * @return Address
     */
    public function shippingAddress()
    {
        return $this->belongsTo('App\Models\Address', 'shipping_address_id');
    }

    /**
     * Gets the billing address.
     *
     * @return Address
     */
    public function billingAddress()
    {
        return $this->belongsTo('App\Models\Address', 'billing_address_id');
    }

    /**
     * Gets the account user.
     *
     * NOTE: ONLY "CUSTOMER" ACCOUNTS HAVE A USER.
     *
     * @return User
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Finds accounts matching the provided search term and type for an
     * ajax autocomplete field.
     *
     * @param  string  $searchTerm
     * @return Builder
     */
    public static function autocompleteSearch($searchTerm)
    {
        $query = Account::query();

        $searchTerm = '%' . $searchTerm . '%';

        $query->whereRaw('
            (id LIKE ?
            OR name LIKE ?
            OR firstname LIKE ?
            OR lastname LIKE ?
            OR email LIKE ?
            OR phone LIKE ?
            OR fax LIKE ?
            OR mobile_phone LIKE ?)', [
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm,
            $searchTerm
        ]);

        return $query;
    }

    /**
     * Finds all accounts with the given criteria.
     *
     * {@inheritdoc}
     */
    public static function search(array $criteria = [], $orderBy = 'id', $order = 'desc', $perPage = 15)
    {
        $query = Account::query()
            ->orderBy(self::sanitizeOrderBy($orderBy), self::sanitizeOrder($order))
            ->with('company', 'shippingAddress');

        if (isset($criteria['company_id']))
        {
            $query->where('company_id', $criteria['company_id']);
        }

        if (isset($criteria['type']))
        {
            switch ($criteria['type'])
            {
                case 'shippers':
                    $query->whereHas('tags', function($q) {
                        $q->where('tag_id', AccountTag::SHIPPER);
                    });
                    break;

                case 'customers':
                    $query->whereHas('tags', function($q) {
                        $q->where('tag_id', AccountTag::CUSTOMER);
                    });
                    break;
            }
        }

        if ( ! empty($criteria['search']))
        {
            $searchTerm = '%' . $criteria['search'] . '%';

            $query->whereRaw('(
                id LIKE ?
                OR name LIKE ?
                OR firstname LIKE ?
                OR lastname LIKE ?
                OR email LIKE ?
                OR phone LIKE ?
                OR mobile_phone LIKE ?
                )', [
                $searchTerm,
                $searchTerm,
                $searchTerm,
                $searchTerm,
                $searchTerm,
                $searchTerm,
                $searchTerm
                ]);
        }

        return $query->paginate($perPage);
    }
}
