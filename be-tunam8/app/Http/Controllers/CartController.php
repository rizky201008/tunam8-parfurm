<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    protected $cartItem;
    protected $productImage;

    public function __construct(CartItem $cartItem = new CartItem(), ProductImage $productImage = new ProductImage())
    {
        $this->cartItem = $cartItem;
        $this->productImage = $productImage;
    }

    public function getCarts(Request $request)
    {
        $isActive = $request->query('active') ?? 0;
        if ($isActive) {
            $cartItem = $this->cartItem->with('product')->where('user_id', $request->user()->id)->where('selected', 1)->get();
            $cartItem->map(function ($item) {
                $item->product->image = $item->product->images[0]->link;
                unset($item->product->images);
                return $item;
            });
            return response()->json($cartItem);
        }

        $this->cartItem->where('user_id', $request->user()->id)->update(['selected' => 0]);
        $cartItem = $this->cartItem->with('product')->where('user_id', $request->user()->id)->get();

        $cartItem->map(function ($item) {
            $item->product->image = $item->product->images[0]->link;
            unset($item->product->images);
            return $item;
        });

        return response()->json($cartItem);
    }

    public function deleteCart(Request $request)
    {
        $cartItem = $this->cartItem->where('user_id', $request->user()->id)->where('id', $request->id)->first();
        if ($cartItem->delete()) {
            return response()->json(
                [
                    'message' => 'Success: Cart item deleted',
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'message' => 'Err: Cannot delete cart item',
                ],
                500
            );
        }
    }

    public function addCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cartItem = $this->cartItem->where('user_id', $request->user()->id)->where('product_id', $validated['product_id'])->first();
        if ($cartItem !== null) {
            return response()->json(
                [
                    'message' => 'Err: Product already in cart',
                ],
                422
            );
        }

        $cartItem = $this->cartItem->create([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
            'quantity' => 1,
        ]);

        return response()->json(
            [
                'message' => 'Success: Cart item added',
            ],
            200
        );
    }

    public function updateCartItem(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:cart_items,id',
        ]);
        $cartItem = $request->user()->cartItems->where('id', $request->id)->first();
        $stok = $cartItem->product->stock + 1;
        $validated = Validator::make($request->all(), [
            'quantity' => "required|numeric|lt:" . $stok,
        ]);
        if ($validated->fails()) {
            return response()->json(['message' => $validated->errors()->first()], 422);
        }
        $cartItem->update($request->all());

        return response()->json(
            [
                'message' => 'Success: Cart item quantity updated',
            ],
            200
        );
    }
}
