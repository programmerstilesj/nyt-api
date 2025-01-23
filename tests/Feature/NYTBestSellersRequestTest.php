<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NYTBestSellersRequestTest extends TestCase
{
    var $uri = '/api/v1/nyt/best-sellers';

    protected function setUp(): void
    {
        parent::setUp();
        // Faking HTTP requests because we don't want to bother the real NYT API during tests
        Http::fake();
    }

    public function test_valid_author()
    {
        // Testing with a valid author name, expecting a 200 OK response
        $response = $this->json('GET', $this->uri, ['author' => 'John Doe']);
        $response->assertStatus(200);
    }

    public function test_invalid_isbn()
    {
        // Testing with an invalid ISBN, expecting a 422 Unprocessable Entity response
        $response = $this->json('GET', $this->uri, ['isbn' => '123']);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('isbn');
    }

    public function test_valid_isbn()
    {
        // Testing with a valid ISBN, expecting a 200 OK response
        $response = $this->json('GET', $this->uri, ['isbn' => '9780446579933']);
        $response->assertStatus(200);
    }

    public function test_valid_multiple_isbns()
    {
        // Testing with multiple valid ISBNs, expecting a 200 OK response
        $response = $this->json('GET', $this->uri, ['isbn' => '9780446579933;0061374229']);
        $response->assertStatus(200);
    }

    public function test_invalid_offset()
    {
        // Testing with an invalid offset, expecting a 422 Unprocessable Entity response
        $response = $this->json('GET', $this->uri, ['offset' => 25]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors('offset');
    }

    public function test_valid_offset()
    {
        // Testing with a valid offset, expecting a 200 OK response
        $response = $this->json('GET', $this->uri, ['offset' => 20]);
        $response->assertStatus(200);
    }

    public function test_valid_title()
    {
        // Testing with a valid book title, expecting a 200 OK response
        $response = $this->json('GET', $this->uri, ['title' => 'Sample Book']);
        $response->assertStatus(200);
    }
}
