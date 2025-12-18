<?php

namespace App\Services\WooCommerce;

use Automattic\WooCommerce\Client;
use Automattic\WooCommerce\HttpClient\HttpClientException;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WooCommerceClient
{
    protected ?Client $client = null;

    protected array $config;

    public function __construct()
    {
        $config = config('woocommerce');
        $this->config = is_array($config) ? $config : [];
        $this->bootClient();
    }

    protected function bootClient(): void
    {
        if (
            empty($this->config['store_url']) ||
            empty($this->config['consumer_key']) ||
            empty($this->config['consumer_secret'])
        ) {
            return;
        }

        $this->client = new Client(
            $this->config['store_url'],
            $this->config['consumer_key'],
            $this->config['consumer_secret'],
            [
                'version' => $this->config['api_version'] ?? 'wc/v3',
                'verify_ssl' => (bool) ($this->config['verify_ssl'] ?? true),
                'timeout' => (int) ($this->config['timeout'] ?? 30),
                'query_string_auth' => true,
            ]
        );
    }

    public function isConfigured(): bool
    {
        return $this->client !== null;
    }

    public function getConnectionStatus(): array
    {
        if (! $this->isConfigured()) {
            return [
                'connected' => false,
                'message' => 'WooCommerce credentials are not configured.',
            ];
        }

        try {
            // طلب بسيط للتأكد من الاتصال (قائمة المنتجات صفحة واحدة)
            $this->get('products', ['per_page' => 1]);

            return [
                'connected' => true,
                'message' => 'Connected to WooCommerce successfully.',
            ];
        } catch (Exception $e) {
            return [
                'connected' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * تنفيذ طلب GET.
     */
    public function get(string $endpoint, array $params = []): mixed
    {
        return $this->request('get', $endpoint, $params);
    }

    /**
     * تنفيذ طلب POST.
     */
    public function post(string $endpoint, array $data = []): mixed
    {
        return $this->request('post', $endpoint, $data);
    }

    /**
     * تنفيذ طلب PUT.
     */
    public function put(string $endpoint, array $data = []): mixed
    {
        return $this->request('put', $endpoint, $data);
    }

    /**
     * تنفيذ طلب DELETE.
     */
    public function delete(string $endpoint, array $params = []): mixed
    {
        return $this->request('delete', $endpoint, $params);
    }

    /**
     * تنفيذ طلب مع معالجة الأخطاء والتسجيل.
     */
    protected function request(string $method, string $endpoint, array $data = []): mixed
    {
        if (! $this->isConfigured()) {
            throw new Exception('WooCommerce client not configured (check .env).');
        }

        try {
            $this->logRequest($method, $endpoint, $data);

            $response = match ($method) {
                'get' => $this->client->get($endpoint, $data),
                'post' => $this->client->post($endpoint, $data),
                'put' => $this->client->put($endpoint, $data),
                'delete' => $this->client->delete($endpoint, $data),
                default => throw new Exception("Unsupported method [{$method}]"),
            };

            $this->logResponse($method, $endpoint, $response);

            return $response;
        } catch (HttpClientException $e) {
            $body = $e->getResponse()->getBody();
            $decoded = is_string($body) ? json_decode($body, true) : null;
            $message = $decoded['message'] ?? $e->getMessage();

            $this->logError("WooCommerce API error [{$method} {$endpoint}]", $e);

            throw new Exception($message, $e->getCode(), $e);
        } catch (Exception $e) {
            $this->logError("WooCommerce request error [{$method} {$endpoint}]", $e);

            throw $e;
        }
    }

    /**
     * جلب كل الصفحات (pagination) لنقطة نهاية معينة.
     */
    public function getAllPaginated(string $endpoint, array $params = [], int $perPage = 50): array
    {
        $all = [];
        $page = 1;

        do {
            $params['page'] = $page;
            $params['per_page'] = $perPage;

            $items = $this->get($endpoint, $params);

            if (empty($items)) {
                break;
            }

            $all = array_merge($all, $items);
            $page++;
        } while (count($items) === $perPage);

        return $all;
    }

    /**
     * GET مع Cache اختياري.
     */
    public function getCached(string $endpoint, array $params = [], ?int $ttl = null): mixed
    {
        if (! ($this->config['cache']['enabled'] ?? false)) {
            return $this->get($endpoint, $params);
        }

        $key = 'woo_' . md5($endpoint . serialize($params));
        $ttl = $ttl ?? (int) ($this->config['cache']['ttl'] ?? 300);

        return Cache::remember($key, $ttl, function () use ($endpoint, $params) {
            return $this->get($endpoint, $params);
        });
    }

    protected function logRequest(string $method, string $endpoint, array $data): void
    {
        if (! ($this->config['logging']['enabled'] ?? false)) {
            return;
        }

        Log::channel($this->config['logging']['channel'] ?? 'stack')->debug('WooCommerce request', [
            'method' => strtoupper($method),
            'endpoint' => $endpoint,
            'data' => $this->sanitize($data),
        ]);
    }

    protected function logResponse(string $method, string $endpoint, mixed $response): void
    {
        if (! ($this->config['logging']['enabled'] ?? false)) {
            return;
        }

        Log::channel($this->config['logging']['channel'] ?? 'stack')->debug('WooCommerce response', [
            'method' => strtoupper($method),
            'endpoint' => $endpoint,
            'items' => is_array($response) ? count($response) : 1,
        ]);
    }

    protected function logError(string $message, Exception $e): void
    {
        if (! ($this->config['logging']['enabled'] ?? false)) {
            return;
        }

        Log::channel($this->config['logging']['channel'] ?? 'stack')->error($message, [
            'error' => $e->getMessage(),
        ]);
    }

    protected function sanitize(array $data): array
    {
        foreach (['password', 'consumer_key', 'consumer_secret'] as $key) {
            if (isset($data[$key])) {
                $data[$key] = '***';
            }
        }

        return $data;
    }
}


