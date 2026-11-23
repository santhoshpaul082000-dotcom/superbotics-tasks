
<?php

class PointsCalculator
{
    private array $entries;

    public function __construct(array $entries)
    {
        $this->entries = $entries;
    }

    // Calculate current balance
    public function balance(): int
    {
        $balance = 0;

        foreach ($this->entries as $entry) {
            if ($entry['type'] === 'earn') {
                $balance += $entry['points'];
            } else {
                $balance -= $entry['points'];
            }
        }

        return $balance;
    }

    // Calculate expired and unused points
    public function expiredPoints(string $today): int
    {
        $earnings = [];

        // Collect earned points
        foreach ($this->entries as $entry) {
            if ($entry['type'] === 'earn') {
                $earnings[] = [
                    'points' => $entry['points'],
                    'date' => $entry['date']
                ];
            }
        }

        // Sort oldest earning first (FIFO)
        usort($earnings, fn($a, $b) =>
            strtotime($a['date']) <=> strtotime($b['date'])
        );

        // Deduct spent points from oldest earnings
        foreach ($this->entries as $entry) {
            if ($entry['type'] === 'spend') {
                $remaining = $entry['points'];

                foreach ($earnings as &$earning) {
                    if ($remaining <= 0) break;

                    $used = min($earning['points'], $remaining);

                    $earning['points'] -= $used;
                    $remaining -= $used;
                }

                unset($earning); // Stop referencing the last array item.
            }
        }

        // Calculate expired points
        $expired = 0;
        $expiryDate = new DateTime($today);
        $expiryDate->modify('-12 months');

        foreach ($earnings as $earning) {
            if (
                new DateTime($earning['date']) < $expiryDate
                && $earning['points'] > 0
            ) {
                $expired += $earning['points'];
            }
        }

        return $expired;
    }
}


// Sample entries
$entries = [
    ['type' => 'earn', 'points' => 100, 'date' => '2025-01-10'],
    ['type' => 'earn', 'points' => 200, 'date' => '2026-06-10'],
    ['type' => 'spend', 'points' => 50, 'date' => '2026-07-10'],
];

$calculator = new PointsCalculator($entries);

echo "Balance: " . $calculator->balance() . PHP_EOL;

echo "Expired Points: " .
     $calculator->expiredPoints('2026-11-23');
