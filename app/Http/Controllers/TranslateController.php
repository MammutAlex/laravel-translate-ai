<?php

namespace App\Http\Controllers;

use App\Http\Requests\TranslateRequest;
use App\Models\Translate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TranslateController extends Controller
{
    public function __invoke(TranslateRequest $request)
{
    $text = $this->getTranslation($request);

    if (!$text) {
        $text = $this->fetchTranslation($request);
        $this->storeTranslation($request, $text);
    }

    return ['text' => $text];
}

private function getTranslation($request)
{
    return Translate::where('text', $request->text)
        ->where('lang', $request->lang)
        ->first()?->translated_text;
}

private function fetchTranslation($request)
{
    return Http::withToken(config('services.chatgpt.token'))
        ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a translate.'],
                ['role' => 'user', 'content' => "Translate this text to {$request->lang} language. If the word cannot be translated, transliterate it.\n\n{$request->text}"],
            ],
        ])->json('choices.0.message.content');
}

private function storeTranslation($request, $text)
{
    Translate::forceCreate([
        'text' => $request->text,
        'translated_text' => $text,
        'lang' => $request->lang,
    ]);
}
}
