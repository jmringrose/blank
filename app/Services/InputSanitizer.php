<?php

namespace App\Services;

class InputSanitizer
{
    /**
     * Sanitize email content for safe storage and display
     */
    public function sanitizeEmailContent(string $content): string
    {
        if (!config('emails.security.sanitize_input', true)) {
            return $content;
        }

        // Allow specific HTML tags for email content
        $allowedTags = '<p><br><strong><b><em><i><u><a><img><h1><h2><h3><h4><h5><h6><ul><ol><li><div><span><table><tr><td><th><thead><tbody><hr>';
        
        // Strip dangerous tags but keep email formatting
        $content = strip_tags($content, $allowedTags);
        
        // Remove potentially dangerous attributes but keep safe ones
        $content = preg_replace('/(<[^>]+)\s+(on\w+|javascript:|vbscript:|data:)[^>]*>/i', '$1>', $content);
        
        // Clean up excessive whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        
        return trim($content);
    }

    /**
     * Sanitize user input data
     */
    public function sanitizeUserData(array $data): array
    {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = $this->sanitizeString($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeUserData($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }

    /**
     * Sanitize individual string values
     */
    private function sanitizeString(string $value): string
    {
        // Remove null bytes
        $value = str_replace("\0", '', $value);
        
        // Trim whitespace
        $value = trim($value);
        
        // Convert special characters to HTML entities for display
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate and sanitize unsubscribe token
     */
    public function sanitizeToken(string $token): ?string
    {
        // Remove any non-alphanumeric characters
        $token = preg_replace('/[^a-zA-Z0-9]/', '', $token);
        
        // Check length
        $expectedLength = config('emails.tokens.length', 32);
        if (strlen($token) !== $expectedLength) {
            return null;
        }
        
        return $token;
    }
}