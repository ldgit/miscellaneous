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
    public function shouldThrowErrorWhenGivenLessThanTwoParticipants($participants)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->santa->assign($participants);
    }

    public function invalidParticipantsList()
    {
        return [[[]], [['john']]];
    }

    /** 
     * @test
     * @dataProvider twoParticipantsList
     */
    public function givenTwoParticipantsAssignThemAsEachOthersSecretSanta($participants, $expected)
    {
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

    /** @test */
    public function shouldThrowExceptionIfGivenDuplicateParticipants()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->santa->assign(['alice', 'bob', 'alice']);
    }
    
    /** @test */
    public function givenThreeParticipantsShuffleThemAndAssignEachToTheNextOneInArray()
    {
        $participants = ['alice', 'bob', 'carol'];

        $actual = $this->santa->assign($participants);
        array_multisort($actual);

        // $participants shuffle results in ['alice', 'carol', 'bob'] with given test SEED
        $this->assertEquals([
            ['santa' => 'alice', 'recipient' => 'carol'],
            ['santa' => 'bob', 'recipient' => 'alice'],
            ['santa' => 'carol', 'recipient' => 'bob'],
        ], $actual);
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
    public function noOneGetsPickedAsTheirOwnSecretSanta($participantsJsonFile)
    {
        $this->santa = new SecretSanta();
        $participants = $this->getParticipantsFromJsonFile($participantsJsonFile);
        $actual = $this->santa->assign($participants);
        foreach ($this->santa->assign($participants) as $pair) {
             $this->assertNotEquals($pair['santa'], $pair['recipient']);
        } 
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
