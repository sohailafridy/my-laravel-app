<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductSummary;
use App\Models\Purchase;
use App\Models\StockMovement;




class InventoryController extends Controller
{
    public function index($id = null)
    {
        $single_product = null;
        $products = null;
        if ($id) {
            $single_product = Product::where('id', $id)->first();
        } else {
            $products = Product::latest()->paginate(10);
        }

        return view('admin.products', compact('products', 'single_product'));
    }

    public function store(Request $request)
    {

        $skuRule = 'required|string|max:255|unique:products';
        if($request->has('product_id') && $request->product_id) {
            $skuRule = 'required|string|max:255|unique:products,sku,' . $request->product_id;
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' =>  $skuRule,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:1,0',
        ]);

        if($request->has('product_id') && $request->product_id) {
            $product = Product::findOrFail($request->product_id);
            $product->name = $request->name;
            $product->sku = $request->sku;
            $product->status = $request->status ? 1 : 0;

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image && \Storage::disk('public')->exists($product->image)) {
                    \Storage::disk('public')->delete($product->image);
                }
                $imagePath = $request->file('image')->store('uploads/products', 'public');
                $product->image = $imagePath;
            }

            $product->save();

            return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/products', 'public');
        }


        $product = Product::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'status' => $request->status ? 1 : 1,
            'image' => $imagePath ?? null,
        ]);

        return redirect()->route('admin.products')->with('success', 'Product created successfully.');
    }

    public function restock()
    {
        return view('admin.restock');
    }
    public function restockStore(Request $request)
    {
        DB::beginTransaction();
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
        ]);

        Product::where('id', $data['product_id'])->increment('current_stock', $data['quantity']);


        Product::where('id', $data['product_id'])->update([
            'purchase_price' => $data['purchase_price'],
            'sale_price' => $data['sale_price'],
        ]);


        $purchase =Purchase::create([
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'unit_cost' => $data['purchase_price'],
            'total_cost' => $data['quantity'] * $data['purchase_price'],
            'purchase_date' => now(),
            'created_at' => now(),
        ]);

        StockMovement::create([
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'type' => 'in',
            'reference_id' => $purchase->id,
            'created_at' => now(),
        ]);

        $product_summary = ProductSummary::firstOrNew(
            ['product_id' => $request->product_id],  
            [
                'total_purchased' => 0,
                'total_cost'     => 0,
                'current_stock'  => 0,
                'last_purchase_date' => null,
            ]
        );
        $product_summary->total_purchased += $data['quantity'];
        $product_summary->total_cost += $data['quantity'] * $data['purchase_price'];
        $product_summary->current_stock += $data['quantity'];
        $product_summary->last_purchase_date = now();
        $product_summary->save();

        // $product = Product::findOrFail($request->product_id);
        // $product->stock += $request->quantity;
        // $product->save();

        DB::commit();
         return redirect()->route('admin.products')->with('success', 'Stock added successfully.');
    }

    public function purchases(Request $request)
    {
        $sku = $request->input('sku', $request->route('sku'));

        $query = DB::table('purchases')
            ->leftJoin('products', 'purchases.product_id', '=', 'products.id')
            ->select('purchases.*', 'products.name as product_name', 'products.sku as product_sku')
            ->latest('purchases.created_at');

        if (! empty($sku)) {
            $query->where(function ($query) use ($sku) {
                $query->where('products.sku', 'like', "%{$sku}%")
                    ->orWhere('products.name', 'like', "%{$sku}%");
            });
        }

        $purchases = $query->paginate(10)->withQueryString();

        return view('admin.purchases', compact('purchases', 'sku'));
    }

    public function reversePurchase($id)
    {
        DB::beginTransaction();
        $purchase = Purchase::findOrFail($id);

        $product = Product::findOrFail($purchase->product_id);
        $product->current_stock -= $purchase->quantity;
        $product->save();

        $product_summary = ProductSummary::where('product_id', $purchase->product_id)->first();
        $product_summary->current_stock -= $purchase->quantity;
        $product_summary->total_cost -= $purchase->total_cost;
        $product_summary->total_purchased -= $purchase->quantity;
        $product_summary->save();

        $stock_movement = StockMovement::where('reference_id', $id)->first();
        $stock_movement->delete();

        $purchase->delete();
        DB::commit();
        return redirect()->route('admin.purchases')->with('success', 'Restock entry reversed successfully!');
    }

    public function productMovement(Request $request)
    {
        $sku = $request->input('sku');
        
        $query = DB::table('stock_movements')
            ->leftJoin('products', 'stock_movements.product_id', '=', 'products.id')
            ->select('stock_movements.*', 'products.name as product_name', 'products.sku as product_sku')
            ->latest();
            
        if (!empty($sku)) {
            $query->where(function ($q) use ($sku) {
                $q->where('products.sku', 'like', "%{$sku}%")
                  ->orWhere('products.name', 'like', "%{$sku}%");
            });
        }
        
        $movements = $query->paginate(10)->withQueryString();

        return view('admin.product-movement', compact('movements', 'sku'));
    }

    public function productSummary(Request $request)
    {
        $query = DB::table('product_summary')
            ->leftJoin('products', 'product_summary.product_id', '=', 'products.id')
            ->select('product_summary.*', 'products.name as product_name', 'products.sku as product_sku')
            ->latest('product_summary.last_purchase_date');
            
        $summaries = $query->paginate(10);
        $overallProfit = DB::table('product_summary')->sum('total_profit');

        return view('admin.product-summary', compact('summaries', 'overallProfit'));
    }

    public function productLedger($id)
    {
        $product = Product::findOrFail($id);
        $movements = StockMovement::where('stock_movements.product_id', $id)
            ->leftJoin('purchases', 'stock_movements.reference_id', '=', 'purchases.id')
            ->select('stock_movements.*', 'purchases.unit_cost as purchase_price')
            ->latest()
            ->paginate(10);


        $product_summary = ProductSummary::where('product_id', $id)->first();

        // dd($movements->toArray());
        // exit;

        return view('admin.product-ledger', compact('product', 'movements', 'product_summary'));
    }
}
