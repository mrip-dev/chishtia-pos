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
        $column   = "Name,Mobile,E-mail,Payable,Receivable,Address\n";
        $curSym   = gs('cur_sym');
        foreach ($data as $supplier) {
            $payable    = $curSym . getAmount($supplier->totalPayableAmount());
            $receivable = $curSym . getAmount($supplier->totalReceivableAmount());

            $column .= "$supplier->name,$supplier->mobile,$supplier->email,$payable,$receivable,$supplier->address\n";
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
            $exist = Supplier::where('mobile', $request->mobile)->first();
            if ($exist) {
                $notify[] = ['error', 'The mobile number already exists'];
                return back()->withNotify($notify);
            }
            $notification = 'Supplier added successfully';
            $supplier     = new Supplier();
        }

        $this->saveSupplier($request, $supplier, $id);
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    protected function saveSupplier($request, $supplier, $id)
    {
        $supplier->name         = $request->name;
        $supplier->email        = strtolower(trim($request->email));
        $supplier->mobile       = $request->mobile;
        $supplier->company_name = $request->company_name;
        $supplier->address      = $request->address;
        $supplier->save();
        Action::newEntry($supplier, $id ? 'UPDATED' : 'CREATED');
    }

    protected function validation($request, $id = 0)
    {
        $request->validate([
            'name'         => 'required|string|max:40',
            'email'        => 'required|string|email|unique:suppliers,email,' . $id,
            'mobile'       => 'required|regex:/^([0-9]*)$/|unique:suppliers,mobile,' . $id,
            'company_name' => 'nullable|string|max:40',
            'address'      => 'nullable|string|max:500',
        ]);
    }

    public function import(Request $request)
    {
        $reqHeader    = ['name', 'email', 'mobile', 'company_name', 'address'];
        $importResult = importCSV($request, Supplier::class, $reqHeader, $unique = "mobile");

        if ($importResult['data']) {
            $notify[] = ['success', $importResult['notify']];
        } else {
            $notify[] = ['error', 'No new data imported'];
        }
        return back()->withNotify($notify);
    }
}
