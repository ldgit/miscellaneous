<?php 

namespace Events;

/**
 * Very naive Secret Santa assignment algorithm.
 */
class SecretSanta
{
    private $seed;

    public function __construct(int $seed = 0)
    {
        $this->seed = $seed;
    }

    public function assign(array $participants)
    {
        $this->validateParticipants($participants);
        $this->setSeed();

        $assignments = [];
        $participantCount = count($participants);

        shuffle($participants);

        foreach ($participants as $index => $participant) {
            $assignments[] = [
                'santa' => $participant, 
                'recipient' => $this->isFinalParticipant($index, $participantCount) 
                    ? $this->getFirstParticipant($participants)
                    : $this->getNextParticipant($participants, $index),
            ];
        }

        return $assignments;
    }

    private function validateParticipants(array $participants)
    {
        if (count($participants) < 2) {
            throw new \InvalidArgumentException('Must give at least 2 participants');
        }

        if ($participants !== array_unique($participants)) {
            throw new \InvalidArgumentException('All participants must be unique');         
        }
    }

    private function setSeed()
    {
        srand($this->seed !== 0 ? $this->seed : microtime(true));
    }

    private function isFinalParticipant(int $index, int $participantCount): bool
    {
        return $index + 1 === $participantCount;
    }

    private function getFirstParticipant(array $participants)
    {
        return $participants[0];
    }

    private function getNextParticipant(array $participants, int $index)
    {
        return $participants[$index + 1];
    }
}
