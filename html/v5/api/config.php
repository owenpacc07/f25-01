<?php

/**
 * Service Configuration
 *
 * Centralized configuration for AI service providers
 * Implements service factory pattern for easy switching between providers
 */

// Load environment variables
$env = parse_ini_file("/var/www/projects/f25-01/.env");

/**
 * Service Factory - Gets appropriate service instance based on configuration
 *
 * @param string $serviceName Service to instantiate (groq, hydra, etc.)
 * @param array $env Environment variables
 *
 * @return BaseAIService Service instance
 * @throws Exception If service not found or configuration missing
 */
function getAIService($serviceName = null, $env = []) {
    // Use primary service if not specified
    if (null === $serviceName) {
        $serviceName = $env['AI_SERVICE_PRIMARY'] ?? 'groq';
    }

    // Instantiate requested service
    switch (strtolower($serviceName)) {
        case 'groq':
            if (empty($env['GROQ_API_KEY'])) {
                throw new Exception("Groq API key not configured in .env");
            }
            return new GroqService($env);

        case 'hydra':
            if (empty($env['HYDRA_API_KEY'])) {
                throw new Exception("Hydra API key not configured in .env");
            }
            return new HydraService($env);

        default:
            throw new Exception("Unknown AI service: {$serviceName}");
    }
}

/**
 * Get fallback service if primary fails
 *
 * @param array $env Environment variables
 *
 * @return BaseAIService|null Fallback service instance or null
 */
function getFallbackService($env = []) {
    $fallbackName = $env['AI_SERVICE_FALLBACK'] ?? 'hydra';
    $primaryName = $env['AI_SERVICE_PRIMARY'] ?? 'groq';

    // Don't use same service as fallback
    if ($fallbackName === $primaryName) {
        return null;
    }

    try {
        return getAIService($fallbackName, $env);
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Service constants
 */
define('SERVICE_GROQ', 'groq');
define('SERVICE_HYDRA', 'hydra');
define('SERVICE_ERROR', 'error');

/**
 * Configuration validation
 */
function validateServiceConfig($env = []) {
    $errors = [];

    // Check primary service
    $primaryService = $env['AI_SERVICE_PRIMARY'] ?? 'groq';
    if ($primaryService === 'groq' && empty($env['GROQ_API_KEY'])) {
        $errors[] = "GROQ_API_KEY not configured";
    }
    if ($primaryService === 'hydra' && empty($env['HYDRA_API_KEY'])) {
        $errors[] = "HYDRA_API_KEY not configured";
    }

    // Check fallback service
    $fallbackService = $env['AI_SERVICE_FALLBACK'] ?? 'hydra';
    if ($fallbackService !== $primaryService) {
        if ($fallbackService === 'groq' && empty($env['GROQ_API_KEY'])) {
            $errors[] = "Groq fallback configured but GROQ_API_KEY not set";
        }
        if ($fallbackService === 'hydra' && empty($env['HYDRA_API_KEY'])) {
            $errors[] = "Hydra fallback configured but HYDRA_API_KEY not set";
        }
    }

    return $errors;
}

?>
