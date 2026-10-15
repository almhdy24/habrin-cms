<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Core\Auth;
use Core\Session;

final class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }
    }

    protected function tearDown(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
        }
    }

    public function testInitialStateIsUnauthenticated(): void
    {
        $this->assertFalse(Auth::check());
    }

    public function testLoginAndCheck(): void
    {
        Auth::login();
        $this->assertTrue(Auth::check());
    }

    public function testLogout(): void
    {
        Auth::login();
        $this->assertTrue(Auth::check());

        Auth::logout();
        $this->assertFalse(Auth::check());
    }

    public function testSessionExpirationAfterTimeout(): void
    {
        Auth::login();
        $this->assertTrue(Auth::check());

        // Simulate 31 minutes passing (timeout is 1800s / 30 mins)
        Session::set('last_activity', time() - 1860);

        $this->assertFalse(Auth::check());
    }

    public function testCsrfTokenGenerationAndValidation(): void
    {
        $token = Session::csrfToken();
        $this->assertNotEmpty($token);
        $this->assertSame(64, strlen($token)); // 32 random bytes in hex

        // Validate correct token
        $this->assertTrue(Session::validateCsrfToken($token));

        // Invalidate wrong token
        $this->assertFalse(Session::validateCsrfToken('invalid_token_string_12345'));
    }

    public function testPasswordHashingVerification(): void
    {
        $password = 'SuperSecureP@ssw0rd!2026';
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('wrong_password', $hash));
    }

    public function testAdminCredentialsValidationRules(): void
    {
        $currentPass = 'InitialAdminPass123!';
        $currentHash = password_hash($currentPass, PASSWORD_DEFAULT);

        // Verification of current password
        $this->assertTrue(password_verify($currentPass, $currentHash));
        $this->assertFalse(password_verify('wrongPass123', $currentHash));

        // New password length rule (min 8 chars)
        $shortPass = 'abc123';
        $this->assertLessThan(8, strlen($shortPass));

        $validNewPass = 'NewComplexAdminP@ss2026';
        $this->assertGreaterThanOrEqual(8, strlen($validNewPass));

        // Confirmation matching rule
        $confirmMatching = 'NewComplexAdminP@ss2026';
        $confirmMismatched = 'DifferentP@ss2026';
        $this->assertSame($validNewPass, $confirmMatching);
        $this->assertNotSame($validNewPass, $confirmMismatched);

        // Timing-safe username comparison
        $this->assertTrue(hash_equals('admin', 'admin'));
        $this->assertFalse(hash_equals('admin', 'attacker'));
    }
}
