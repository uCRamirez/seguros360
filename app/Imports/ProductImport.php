<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\Campaign;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToArray;

class ProductImport implements ToArray, WithHeadingRow
{
    protected string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function array(array $products)
    {
        DB::transaction(function () use ($products) {
            $company = company();
            $contador = 1;

            foreach ($products as $row) {
                $contador++;

                // Validación de headers obligatorios (según lo que usas sí o sí)
                if (
                    !array_key_exists('name', $row) ||
                    !array_key_exists('coverage', $row) ||
                    !array_key_exists('category', $row) ||
                    !array_key_exists('campaign', $row) ||
                    !array_key_exists('price', $row) ||
                    !array_key_exists('tax_rate', $row) ||
                    !array_key_exists('internal_code', $row) ||
                    !array_key_exists('enter_price', $row)
                ) {
                    throw new ApiException('Field missing from header.');
                }

                // Name
                $productName = trim((string) ($row['name'] ?? ''));
                if ($productName === '') {
                    throw new ApiException('Name Required. Line:' . $contador);
                }

                // Campaign
                $campaignName = trim((string) ($row['campaign'] ?? ''));
                $campaign = Campaign::where('name', $campaignName)->first();
                if (!$campaign) {
                    throw new ApiException('Invalid Campaign. Line:' . $contador);
                }

                // Internal Code
                $internalCode = trim((string) ($row['internal_code'] ?? ''));
                if ($internalCode === '') {
                    throw new ApiException('Internal Code Required. Line:' . $contador);
                }

                // Price
                $price = ($row['price'] ?? null);
                $price = $price !== null && trim((string)$price) !== '' ? trim((string)$price) : null;
                if ($price === null) {
                    throw new ApiException('Price Required. Line:' . $contador);
                }
                $price = str_replace([',', '-'], '', $price);
                $price = is_numeric($price) ? $price : null;
                if ($price === null) {
                    throw new ApiException('Invalid Price. Line:' . $contador);
                }

                // Coverage
                $productCoverage = trim((string) ($row['coverage'] ?? ''));

                // Category
                $categoryName = trim((string) ($row['category'] ?? ''));
                $category = Category::where('name', $categoryName)->first();
                if (!$category) {
                    // Mantengo tu comportamiento: si no existe, usa la primera
                    $category = Category::first();
                }
                if (!$category) {
                    throw new ApiException('No Category available in system. Line:' . $contador);
                }

                // Tax Rate
                $taxRate = trim((string) ($row['tax_rate'] ?? ''));
                if ($taxRate !== '') {
                    $taxRate = str_replace([',', '-'], '', $taxRate);
                    $taxRate = is_numeric($taxRate) ? $taxRate : null;
                } else {
                    $taxRate = null;
                }

                $taxLabel = array_key_exists('tax_label', $row) ? trim((string)$row['tax_label']) : null;

                $enterPriceRaw = array_key_exists('enter_price', $row) ? trim((string)$row['enter_price']) : '';
                $enterPrice = ($enterPriceRaw === '1' || $enterPriceRaw === 1) ? 1 : 0;

                $product = Product::where('company_id', $company->id)
                    ->where('campaign_id', $campaign->id)
                    ->where('internal_code', $internalCode)
                    ->where('name', $productName)
                    ->first();

                if (!$product) {
                    $product = Product::where('company_id', $company->id)
                        ->where('campaign_id', $campaign->id)
                        ->where('name', $productName)
                        ->first();
                }

                if (!$product) {
                    $product = new Product();
                }

                $product->name = $productName;
                $product->coverage = $productCoverage;
                $product->category_id = $category->id;
                $product->company_id = $company->id;
                $product->tax_rate = $taxRate;
                $product->price = $price;
                $product->tax_label = $taxLabel;
                $product->internal_code = $internalCode;
                $product->campaign_id = $campaign->id;
                $product->nombreBase = $this->filename;
                $product->digitar_precio = $enterPrice;
                $product->status = true;
                $product->save();
            }
        });
    }
}
