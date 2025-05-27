<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'All Suppliers';
    }

    protected function getSuppliers()
    {
        return Supplier::searchable(['name', 'mobile', 'email', 'address'], false)->with('purchases', 'purchaseReturns')->orderBy('id', 'desc');
    }

    public function index()
    {
        $pageTitle = $this->pageTitle;
        $suppliers = $this->getSuppliers()->paginate(getPaginate());
        return view('admin.supplier.index', compact('pageTitle', 'suppliers'));
    }

    public function supplierPDF()
    {
        $pageTitle = $this->pageTitle;
        $suppliers = $this->getSuppliers()->get();
        return downloadPDF('pdf.supplier.list', compact('pageTitle', 'suppliers'));
    }

    public function supplierCSV()
    {
        $pageTitle = $this->pageTitle;
        $filename  = $this->downloadCsv($pageTitle, $this->getSuppliers()->get());
        return response()->download(...$filename);
    }

    protected function downloadCsv($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "name,email,mobile,company_name,address\n";
        $curSym   = gs('cur_sym');
        foreach ($data as $supplier) {
            // $payable    = $curSym . getAmount($supplier->totalPayableAmount());
            // $receivable = $curSym . getAmount($supplier->totalReceivableAmount());

            // Remove commas from company_name
            $cleanCompanyName = str_replace(',', '', $supplier->company_name);
            $cleanAddress = str_replace(',', '', $supplier->address);

            $column .= "$supplier->name,$supplier->email,$supplier->mobile,$cleanCompanyName,$cleanAddress\n";
        }

        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }

    public function store(Request $request, $id = 0)
    {
        $this->validation($request, $id);
        if ($id) {
            $notification = 'Supplier updated successfully';
            $supplier     = Supplier::findOrFail($id);
        } else {
            // $exist = Supplier::where('mobile', $request->mobile)->first();
            // if ($exist) {
            //     $notify[] = ['error', 'The mobile number already exists'];
            //     return back()->withNotify($notify);
            // }
            $notification = 'Supplier added successfully';
            $supplier     = new Supplier();
        }

        $this->saveSupplier($request, $supplier, $id);
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        $supplier->delete();
        Action::newEntry($supplier, 'DELETED');

        $notify[] = ['success', 'Supplier deleted successfully'];
        return back()->withNotify($notify);
    }
    protected function saveSupplier($request, $supplier, $id)
    {
        $supplier->name         = $request->name;
        $supplier->email        = strtolower(trim($request->email));
        $supplier->mobile       = $request->mobile;
        $supplier->company_name = $request->company_name;
        $supplier->address      = $request->address;
        $supplier->opening_balance = $request->opening_balance ?? 0.00;
        $supplier->booklet_no = $request->booklet_no;
        $supplier->save();
        Action::newEntry($supplier, $id ? 'UPDATED' : 'CREATED');
    }

    protected function validation($request, $id = 0)
    {
        $request->validate([
            'name'         => 'required|string|max:40',
            'booklet_no'   => 'required|string|max:100',
            'email'        => 'nullable',
            'mobile'       => 'nullable',
            'company_name' => 'nullable|string|max:40',
            'address'      => 'nullable|string|max:500',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);
    }

    public function import(Request $request)
    {
        $reqHeader    = ['name', 'email', 'mobile', 'company_name', 'address'];
        $importResult = importCSV($request, Supplier::class, $reqHeader, 'name');

        if ($importResult['data']) {
            $notify[] = ['success', $importResult['notify']];
        } else {
            $notify[] = ['error', 'No new data imported'];
        }
        return back()->withNotify($notify);
    }
}
