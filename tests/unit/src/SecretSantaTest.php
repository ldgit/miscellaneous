<?php

namespace Events;

use Events\SecretSanta;
use PHPUnit\Framework\TestCase;

class SecretSantaTest extends TestCase
{
    const SEED = 1000;

    public function setUp()
    {
        $this->santa = new SecretSanta(self::SEED);
    }    

    /** 
     * @test
     * @dataProvider invalidParticipantsList
     */
    public function shouldThrowExceptionWhenGivenLessThanTwoParticipants($participants)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->santa->assign($participants);
    }

    public function invalidParticipantsList()
    {
        return [[[]], [['john']]];
    }

    /** @test */
    public function shouldThrowExceptionIfGivenDuplicateParticipants()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->santa->assign(['alice', 'bob', 'alice']);
    }

    /** 
     * @test
     * @dataProvider twoParticipantsList
     */
    public function shouldAssignParticipantsAsEachOthersSecretSantaIfGivenOnlyTwo(
        $participants, 
        $expected
    ) {
        $actual = $this->santa->assign($participants);
        array_multisort($actual);
        array_multisort($expected);
        $this->assertEquals($expected, $actual);
    }
    
    public function twoParticipantsList()
    {
        return [
            [['alice', 'bob'], [
                ['santa' => 'alice', 'recipient' => 'bob'],
                ['santa' => 'bob', 'recipient' => 'alice'],
            ]], 
            [['oscar', 'eve'], [
                ['santa' => 'oscar', 'recipient' => 'eve'],
                ['santa' => 'eve', 'recipient' => 'oscar'],
            ]],
        ];
    }

    /** 
     * @test
     * @dataProvider participantsWithDifferentKeys
     */
    public function differentParticipantKeysShouldNotAffectAssignment($participants)
    {
        $actual = $this->santa->assign($participants);
        array_multisort($actual);
        $this->assertEquals([
            ['santa' => 'alice', 'recipient' => 'carol'],
            ['santa' => 'bob', 'recipient' => 'alice'],
            ['santa' => 'carol', 'recipient' => 'bob'],
        ], $actual);
    }    

    public function participantsWithDifferentKeys()
    {
        return [
            [[5 => 'alice', 1 => 'bob', 2 => 'carol']],
            [['string' => 'alice', 'stuff' => 'bob', 'asKeys' => 'carol']],
        ];
    }

    /** 
     * @test
     * @dataProvider jsonParticipants
     */
    public function noOneIsAssignedAsTheirOwnSecretSanta($participantsJsonFile)
    {
        $this->santa = new SecretSanta();
        $participants = $this->getParticipantsFromJsonFile($participantsJsonFile);
        foreach ($this->santa->assign($participants) as $pair) {
            $this->assertNotEquals($pair['santa'], $pair['recipient']);
        } 
    }

    /** 
     * @test
     * @dataProvider jsonParticipants
     */
    public function noOneIsAssignedTwice($participantsJsonFile)
    {
        $participants = $this->getParticipantsFromJsonFile($participantsJsonFile);
        $santas = $recipients = [];

        foreach ($this->santa->assign($participants) as $pair) {
            $santas[] = $pair['santa'];
            $recipients[] = $pair['recipient'];
        }

        $this->assertNotEmpty($santas);
        $this->assertNotEmpty($recipients);
        $this->assertEquals(array_unique($santas), $santas);
        $this->assertEquals(array_unique($recipients), $recipients);
    }
    
    public function jsonParticipants()
    {
        return [
            [__DIR__.'/fixtures/small.json'],
            [__DIR__.'/fixtures/medium.json'],
            [__DIR__.'/fixtures/large.json'],
        ];
    }

    private function getParticipantsFromJsonFile(string $filePath): array
    {
        return json_decode(file_get_contents($filePath));
    }
}
