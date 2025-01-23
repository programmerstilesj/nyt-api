<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NYTControllerTest extends TestCase
{
    var $uri = '/api/v1/nyt/best-sellers';

    protected function setUp(): void
    {
        parent::setUp();
        // Clear the cache because we don't want any stale data hanging around
        Cache::flush();
    }

    public function test_get_best_sellers()
    {
        // Fake the HTTP response from the NYT API
        Http::fake([
            'api.nytimes.com/*' => Http::response(['results' => []], 200),
        ]);

        // Make the GET request to our endpoint
        $response = $this->getJson($this->uri);

        // Assert that the response status is 200 and the results are empty
        $response->assertStatus(200)
            ->assertJson(['results' => []]);
    }

    public function test_get_best_sellers_with_parameters()
    {
        // Fake the HTTP response from the NYT API
        Http::fake([
            'api.nytimes.com/*' => Http::response(['results' => []], 200),
        ]);

        // Make the GET request with query parameters
        $response = $this->getJson($this->uri . '?author=John+Doe&title=Sample+Book');

        // Assert that the response status is 200 and the results are empty
        $response->assertStatus(200)
            ->assertJson(['results' => []]);
    }

    public function test_get_best_sellers_handles_errors()
    {
        // Fake the HTTP response from the NYT API with an error status
        Http::fake([
            'api.nytimes.com/*' => Http::response(null, 500),
        ]);

        // Make the GET request to our endpoint
        $response = $this->getJson($this->uri);

        // Assert that the response status is 500 and the error message is returned
        $response->assertStatus(500)
            ->assertJson(['error' => 'Unable to fetch data from NYT API']);
    }

    public function test_get_best_sellers_with_invalid_parameters()
    {
        // Fake the HTTP response from the NYT API
        Http::fake([
            'api.nytimes.com/*' => Http::response(['results' => []], 200),
        ]);

        // Make the GET request with invalid query parameters
        $response = $this->getJson($this->uri . '?offset=invalid');

        // Assert that the response status is 422 because the offset is invalid
        $response->assertStatus(422); // Unprocessable Entity
    }

    public function test_get_best_sellers_with_negative_parameters()
    {
        // Fake the HTTP response from the NYT API
        Http::fake([
            'api.nytimes.com/*' => Http::response(['results' => []], 200),
        ]);

        // Make the GET request with negative query parameters
        $response = $this->getJson($this->uri . '?offset=-1');

        // Assert that the response status is 422 because the offset is negative
        $response->assertStatus(422); // Unprocessable Entity
    }

    public function test_get_best_sellers_with_missing_api_key()
    {
        // Set the API key to null because we want to see what happens without it
        config(['services.nyt.api_key' => null]);

        // Fake the HTTP response from the NYT API with an error status
        Http::fake([
            'https://api.nytimes.com/svc/books/v3/lists/best-sellers/history.json' => Http::response(['error' => 'Unable to fetch data from NYT API'], 500),
        ]);

        // Make the GET request to our endpoint
        $response = $this->getJson($this->uri);

        // Assert that the response status is 500 and the error message is returned
        $response->assertStatus(500)
            ->assertJson(['error' => 'Unable to fetch data from NYT API']);
    }

    public function test_get_best_sellers_with_different_http_status_codes()
    {
        // Fake the HTTP response from the NYT API with a 404 status
        Http::fake([
            'api.nytimes.com/*' => Http::response(null, 404),
        ]);

        // Make the GET request to our endpoint
        $response = $this->getJson($this->uri);

        // Assert that the response status is 404 and the error message is returned
        $response->assertStatus(404)
            ->assertJson(['error' => 'Unable to fetch data from NYT API']);
    }

    public function test_get_best_sellers_with_large_data_set()
    {
        // Create a large data set to test the response
        $largeDataSet = array_fill(0, 1000, ['title' => 'Sample Book']);
        // Fake the HTTP response from the NYT API
        Http::fake([
            'api.nytimes.com/*' => Http::response(['results' => $largeDataSet], 200),
        ]);

        // Make the GET request to our endpoint
        $response = $this->getJson($this->uri);

        // Assert that the response status is 200 and the results match the large data set
        $response->assertStatus(200)
            ->assertJson(['results' => $largeDataSet]);
    }

    public function test_get_best_sellers_caching()
    {
        // Fake the HTTP response from the NYT API
        Http::fake([
            'api.nytimes.com/*' => Http::response(['results' => []], 200),
        ]);

        // Mock the Cache::remember method to ensure it's called
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn([
                'status' => 200,
                'body' => ['results' => []]
            ]);

        // Make the GET request to our endpoint
        $response = $this->getJson($this->uri);

        // Assert that the response status is 200 and the results are empty
        $response->assertStatus(200)
            ->assertJson(['results' => []]);
    }

    public function test_get_best_sellers_with_empty_parameters()
    {
        // Fake the HTTP response from the NYT API
        Http::fake([
            'api.nytimes.com/*' => Http::response(['results' => []], 200),
        ]);

        // Make the GET request with empty query parameters
        $response = $this->getJson($this->uri . '?author=&title=');

        // Assert that the response status is 200 and the results are empty
        $response->assertStatus(200)
            ->assertJson(['results' => []]);
    }

    public function test_get_best_sellers_with_invalid_data_types()
    {
        // Fake the HTTP response from the NYT API
        Http::fake([
            'api.nytimes.com/*' => Http::response(['results' => []], 200),
        ]);

        // Make the GET request with invalid data types
        $response = $this->getJson($this->uri . '?offset=invalid');

        // Assert that the response status is 422 because the offset is invalid
        $response->assertStatus(422); // Unprocessable Entity
    }

    public function test_get_best_sellers_with_boundary_values()
    {
        // Fake the HTTP response from the NYT API
        Http::fake([
            'api.nytimes.com/*' => Http::response(['results' => []], 200),
        ]);

        // Make the GET request with boundary values
        $response = $this->getJson($this->uri . '?offset=0');

        // Assert that the response status is 200 and the results are empty
        $response->assertStatus(200)
            ->assertJson(['results' => []]);
    }

    public function test_get_best_sellers_with_rate_limiting()
    {
        // Fake the HTTP response from the NYT API with a 429 status
        Http::fake([
            'api.nytimes.com/*' => Http::response(null, 429), // Too Many Requests
        ]);

        // Make the GET request to our endpoint
        $response = $this->getJson($this->uri);

        // Assert that the response status is 429 and the error message is returned
        $response->assertStatus(429)
            ->assertJson(['error' => 'Unable to fetch data from NYT API']);
    }

    public function test_get_best_sellers_with_network_issues()
    {
        // Fake the HTTP response from the NYT API with a 504 status
        Http::fake([
            'api.nytimes.com/*' => Http::response(null, 504), // Gateway Timeout
        ]);

        // Make the GET request to our endpoint
        $response = $this->getJson($this->uri);

        // Assert that the response status is 504 and the error message is returned
        $response->assertStatus(504)
            ->assertJson(['error' => 'Unable to fetch data from NYT API']);
    }
}
