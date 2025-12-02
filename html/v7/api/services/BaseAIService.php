<?php

/**
 * Base AI Service Abstract Class
 *
 * Defines the interface and common functionality for all AI service providers
 * Supports multiple backends (Groq, Hydra, etc.) with consistent interface
 */

abstract class BaseAIService {
    protected $apiKey;
    protected $apiUrl;
    protected $model;
    protected $maxTokens;
    protected $temperature;
    protected $logFile;
    protected $debugMode;

    /**
     * Constructor
     *
     * @param array $config Service configuration from .env
     */
    public function __construct($config = []) {
        $this->apiKey = $config['api_key'] ?? null;
        $this->apiUrl = $config['api_url'] ?? null;
        $this->model = $config['model'] ?? null;
        $this->maxTokens = $config['max_tokens'] ?? 500;
        $this->temperature = $config['temperature'] ?? 0.7;
        $this->logFile = $config['log_file'] ?? '/var/www/projects/f25-01/html/v3b/api/logs/chatbot.log';
        $this->debugMode = $config['debug_mode'] ?? false;
    }

    /**
     * Send message to AI service
     *
     * Abstract method to be implemented by subclasses
     *
     * @param string $userMessage User's question/message
     * @param string $systemPrompt System prompt with context
     * @param array $context Request context
     *
     * @return array Response with structure:
     *               ['status' => 'success'|'error',
     *                'response' => 'AI response text or error message',
     *                'service' => 'service name',
     *                'timestamp' => timestamp,
     *                'model' => 'model used',
     *                'tokens_used' => estimated tokens]
     */
    abstract public function sendMessage($userMessage, $systemPrompt, $context = []);

    /**
     * Check if service is healthy and accessible
     *
     * Abstract method to be implemented by subclasses
     *
     * @return bool True if service is healthy
     */
    abstract public function isHealthy();

    /**
     * Get service name
     *
     * @return string Service identifier
     */
    abstract public function getServiceName();

    /**
     * Log request to service
     *
     * @param string $service Service name
     * @param string $message User message
     * @param array $context Additional context
     */
    protected function logRequest($service, $message, $context = []) {
        if (!$this->debugMode) return;

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'service' => $service,
            'action' => 'request',
            'message_length' => strlen($message),
            'mechanism' => $context['mechanism'] ?? 'unknown',
            'mode' => $context['mode'] ?? 'unknown'
        ];

        $this->writeLog(json_encode($logEntry));
    }

    /**
     * Log response from service
     *
     * @param string $service Service name
     * @param array $response Response data
     * @param int $responseTime Time taken in milliseconds
     */
    protected function logResponse($service, $response, $responseTime = 0) {
        if (!$this->debugMode) return;

        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'service' => $service,
            'action' => 'response',
            'status' => $response['status'] ?? 'unknown',
            'response_length' => strlen($response['response'] ?? ''),
            'response_time_ms' => $responseTime,
            'model' => $response['model'] ?? 'unknown'
        ];

        $this->writeLog(json_encode($logEntry));
    }

    /**
     * Log error from service
     *
     * @param string $service Service name
     * @param string $error Error message
     * @param int $errorCode Error code (HTTP status, etc.)
     */
    protected function logError($service, $error, $errorCode = 0) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'service' => $service,
            'action' => 'error',
            'error' => $error,
            'error_code' => $errorCode
        ];

        $this->writeLog(json_encode($logEntry));
    }

    /**
     * Write log entry to file
     *
     * @param string $entry Log entry to write
     */
    protected function writeLog($entry) {
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $file = fopen($this->logFile, 'a');
        if ($file) {
            fwrite($file, $entry . PHP_EOL);
            fclose($file);
        }
    }

    /**
     * Format response in standard format
     *
     * @param string $status 'success' or 'error'
     * @param string $response Response text or error message
     * @param string $service Service name
     * @param string $model Model used
     * @param int $tokensUsed Estimated tokens used
     *
     * @return array Formatted response
     */
    protected function formatResponse($status, $response, $service, $model = '', $tokensUsed = 0) {
        return [
            'status' => $status,
            'response' => $response,
            'service' => $service,
            'timestamp' => time(),
            'model' => $model,
            'tokens_used' => $tokensUsed
        ];
    }

    /**
     * Get API key from config
     *
     * @return string API key
     * @throws Exception If API key not configured
     */
    protected function getApiKey() {
        if (!$this->apiKey) {
            throw new Exception("API key not configured for " . $this->getServiceName());
        }
        return $this->apiKey;
    }

    /**
     * Make curl request to API
     *
     * @param string $url API endpoint URL
     * @param array $headers HTTP headers
     * @param string $postData JSON post data
     * @param int $timeout Request timeout in seconds
     *
     * @return array Response with 'success' boolean and 'data' or 'error'
     */
    protected function makeCurlRequest($url, $headers, $postData, $timeout = 30) {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $startTime = microtime(true);
        $response = curl_exec($ch);
        $responseTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        return [
            'success' => ($httpCode === 200),
            'data' => $response,
            'http_code' => $httpCode,
            'error' => $error,
            'response_time_ms' => round($responseTime, 2)
        ];
    }

    /**
     * Estimate tokens in text (rough approximation)
     *
     * Most LLMs use approximately 1 token per 4 characters
     *
     * @param string $text Text to estimate tokens for
     *
     * @return int Estimated token count
     */
    protected function estimateTokens($text) {
        return ceil(strlen($text) / 4);
    }
}

?>
