<?php

namespace Tests\Feature;

use App\Models\Translate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TranslateTest extends TestCase
{
    public function test_translate(): void
    {
        $this->postJson('/api/translate', [
            'text' => 'Привіт',
            'lang' => 'en',
        ])->assertSuccessful()
            ->assertJsonFragment([
                'text' => 'Hello',
            ]);
        $this->assertDatabaseHas('translates', [
            'text' => 'Привіт',
            'translated_text' => 'Hello',
            'lang' => 'en',
        ]);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.openai.com/v1/chat/completions';
        });
    }

    public function test_get_text_form_database_if_exist()
    {
        $translate = Translate::factory()->create();
        $this->postJson('/api/translate', [
            'text' => $translate->text,
            'lang' => $translate->lang,
        ])->assertSuccessful()
            ->assertJsonFragment([
                'text' => $translate->translated_text,
            ]);

        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://api.openai.com/v1/chat/completions';
        });
    }

    public function test_translate_validation_error(): void
    {
        $this->postJson('/api/translate')
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'text',
                'lang',
            ]);
    }
}
