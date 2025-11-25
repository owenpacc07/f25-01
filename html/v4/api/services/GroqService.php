<?php

/**
 * Groq AI Service Implementation
 *
 * Integrates with Groq's cloud API for fast, reliable AI responses
 * Uses OpenAI-compatible API format
 *
 * Groq API: https://api.groq.com/openai/v1/chat/completions
 * Models: mixtral-8x7b-32768, llama2-70b, gemma-7b-it
 */

class GroqService extends BaseAIService {

    /**
     * Constructor
     *
     * @param array $env Environment variables from .env
     */
    public function __construct($env = []) {
        $config = [
            'api_key' => $env['GROQ_API_KEY'] ?? null,
            'api_url' => 'https://api.groq.com/openai/v1/chat/completions',
            'model' => $env['GROQ_MODEL'] ?? 'mixtral-8x7b-32768',
            'max_tokens' => $env['GROQ_MAX_TOKENS'] ?? 500,
            'temperature' => $env['GROQ_TEMPERATURE'] ?? 0.7,
            'log_file' => $env['LOG_FILE'] ?? '/var/www/projects/f25-01/html/v3b/api/logs/chatbot.log',
            'debug_mode' => $env['DEBUG_MODE'] ?? false
        ];

        parent::__construct($config);
    }

    /**
     * Get service name
     *
     * @return string Service identifier
     */
    public function getServiceName() {
        return 'groq';
    }

    /**
     * Send message to Groq API
     *
     * @param string $userMessage User's question
     * @param string $systemPrompt System prompt with context
     * @param array $context Request context (for logging)
     *
     * @return array Response in standard format
     */
    public function sendMessage($userMessage, $systemPrompt, $context = []) {
        try {
            // Validate inputs
            if (empty($userMessage)) {
                throw new Exception("User message cannot be empty");
            }

            // Log request if debug enabled
            $this->logRequest($this->getServiceName(), $userMessage, $context);

            // Construct request
            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage]
            ];

            $requestPayload = [
                'model' => $this->model,
                'messages' => $messages,
                'temperature' => floatval($this->temperature),
                'max_tokens' => intval($this->maxTokens)
            ];

            // Prepare headers
            $headers = [
                'Authorization: Bearer ' . $this->getApiKey(),
                'Content-Type: application/json'
            ];

            // Make request
            $startTime = microtime(true);
            $curlResult = $this->makeCurlRequest(
                $this->apiUrl,
                $headers,
                json_encode($requestPayload),
                30 // 30 second timeout
            );
            $responseTime = (microtime(true) - $startTime) * 1000;

            // Handle curl errors
            if (!$curlResult['success'] && !empty($curlResult['error'])) {
                throw new Exception("Curl error: " . $curlResult['error']);
            }

            // Parse response
            $responseData = json_decode($curlResult['data'], true);

            // Handle API errors
            if ($curlResult['http_code'] !== 200) {
                $errorMessage = $responseData['error']['message'] ?? 'Unknown API error';
                $this->logError($this->getServiceName(), $errorMessage, $curlResult['http_code']);

                // Provide user-friendly error messages
                if ($curlResult['http_code'] === 401) {
                    throw new Exception("Groq API authentication failed. Check API key.");
                } elseif ($curlResult['http_code'] === 429) {
                    throw new Exception("Groq API rate limit exceeded. Please try again later.");
                } elseif ($curlResult['http_code'] === 503) {
                    throw new Exception("Groq API temporarily unavailable. Trying fallback...");
                } else {
                    throw new Exception("Groq API error: " . $errorMessage);
                }
            }

            // Extract response
            if (!isset($responseData['choices'][0]['message']['content'])) {
                throw new Exception("Invalid response format from Groq API");
            }

            $aiResponse = $responseData['choices'][0]['message']['content'];
            $tokensUsed = $this->estimateTokens($userMessage) + $this->estimateTokens($aiResponse);

            // Log successful response
            $this->logResponse($this->getServiceName(), [
                'status' => 'success',
                'response' => $aiResponse,
                'model' => $this->model
            ], $responseTime);

            // Return formatted response
            return $this->formatResponse(
                'success',
                $aiResponse,
                $this->getServiceName(),
                $this->model,
                $tokensUsed
            );

        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logError($this->getServiceName(), $errorMessage);

            return $this->formatResponse(
                'error',
                $errorMessage,
                $this->getServiceName()
            );
        }
    }

    /**
     * Check if Groq API is healthy
     *
     * @return bool True if service is accessible
     */
    public function isHealthy() {
        try {
            // Simple health check - verify API key is valid
            // Could make a minimal request, but for now just check key exists
            return !empty($this->apiKey);
        } catch (Exception $e) {
            return false;
        }
    }
}

?>
