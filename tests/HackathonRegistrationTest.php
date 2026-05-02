<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../HackathonRegistration.php';  // Исправлено название файла

class HackathonRegistrationTest extends TestCase  // Исправлено название класса
{
    private HackathonRegistration $registration;  // Исправлено
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->registration = new HackathonRegistration(null);  // Исправлено
    }

    public function testSuccessfulRegistration(): void
    {
        $data = [
            'name' => 'Иван Петров',
            'age' => 25,
            'direction' => 'backend',
            'hasExperience' => true,
            'role' => 'developer'
        ];
        
        $result = $this->registration->register($data);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('Участник Иван Петров успешно зарегистрирован', $result['message']);
        $this->assertArrayHasKey('id', $result);
    }
    
    public function testValidationNameError(): void
    {
        $data = [
            'name' => 'A',
            'age' => 25,
            'direction' => 'backend',
            'hasExperience' => true,
            'role' => 'developer'
        ];
        
        $result = $this->registration->register($data);
        
        $this->assertFalse($result['success']);
        $this->assertContains('Имя должно содержать минимум 2 символа', $result['errors']);
    }

    public function testValidationAgeError(): void
    {
        $data = [
            'name' => 'Иван Петров',
            'age' => 12, 
            'direction' => 'backend',
            'hasExperience' => true,
            'role' => 'developer'
        ];
        
        $result = $this->registration->register($data);
        
        $this->assertFalse($result['success']);
        $this->assertContains('Возраст должен быть от 14 до 100 лет', $result['errors']);
    }
    
    public function testValidationDirectionError(): void
    {
        $data = [
            'name' => 'Иван Петров',
            'age' => 25,
            'direction' => 'invalid_direction',
            'hasExperience' => true,
            'role' => 'developer'
        ];
        
        $result = $this->registration->register($data);
        
        $this->assertFalse($result['success']);
        $this->assertContains('Выберите корректное направление', $result['errors']);
    }
}