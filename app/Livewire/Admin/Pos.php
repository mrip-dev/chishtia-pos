<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Product; // ✅ Use main Product model
use Livewire\Component;

class Pos extends Component
{
    public $products = [];
    public $cart = [];
    public $orderStatus = 'draft';
    public $orderId = null;

    public function mount()
    {
        // ✅ Fetch all products from main Product model
        $this->products = Product::all();
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (!$product) return;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image ?? 'default.png',
            ];
        }
    }

    public function increaseQty($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        }
    }

    public function decreaseQty($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] > 1) {
                $this->cart[$productId]['quantity']--;
            } else {
                unset($this->cart[$productId]);
            }
        }
    }

    public function removeItem($productId)
    {
        unset($this->cart[$productId]);
    }

    public function getTotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function saveOrder()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Cart is empty. Please add some products before saving.');
            return;
        }

        $order = Order::updateOrCreate(
            ['id' => $this->orderId],
            ['status' => 'saved', 'total' => $this->total]
        );

        // ✅ Delete previous order items and recreate them
        $order->items()->delete();

        foreach ($this->cart as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        // ✅ Reset cart and status for new order
        $this->cart = [];
        $this->orderId = null;
        $this->orderStatus = 'draft';

        session()->flash('success', 'Order saved successfully! Ready for a new order.');
    }

    public function render()
    {
        return view('livewire.admin.pos');
    }
}
