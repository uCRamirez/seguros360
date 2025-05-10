<?php

namespace App\Imports;

use App\Classes\Common;
use App\Models\Category;
use App\Models\Product;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Str;

class ProductImport implements ToArray, WithHeadingRow
{
    public function array(array $products)
    {

        DB::transaction(function () use ($products) {
            $company = company();
            $user = user();

            foreach ($products as $product) {

                if (
                    !array_key_exists('name', $product) || !array_key_exists('category', $product) || !array_key_exists('product_type', $product) ||
                    !array_key_exists('price', $product) || !array_key_exists('tax_rate', $product) || !array_key_exists('internal_code', $product)
                ) {
                    throw new ApiException('Field missing from header.');
                }

                // Name
                $productName = trim($product['name']);

                if ($productName != '') {
                    $productCount = Product::where('name', $productName)->count();
                    if ($productCount > 0) {
                        throw new ApiException('Product ' . $productName . ' Already Exists');
                    }

                    // Category
                    $categoryName = trim($product['category']);
                    $category = Category::where('name', $categoryName)->first();
                    if (!$category) {
                        $category = new Category();
                        $category->name = 'electronics';
                        $category->save();
                    }

                    // Product Type
                    $productType = trim($product['product_type']);

                    // Price
                    $price = $product['price'] && $product['price'] != '' ? trim($product['price']) : null;
                    if ($price != null) {
                        $price = str_replace(',', '', $price);
                        $price = str_replace('-', '', $price);
                        $price = is_numeric($price) ? $price : null;
                    }

                    // Tax Rate
                        $taxRate = trim($product['tax_rate']);
                        if ($taxRate != null) {
                            $taxRate = str_replace(',', '', $taxRate);
                            $taxRate = str_replace('-', '', $taxRate);
                            $taxRate = is_numeric($taxRate) ? $taxRate : null;
                        }

                    // Tax Label
                        $taxLabel = trim($product['tax_label']);

                    // Internal Code
                        $internalCode = trim($product['internal_code']);

                        $product = new Product();
                        $product->name = $productName;
                        $product->category_id = $category->id;
                        $product->company_id = $company->id;
                        $product->tax_rate = $taxRate;
                        $product->price = $price;
                        $product->tax_label = $taxLabel;
                        $product->internal_code = $internalCode;
                        $product->product_type = $productType;
                        $product->save();


                }
            }
        });
    }
}
