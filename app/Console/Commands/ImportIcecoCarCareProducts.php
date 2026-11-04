<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ImportIcecoCarCareProducts extends Command
{
    protected $signature = 'ventor:import-iceco-car-care {--dry-run : Fetch products and show the import plan without saving anything}';

    protected $description = 'Import ICECO car-care products with doubled retail prices';

    private const API = 'https://iceco.ge/wp-json/wc/store/v1';

    public function handle(): int
    {
        $this->info('ICECO ავტომობილის მოვლის კატეგორიის ჩამოტვირთვა...');

        try {
            $categories = Http::acceptJson()->timeout(30)->retry(2, 500)
                ->get(self::API.'/products/categories', ['per_page' => 100])
                ->throw()
                ->json();

            $sourceCategory = collect($categories)->first(
                fn (array $category): bool => ($category['slug'] ?? null) === 'car-care-products'
                    || str_contains(mb_strtolower((string) ($category['name'] ?? '')), 'ავტომობილის მოვლის')
            );

            if (! $sourceCategory) {
                $this->error('ICECO-ზე ავტომობილის მოვლის კატეგორია ვერ მოიძებნა.');

                return self::FAILURE;
            }

            $products = collect();
            $page = 1;

            do {
                $response = Http::acceptJson()->timeout(45)->retry(2, 500)
                    ->get(self::API.'/products', [
                        'category' => $sourceCategory['id'],
                        'per_page' => 100,
                        'page' => $page,
                    ])
                    ->throw();

                $batch = collect($response->json());
                $products = $products->concat($batch);
                $totalPages = (int) $response->header('X-WP-TotalPages', $page);
                $page++;
            } while ($page <= $totalPages && $page <= 100);

            $products = $products->unique('id')->values();

            if ($products->isEmpty()) {
                $this->warn('ამ კატეგორიაში პროდუქცია ვერ მოიძებნა.');

                return self::SUCCESS;
            }

            $this->info("ნაპოვნია {$products->count()} უნიკალური პროდუქტი.");
            $this->table(
                ['პროდუქტი', 'წყაროს ფასი', 'VENTOR ფასი'],
                $products->map(function (array $product): array {
                    $sourcePrice = $this->priceFromStoreApi($product);
                    return [
                        $product['name'] ?? 'უსახელო პროდუქტი',
                        number_format($sourcePrice, 2).' ₾',
                        number_format($sourcePrice * 2, 2).' ₾',
                    ];
                })->all()
            );

            if ($this->option('dry-run')) {
                $this->warn('საცდელი რეჟიმია — მონაცემები არ შენახულა და ფოტოები არ ჩამოტვირთულა.');

                return self::SUCCESS;
            }

            $category = Category::firstOrCreate(
                ['slug' => 'car-care'],
                [
                    'name' => 'ავტომობილის მოვლა',
                    'description' => 'ავტომობილის მოვლისა და დეტეილინგის საშუალებები',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            );

            $created = 0;
            $updated = 0;
            $imageFailures = 0;

            foreach ($products as $sourceProduct) {
                $sourceSlug = Str::slug((string) ($sourceProduct['slug'] ?? $sourceProduct['name'] ?? 'product'));
                $slug = 'iceco-'.$sourceSlug;
                $sourcePrice = $this->priceFromStoreApi($sourceProduct);
                $imagePath = $this->downloadProductImage($sourceProduct, $slug);

                if (! empty($sourceProduct['images']) && ! $imagePath) {
                    $imageFailures++;
                }

                $product = Product::query()->where('slug', $slug)->first();
                $attributes = [
                    'category_id' => $category->id,
                    'name' => trim(strip_tags((string) ($sourceProduct['name'] ?? 'ICECO პროდუქტი'))),
                    'short_description' => Str::limit($this->plainText($sourceProduct['short_description'] ?? ''), 490, ''),
                    'description' => $this->plainText($sourceProduct['description'] ?? ''),
                    'price' => number_format($sourcePrice * 2, 2, '.', ''),
                    'is_active' => true,
                ];

                if ($imagePath) {
                    $attributes['image_path'] = $imagePath;
                }

                if ($product) {
                    $product->update($attributes);
                    $updated++;
                } else {
                    Product::create($attributes + [
                        'slug' => $slug,
                        'stock' => 0,
                        'is_featured' => false,
                    ]);
                    $created++;
                }
            }

            $this->newLine();
            $this->info("დასრულდა: დამატებულია {$created}, განახლებულია {$updated}.");
            $this->line("ფოტოს ჩამოტვირთვის წარუმატებელი მცდელობები: {$imageFailures}.");
            $this->warn('მარაგი დატოვებულია 0-ზე — წყაროს საიტზე არსებული მარაგი VENTOR-ის რეალურ მარაგად არ გადმოტანილა.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('იმპორტი შეწყდა: '.$exception->getMessage());

            return self::FAILURE;
        }
    }

    private function priceFromStoreApi(array $product): float
    {
        $prices = $product['prices'] ?? [];
        $minorUnit = (int) ($prices['currency_minor_unit'] ?? 2);
        $rawPrice = $prices['price'] ?? null;

        if ($rawPrice === null || ! is_numeric($rawPrice)) {
            throw new RuntimeException('პროდუქტს ფასი არ აქვს: '.($product['name'] ?? 'unknown'));
        }

        return ((float) $rawPrice) / (10 ** $minorUnit);
    }

    private function plainText(mixed $value): string
    {
        $decoded = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(preg_replace('/\s+/u', ' ', strip_tags($decoded)) ?? '');
    }

    private function downloadProductImage(array $product, string $slug): ?string
    {
        $imageUrl = $product['images'][0]['src'] ?? null;

        if (! is_string($imageUrl) || ! filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($imageUrl);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));

        // Only download images hosted by ICECO itself (including its subdomains).
        if (! in_array($scheme, ['https', 'http'], true)
            || ($host !== 'iceco.ge' && ! str_ends_with($host, '.iceco.ge'))) {
            return null;
        }

        try {
            $response = Http::timeout(30)->retry(2, 500)->withHeaders([
                'User-Agent' => 'VENTOR product catalog importer/1.0',
                'Accept' => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
            ])->get($imageUrl)->throw();

            $contentType = strtolower(trim(explode(';', (string) $response->header('Content-Type'))[0]));
            $extensions = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                'image/avif' => 'avif',
            ];

            if (! isset($extensions[$contentType])) {
                $this->warn('ფოტო გამოტოვებულია (არასწორი Content-Type): '.($product['name'] ?? $slug));

                return null;
            }

            $path = 'products/iceco/'.$slug.'.'.$extensions[$contentType];

            if (Storage::disk('public')->exists($path)) {
                return $path;
            }

            if (! Storage::disk('public')->put($path, $response->body())) {
                $this->warn('ფოტოს შენახვა ვერ მოხერხდა: '.($product['name'] ?? $slug));

                return null;
            }

            return $path;
        } catch (Throwable $exception) {
            report($exception);
            $this->warn('ფოტოს ჩამოტვირთვა ვერ მოხერხდა: '.($product['name'] ?? $slug).' — '.$exception->getMessage());

            return null;
        }
    }
}
}
