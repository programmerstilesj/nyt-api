<?php

use App\Http\Controllers\V1\NYTController;
use App\Http\Requests\NYTBestSellersRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NYTControllerUnitTest extends TestCase
{
    // Test to ensure the response is cached correctly
    public function test_get_best_sellers_caches_response()
    {
        // Create a new request with the author parameter
        $request = new NYTBestSellersRequest();
        $request->replace(['author' => 'John Doe']);

        // Fake the HTTP response from the NYT API
        Http::fake([
            'api.nytimes.com/*' => Http::response(['results' => []], 200),
        ]);

        // Instantiate the controller and call the getBestSellers method
        $controller = new NYTController();
        $response = $controller->getBestSellers($request);

        // Assert that the response is cached
        $this->assertTrue(Cache::has('nyt_best_sellers_' . md5(serialize(['author' => 'John Doe', 'api-key' => config('services.nyt.api_key')]))));
    }

    // Test to ensure the controller handles HTTP errors correctly
    public function test_get_best_sellers_handles_http_errors()
    {
        // Create a new request with the author parameter
        $request = new NYTBestSellersRequest();
        $request->replace(['author' => 'John Doe']);

        // Fake the HTTP response from the NYT API with an error status
        Http::fake([
            'api.nytimes.com/*' => Http::response(null, 500),
        ]);

        // Instantiate the controller and call the getBestSellers method
        $controller = new NYTController();
        $response = $controller->getBestSellers($request);

        // Assert that the response status is 500 and the error message is returned
        $this->assertEquals(500, $response->status());
        $this->assertEquals(['error' => 'Unable to fetch data from NYT API'], $response->getData(true));
    }
}

