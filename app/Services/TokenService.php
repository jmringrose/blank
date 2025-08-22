<?php

namespace App\Services;

use App\Models\NewsletterSequence;
use Illuminate\Support\Str;

class TokenService
{
    /**
     * Generate a unique unsubscribe token
     */
    public function generateToken(): string
    {
        $length = config('emails.tokens.length', 32);
        $characters = config('emails.tokens.characters', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');
        
        do {
            $token = '';
            for ($i = 0; $i < $length; $i++) {
                $token .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (NewsletterSequence::where('unsub_token', $token)->exists());
        
        return $token;
    }

    /**
     * Generate tokens for multiple sequences
     */
    public function bulkGenerateTokens(array $sequenceIds): array
    {
        $results = [];
        
        foreach ($sequenceIds as $id) {
            try {
                $sequence = NewsletterSequence::findOrFail($id);
                
                if (empty($sequence->unsub_token)) {
                    $token = $this->generateToken();
                    $sequence->unsub_token = $token;
                    $sequence->save();
                    
                    $results[$id] = [
                        'success' => true,
                        'token' => $token,
                        'message' => 'Token generated successfully'
                    ];
                } else {
                    $results[$id] = [
                        'success' => false,
                        'token' => $sequence->unsub_token,
                        'message' => 'Token already exists'
                    ];
                }
            } catch (\Exception $e) {
                $results[$id] = [
                    'success' => false,
                    'token' => null,
                    'message' => 'Failed to generate token: ' . $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

    /**
     * Validate token format
     */
    public function validateToken(string $token): bool
    {
        $length = config('emails.tokens.length', 32);
        $characters = config('emails.tokens.characters');
        
        if (strlen($token) !== $length) {
            return false;
        }
        
        return preg_match('/^[' . preg_quote($characters, '/') . ']+$/', $token);
    }

    /**
     * Check if token is expired (if expiry is configured)
     */
    public function isTokenExpired(NewsletterSequence $sequence): bool
    {
        $expiryDays = config('emails.tokens.expiry_days');
        
        if ($expiryDays === null) {
            return false; // Never expires
        }
        
        if (!$sequence->updated_at) {
            return true;
        }
        
        return $sequence->updated_at->addDays($expiryDays)->isPast();
    }
}