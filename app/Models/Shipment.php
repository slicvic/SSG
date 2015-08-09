<?php namespace App\Models;

use DB;

use App\Presenters\PresentableTrait;
use App\Observers\ShipmentObserver;

/**
 * Shipment
 *
 * @author Victor Lantigua <vmlantigua@gmail.com>
 */
class Shipment extends BaseSearchable implements ISearchable {

    use CompanyTrait, PresentableTrait, CreatorUpdaterTrait;

    /**
     * The database table name.
     *
     * @var string
     */
    protected $table = 'shipments';

    /**
     * The presenter path.
     *
     * @var string
     */
    protected $presenter = 'App\Presenters\ShipmentPresenter';

    /**
     * A list of fillable fields.
     *
     * @var array
     */
    protected $fillable = [
        'carrier_id',
        'reference_number',
        'departed_at'
    ];

    /**
     * A list of sortable fields.
     *
     * {@inheritdoc}
     */
    protected static $sortable = [
        'id',
        'company_id',
        'departed_at',
        'created_at',
        'updated_at',
        'reference_number',
        'carrier_id'
    ];

    /**
     * Registers model events.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        Shipment::observe(new ShipmentObserver);
    }

    /**
     * Overrides parent method to sanitize attributes.
     *
     * @see parent::setAttribute()
     */
    public function setAttribute($key, $value)
    {
        switch ($key) {
            case 'departed_at':
                $value = date('Y-m-d H:i:s', strtotime($value));
                break;
            case 'reference_number':
                $value = strtoupper($value);
                break;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Gets the packages.
     *
     * @return Package[]
     */
    public function packages()
    {
        return $this->hasMany('App\Models\Package');
    }

    /**
     * Gets the carrier.
     *
     * @return Carrier
     */
    public function carrier()
    {
        return $this->belongsTo('App\Models\Carrier');
    }

    /**
     * Attaches the given packages to the shipment.
     *
     * WARNING: AFTER THIS OPERATION IS COMPLETE ONLY THE GIVEN PACKAGES
     * WILL REMAIN IN THE SHIPMENT.
     *
     * @param  array  $packageIds
     * @return void
     */
    public function syncPackages(array $packageIds)
    {
        // First lets detach the packages not in $packageIds
        Package::whereNotIn('id', $packageIds)
            ->where('shipment_id', $this->id)
            ->update(['shipment_id' => NULL]);

        // Next, we'll attach the given packages
        if (count($packageIds))
        {
            Package::whereIn('id', $packageIds)->update(['shipment_id' => $this->id]);
        }
    }

    /**
     * Calculates the total cost of the shipment.
     *
     * @return float
     */
    public function calculateTotalValue()
    {
        return DB::table('packages')->where('shipment_id', $this->id)
            ->sum('invoice_value');
    }

    /**
     * Finds all shipments with the given criteria.
     *
     * {@inheritdoc}
     */
    public static function search(array $criteria = [], $orderBy = 'id', $order = 'desc', $perPage = 15)
    {
        $query = Shipment::query()
            ->select('shipments.*')
            ->orderBy('shipments.' . self::sanitizeOrderBy($orderBy), self::sanitizeOrder($order))
            ->with('carrier', 'creator', 'updater', 'company');

        if ( ! empty ($criteria['company_id']))
        {
            $query->where('shipments.company_id', $criteria['company_id']);
        }

        if (isset($criteria['search']) && strlen($criteria['search']) > 2)
        {
            $searchTerm = '%' . $criteria['search'] . '%';

            $query
                ->leftJoin('packages', 'shipments.id', '=', 'packages.shipment_id')
                ->join('carriers', 'shipments.carrier_id', '=', 'carriers.id')
                ->groupBy('shipments.id')
                ->whereRaw('(
                    shipments.id LIKE ?
                    OR shipments.reference_number LIKE ?
                    OR packages.tracking_number LIKE ?
                    OR carriers.name LIKE ?
                    )', [
                    $searchTerm,
                    $searchTerm,
                    $searchTerm,
                    $searchTerm
                ]);
        }

        return $query->paginate($perPage);
    }
}
