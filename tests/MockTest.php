<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../HackathonRegistration.php';  // Исправлено

class MockTest extends TestCase
{
    private $pdoMock;
    private $statementMock;
    private HackathonRegistration $registration;  // Исправлено
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->pdoMock = $this->createMock(PDO::class);
        $this->statementMock = $this->createMock(PDOStatement::class);
        
        $this->registration = new HackathonRegistration($this->pdoMock);  // Исправлено
    }
    
    public function testRegistrationWithMockDatabase(): void
    {
        $data = [
            'name' => 'Тестовый Участник',
            'age' => 30,
            'direction' => 'ai',
            'hasExperience' => true,
            'role' => 'team_lead'
        ];
        
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->statementMock);
        
        $this->statementMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);
        
        $this->pdoMock->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('999');
        
        $result = $this->registration->register($data);
        
        $this->assertTrue($result['success']);
        $this->assertEquals(999, $result['id']);
    }

    public function testRegistrationDatabaseError(): void
    {
        $data = [
            'name' => 'Тестовый Участник',
            'age' => 30,
            'direction' => 'ai',
            'hasExperience' => true,
            'role' => 'team_lead'
        ];
        
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->will($this->throwException(new PDOException('Database error')));
        
        $result = $this->registration->register($data);
        
        $this->assertFalse($result['success']);
        $this->assertContains('Database error', $result['errors']);
    }
    
    public function testGetAllParticipantsWithMock(): void
    {
        $expectedParticipants = [
            ['id' => 1, 'name' => 'Участник 1', 'age' => 20, 'direction' => 'backend', 'has_experience' => 1, 'team_role' => 'developer', 'created_at' => '2024-01-01 10:00:00'],
            ['id' => 2, 'name' => 'Участник 2', 'age' => 25, 'direction' => 'frontend', 'has_experience' => 0, 'team_role' => 'designer', 'created_at' => '2024-01-02 10:00:00']
        ];
        
        $this->pdoMock->expects($this->once())
            ->method('query')
            ->willReturn($this->statementMock);
        
        $this->statementMock->expects($this->once())
            ->method('fetchAll')
            ->willReturn($expectedParticipants);
        
        $result = $this->registration->getAllParticipants();
        
        $this->assertCount(2, $result);
        $this->assertEquals('Участник 1', $result[0]['name']);
    }
    
    public function testGetParticipantByIdWithMock(): void
    {
        $expectedParticipant = [
            'id' => 5,
            'name' => 'Конкретный Участник',
            'age' => 28,
            'direction' => 'mobile',
            'has_experience' => 1,
            'team_role' => 'developer',
            'created_at' => '2024-01-01 10:00:00'
        ];
        
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->statementMock);
        
        $this->statementMock->expects($this->once())
            ->method('execute')
            ->with([':id' => 5]);
        
        $this->statementMock->expects($this->once())
            ->method('fetch')
            ->willReturn($expectedParticipant);
        
        $result = $this->registration->getParticipantById(5);
        
        $this->assertNotNull($result);
        $this->assertEquals(5, $result['id']);
        $this->assertEquals('Конкретный Участник', $result['name']);
    }
}