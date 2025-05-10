<?php

use Illuminate\Support\Facades\Route;


    Route::controller('DataEntryReportController')->prefix('reports/data-entry')->name('report.data.entry.')->group(function () {
        Route::get('product', 'product')->name('product');
        Route::get('customer', 'customer')->name('customer');
        Route::get('supplier', 'supplier')->name('supplier');
        Route::get('purchase', 'purchase')->name('purchase');
        Route::get('purchase-return', 'purchaseReturn')->name('purchase.return');
        Route::get('sale', 'sale')->name('sale');
        Route::get('sale-return', 'saleReturn')->name('sale.return');
        Route::get('adjustment', 'adjustment')->name('adjustment');
        Route::get('transfer', 'transfer')->name('transfer');
        Route::get('expense', 'expense')->name('expense');
        Route::get('supplier-payment', 'supplierPayment')->name('supplier.payment');
        Route::get('customer-payment', 'customerPayment')->name('customer.payment');
    });

