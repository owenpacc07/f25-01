<?php

/**
 * Hydra AI Service Implementation (Fallback)
 *
 * Integrates with SUNY New Paltz's Hydra server (running Gemma3)
 * Uses as automatic fallback if primary service fails
 *
 * Hydra API: gpt.hydra.newpaltz.edu
 * Model: gemma3:12b
 *
 * NOTE: Hydra can be unreliable (CPU-bound), which is why Groq is primary
 */

class HydraService extends BaseAIService {

    /**
     * Constructor
     *
     * @param array $env Environment variables from .env
     */
    public function __construct($env = []) {
        $hydraUrl = $env['HYDRA_API_URL'] ?? 'gpt.hydra.newpaltz.edu';

        // Ensure proper URL formatting
        if (strpos($hydraUrl, 'http') === false) {
            $hydraUrl = 'https://' . $hydraUrl;
        }

        $config = [
            'api_key' => $env['HYDRA_API_KEY'] ?? null,
            'api_url' => $hydraUrl . '/v1/chat/completions',
            'model' => $env['HYDRA_MODEL'] ?? 'gemma3:12b',
            'max_tokens' => $env['HYDRA_MAX_TOKENS'] ?? 500,
            'temperature' => $env['HYDRA_TEMPERATURE'] ?? 0.7,
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
        return 'hydra';
    }

    /**
     * Send message to Hydra API
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

            // Construct request - Hydra uses OpenAI-compatible format
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

            // Prepare headers - Hydra expects Bearer token auth
            $headers = [
                'Authorization: Bearer ' . $this->getApiKey(),
                'Content-Type: application/json'
            ];

            // Make request with longer timeout (Hydra can be slow)
            $startTime = microtime(true);
            $curlResult = $this->makeCurlRequest(
                $this->apiUrl,
                $headers,
                json_encode($requestPayload),
                60 // 60 second timeout for Hydra (slower due to CPU)
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
                $errorMessage = $responseData['error']['message'] ?? 'Unknown Hydra error';
                $this->logError($this->getServiceName(), $errorMessage, $curlResult['http_code']);

                // Provide user-friendly error messages
                if ($curlResult['http_code'] === 401) {
                    throw new Exception("Hydra API authentication failed. Check API key.");
                } elseif ($curlResult['http_code'] === 503) {
                    throw new Exception("Hydra server unavailable (likely on CPU, slow, or down).");
                } else {
                    throw new Exception("Hydra API error: " . $errorMessage);
                }
            }

            // Extract response - same format as Groq
            if (!isset($responseData['choices'][0]['message']['content'])) {
                throw new Exception("Invalid response format from Hydra API");
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
     * Check if Hydra API is healthy
     *
     * @return bool True if service appears accessible
     */
    public function isHealthy() {
        try {
            // Simple health check - verify API key is configured
            // Full health check would require API call which might timeout
            return !empty($this->apiKey);
        } catch (Exception $e) {
            return false;
        }
    }
}

?>
