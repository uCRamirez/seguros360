<?php

namespace App\Imports;

use App\Classes\Common;
use App\Models\Category;
use App\Models\Product;
use App\Models\Campaign;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;
use Illuminate\Support\Str;

class ProductImport implements ToArray, WithHeadingRow
{
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function array(array $products)
    {
        // \Log::info('ProductImport', [$products]);
        // \Log::info('Importando archivo CSV:', ['filename' => $this->filename]);
        DB::transaction(function () use ($products) {
            $company = company();
            $user = user();
            $contador = 1;
            foreach ($products as $product) {
                $contador++;
                if (
                    !array_key_exists('name', $product) || !array_key_exists('coverage', $product) || !array_key_exists('category', $product) || !array_key_exists('campaign', $product) || //!array_key_exists('product_type', $product) ||
                    !array_key_exists('price', $product) || !array_key_exists('tax_rate', $product) || !array_key_exists('internal_code', $product)
                ) {
                    throw new ApiException('Field missing from header.');
                }

                // Name
                $productName = trim($product['name']);

                if ($productName != '') {
                    // $productCount = Product::where('name', $productName)->count();
                    // if ($productCount > 0) {
                    //     throw new ApiException('Product ' . $productName . ' Already Exists');
                    // }

                    // Campos obligatorios
                    // Campaign
                    $campaignName = trim($product['campaign']);
                    $campaign = Campaign::where('name', $campaignName)->first();
                    if (!$campaign) {
                        throw new ApiException('Invalid Campaign. Line:' . $contador);
                    }
                    // Internal Code
                    $internalCode = trim($product['internal_code']);
                    if ($internalCode === '') {
                        throw new ApiException('Internal Code Required. Line:' . $contador);
                    }
                    // Price
                    $price = $product['price'] && $product['price'] != '' ? trim($product['price']) : null;
                    if ($price != null) {
                        $price = str_replace(',', '', $price);
                        $price = str_replace('-', '', $price);
                        $price = is_numeric($price) ? $price : null;
                    }else{
                        throw new ApiException('Price Required. Line:' . $contador);
                    }


                    $productCoverage = trim($product['coverage']);

                    // Category
                    $categoryName = trim($product['category']);
                    $category = Category::where('name', $categoryName)->first();
                    if (!$category) {
                        // $category = new Category();
                        // $category->name = 'electronics';
                        // $category->save();
                        $category = Category::first();
                    }

                    // Product Type
                    // $productType = trim($product['product_type']);

                    

                    // Tax Rate
                        $taxRate = trim($product['tax_rate']);
                        if ($taxRate != null) {
                            $taxRate = str_replace(',', '', $taxRate);
                            $taxRate = str_replace('-', '', $taxRate);
                            $taxRate = is_numeric($taxRate) ? $taxRate : null;
                        }

                    // Tax Label
                        $taxLabel = trim($product['tax_label']);

                        $product = new Product();
                        $product->name = $productName;
                        $product->coverage = $productCoverage;
                        $product->category_id = $category->id;
                        $product->company_id = $company->id;
                        $product->tax_rate = $taxRate;
                        $product->price = $price;
                        $product->tax_label = $taxLabel;
                        $product->internal_code = $internalCode;
                        // $product->product_type = $productType;
                        $product->campaign_id = $campaign->id;
                        $product->nombreBase = $this->filename;
                        $product->save();


                }else{
                    throw new ApiException('Name Required. Line:' . $contador);
                }
            }
        });
    }
}
