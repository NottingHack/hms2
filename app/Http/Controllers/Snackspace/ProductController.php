<?php

namespace App\Http\Controllers\Snackspace;

use App\Http\Controllers\Controller;
use HMS\Entities\Snackspace\Product;
use HMS\Factories\Snackspace\ProductFactory;
use HMS\Repositories\Snackspace\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * Create a new controller instance.
     *
     * @param ProductRepository $productRepository
     * @param ProductFactory $productFactory
     */
    public function __construct(ProductRepository $productRepository, ProductFactory $productFactory)
    {
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;

        $this->middleware('feature:snackspace,vending');
        $this->middleware('can:snackspace.product.view')->only(['index', 'show']);
        $this->middleware('can:snackspace.product.edit')->only(['create', 'store', 'edit', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = $this->productRepository->paginateAll();

        return view('snackspace.product.index')
            ->with('products', $products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('snackspace.product.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'shortDescription' => 'required|string|max:255',
            'longDescription' => 'sometimes|nullable|string',
            'barcode' => 'sometimes|nullable|string|max:255',
            'price' => 'required|integer|min:1',

        ]);

        $product = $this->productFactory->create(
            $validatedData['price'],
            $validatedData['shortDescription'],
            $validatedData['longDescription']
        );

        if (array_key_exists('barcode', $validatedData)) {
            $product->setBarcode($validatedData['barcode']);
        }
        $this->productRepository->save($product);

        flash('New product added');

        return redirect()->route('snackspace.products.show', $product->getId());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\HMS\Entities\Snackspace\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return view('snackspace.product.show')
            ->with('product', $product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\HMS\Entities\Snackspace\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('snackspace.product.edit')
            ->with('product', $product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\HMS\Entities\Snackspace\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'shortDescription' => 'required|string|max:255',
            'longDescription' => 'sometimes|nullable|string',
            'barcode' => 'sometimes|nullable|string|max255',
            'price' => 'required|integer|min:1',

        ]);

        $product->setPrice($validatedData['price']);
        $product->setShortDescription($validatedData['shortDescription']);
        $product->setLongDescription($validatedData['longDescription']);

        if (array_key_exists('barcode', $validatedData)) {
            $product->setBarcode($validatedData['barcode']);
        }

        $this->productRepository->save($product);
        flash('Product updated')->success();

        return redirect()->route('snackspace.products.show', $product->getId());
    }
}
