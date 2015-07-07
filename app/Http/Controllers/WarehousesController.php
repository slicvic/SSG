<?php namespace App\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Validator;
use Auth;

use App\Models\Warehouse;
use App\Models\Package;
use App\Models\User;
use App\Helpers\Flash;

use Illuminate\Pagination\Paginator;
use App\Pdf\Warehouse as WarehousePdf;
use DB;

/**
 * WarehousesController
 *
 * @author Victor Lantigua <vmlantigua@gmail.com>
 */
class WarehousesController extends BaseAuthController {

    public function __construct(Guard $auth)
    {
        parent::__construct($auth);
        $this->middleware('agent');
    }

    /**
     * Displays a list of warehouses.
     */
    public function getIndex(Request $request)
    {
        $input['limit'] = $request->input('limit', 10);
        $input['sortby'] = $request->input('sortby', 'id');
        $input['order'] = $request->input('order', 'desc');
        $input['q'] = $request->input('q');
        $input['status'] = $request->input('status');

        $criteria['status'] = $input['status'];
        $criteria['q'] = $input['q'];
        $criteria['company_id'] = $this->user->company_id;
        $warehouses = Warehouse::search($criteria, $input['sortby'], $input['order'], $input['limit']);

        return view('warehouses.index', [
            'warehouses' => $warehouses,
            'input' => $input,
            'orderInverse' => ($input['order'] === 'asc' ? 'desc' : 'asc'),
        ]);
    }

    /**
     * Shows a specific warehouse.
     */
    public function getShow(Request $request, $id)
    {
        $warehouse = Warehouse::findOrFailByIdAndCurrentCompany($id);
        return view('warehouses.show', ['warehouse' => $warehouse]);
    }

    /**
     * Shows the form for creating a warehouse.
     */
    public function getCreate()
    {
        return view('warehouses.form', ['warehouse' => new Warehouse]);
    }

    /**
     * Creates a new warehouse.
     */
    public function postStore(Request $request)
    {
        $input = $request->all();

        // Validate input
        $validator = Validator::make($input['warehouse'], Warehouse::$rules);

        if ($validator->fails()) {
            $view = view('flash_messages.error', ['message' => $validator])->render();
            return response()->json(['status' => 'error', 'message' => $view]);
        }

        // Create warehouse
        $warehouse = Warehouse::create($input['warehouse']);

        // Create packages
        $consignee = User::find($input['warehouse']['consignee_user_id']);

        if ( ! empty($input['package'])) {
            foreach ($input['package'] as $package) {
                $package['warehouse_id'] = $warehouse->id;
                $package['ship'] = $consignee->autoship_packages;
                Package::create($package);
            }
        }

        Flash::success('Warehouse created.');
        return response()->json(['status' => 'ok', 'redirect_to' => '/warehouses/show/' . $warehouse->id]);
    }

    /**
     * Shows the form for editing a warehouse.
     */
    public function getEdit($id)
    {
        $warehouse = Warehouse::findOrFailByIdAndCurrentCompany($id);
        return view('warehouses.form', ['warehouse' => $warehouse]);
    }

    /**
     * Updates a specific warehouse.
     */
    public function postUpdate(Request $request, $id)
    {
        $input = $request->all();

        // Validate input
        $validator = Validator::make($input['warehouse'], Warehouse::$rules);

        if ($validator->fails()) {
            $view = view('flash_messages.error', ['message' => $validator])->render();
            return response()->json(['status' => 'error', 'message' => $view]);
        }

        // Update warehouse
        $warehouse = Warehouse::findByIdAndCurrentCompany($id);

        if ( ! $warehouse) {
            return response()->json(['status' => 'error', 'message' => 'Invalid warehouse ID.']);
        }

        $warehouse->update($input['warehouse']);

        // Update packages
        $warehouse->packages()->delete();

        if ( ! empty($input['package'])) {
            foreach ($input['package'] as $packageId => $packageData) {
                $packageData['warehouse_id'] = $warehouse->id;
                Package::create($packageData);
            }
        }

        Flash::success('Warehouse updated.');
        return response()->json(['status' => 'ok', 'redirect_to' => '/warehouses/edit/' . $warehouse->id]);
    }

    /**
     * Displays the warehouse receipt PDF.
     */
    public function getPrintReceipt(Request $request, $warehouseId)
    {
        $warehouse = Warehouse::findOrFail($warehouseId);
        WarehousePdf::getReceipt($warehouse);
    }

    /**
     * Displays the warehouse shipping label PDF.
     */
    public function getPrintLabel(Request $request, $warehouseId)
    {
        $warehouse = Warehouse::findOrFail($warehouseId);
        WarehousePdf::getLabel($warehouse);
    }

    /**
     * Returns a list of users for a jQuery autocomple field.
     *
     * @uses    ajax
     * @return  json
     */
    public function getAjaxShipperConsigneeAutocomplete(Request $request)
    {
        $input = $request->only('term', 'type');
        $response = [];

        if (strlen($input['term']) > 1) {
            foreach(User::findForAutocomplete($input['term'], [$this->user->company_id]) as $user) {
                $label = ($input['type'] == 'shipper') ? $user->present()->companyName() : trim($user->present()->fullName());
                $response[] = [
                    'id' => $user->id,
                    'label' => $label
                ];
            }
        }

        return response()->json($response);
    }

    /**
     * Retrieves a list of packages by warehouse ID.
     * @deprecated
     */
    public function getAjaxPackages(Request $request, $warehouseId)
    {
        $warehouse = Warehouse::findOrFail($warehouseId);
        return view('warehouses.index.packages', ['packages' => $warehouse->packages]);
    }
}
