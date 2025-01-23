<?php

use App\Http\Requests\NYTBestSellersRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class NYTBestSellersRequestTest extends TestCase
{
    // Test to ensure the validation rules are correctly defined
    public function test_validation_rules()
    {
        $request = new NYTBestSellersRequest();

        // Get the validation rules from the request
        $rules = $request->rules();

        // Assert that the rules contain the expected fields
        $this->assertArrayHasKey('author', $rules);
        $this->assertArrayHasKey('isbn', $rules);
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('offset', $rules);
    }

    // Test to ensure valid data passes validation
    public function test_valid_data_passes_validation()
    {
        $data = [
            'author' => 'John Doe',
            'isbn' => '1234567890',
            'title' => 'Sample Book',
            'offset' => 20,
        ];

        $request = new NYTBestSellersRequest();
        // Create a validator instance with the data and rules
        $validator = Validator::make($data, $request->rules());

        // Assert that the validation passes
        $this->assertTrue($validator->passes());
    }

    // Test to ensure invalid data fails validation
    public function test_invalid_data_fails_validation()
    {
        $data = [
            'author' => 123,
            'isbn' => 'invalid_isbn',
            'title' => 456,
            'offset' => -1,
        ];

        $request = new NYTBestSellersRequest();
        // Create a validator instance with the data and rules
        $validator = Validator::make($data, $request->rules());

        // Assert that the validation fails
        $this->assertFalse($validator->passes());
    }

    // Test to ensure the offset field is validated correctly
    public function test_offset_validation()
    {
        $data = [
            'offset' => 'invalid',
        ];

        $request = new NYTBestSellersRequest();
        // you get what is going on here already.
        $validator = Validator::make($data, $request->rules());

        // make sure it fails
        $this->assertFalse($validator->passes());
    }
}
