<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class EmailTemplateService
{
    /**
     * Clean up extra spans around Laravel variables
     */
    public function cleanupVariableSpans(string $content): string
    {
        // Remove multiple nested spans around Laravel variables
        $content = preg_replace('/<span[^>]*>\s*(<span[^>]*>)*\s*(\{\{[^}]+\}\})\s*(<\/span>)*\s*<\/span>/', '$2', $content);
        
        // Clean up any remaining nested spans around variables
        $content = preg_replace('/<span[^>]*>(\{\{[^}]+\}\})<\/span>/', '$1', $content);
        
        return $content;
    }

    /**
     * Check if content already has unsubscribe link
     */
    public function hasUnsubscribeLink(string $content): bool
    {
        return strpos($content, '$unsubscribeUrl') !== false || 
               strpos($content, 'unsubscribe') !== false;
    }

    /**
     * Generate email template
     */
    public function generateTemplate(string $content, string $title, string $type = 'newsletter', array $variables = [])
    {
        // Clean up content
        $cleanContent = $this->cleanupVariableSpans($content);
        
        // Check for existing unsubscribe
        $hasUnsubscribe = $this->hasUnsubscribeLink($cleanContent);
        
        // Handle complex templates (full HTML)
        if (strpos($cleanContent, '<!DOCTYPE') !== false || strpos($cleanContent, '<html') !== false) {
            return $cleanContent;
        }
        
        // Generate simple template - return the view with all variables
        return view('email-templates.wrapper', array_merge($variables, [
            'title' => $title,
            'emailContent' => $cleanContent,
            'hasUnsubscribe' => $hasUnsubscribe
        ]));
    }

    /**
     * Extract content from existing email file
     */
    public function extractEmailContent(string $filename, string $type = 'newsletter'): string
    {
        $filePath = resource_path("views/emails/{$type}s/{$filename}");
        
        if (!file_exists($filePath)) {
            return '';
        }
        
        $content = file_get_contents($filePath);
        
        // Remove wrapper HTML to get just the content
        $content = preg_replace('/.*<div style="max-width: 600px[^>]*>/s', '', $content);
        $content = preg_replace('/<hr style="margin: 30px 0.*$/s', '', $content);
        
        // Decode HTML entities that may have been double-encoded
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8'); // Decode twice for double encoding
        
        // Also decode specific entities
        $content = str_replace(['&lt;', '&gt;', '&amp;'], ['<', '>', '&'], $content);
        
        return trim($content);
    }

    /**
     * Save email template to file with error handling
     */
    public function saveTemplate(string $filename, string $template, string $type = 'newsletter'): bool
    {
        try {
            $filePath = resource_path("views/emails/{$type}s/{$filename}");
            
            // Ensure directory exists
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                    throw new \RuntimeException("Directory '{$directory}' was not created");
                }
            }
            
            $result = file_put_contents($filePath, $template);
            
            // Clear template cache
            Cache::forget("email_template_" . md5($template));
            
            return $result !== false;
            
        } catch (\Exception $e) {
            \Log::error("Failed to save email template: " . $e->getMessage());
            return false;
        }
    }
}