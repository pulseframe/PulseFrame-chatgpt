<?php

namespace PulseFrame\Support\Facades;

use PulseFrame\Facades\Env;

/**
 * Class ChatGPT
 * 
 * @category facades
 * @name ChatGPT
 * 
 * This class is responsible for interacting with the OpenAI GPT chat completion API. 
 * It provides a static method to send prompts to the GPT model and receive completions. 
 * The class utilizes the Env facade to load necessary API keys from configuration files.
 */
class ChatGPT
{
  private static $response;
  public static $message;

  /**
   * Request a chat completion from the OpenAI GPT API.
   *
   * @category facades
   * 
   * @param string $prompt The prompt to send to the GPT model.
   * @param string $url The API endpoint URL (optional, defaults to OpenAI's chat completions endpoint).
   * @return array The response from the GPT model as an associative array.
   *
   * This function sends a prompt to the OpenAI GPT API using a HTTP POST request. It first ensures that 
   * the environment settings are loaded, retrieves the API key from the configuration, constructs the 
   * appropriate request payload, and sends it to the API endpoint using cURL. The response from the API 
   * is parsed and stored in the static properties $response and $message.
   * 
   * Example usage:
   * $response = ChatGPT::requestCompletion('Hello, how are you?');
   * echo $response::$message;
   */
  public static function requestCompletion($prompt)
  {
    $url = Env::get('chatgpt.url') ?? "https://api.openai.com/v1/chat/completions";

    $apiKey = Env::get('chatgpt.key');
    $postData = json_encode([
      'model' => Env::get('chatgpt.model') ?? 'gpt-4o-2024-05-13',
      'messages' => [['role' => 'user', 'content' => $prompt]],
      'max_tokens' => 100
    ]);

    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
      ],
      CURLOPT_POSTFIELDS => $postData
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    self::$response = json_decode($response, true);
    self::$message = self::$response['choices'][0]['message']['content'];

    return self::$response;
  }
}
