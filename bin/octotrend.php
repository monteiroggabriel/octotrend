<?php

require __DIR__ . '/../vendor/autoload.php';

use Octotrend\GithubClient;

try {
    $options = getopt('', ['duration:', 'limit:', 'language:']);

    $duration = $options['duration'] ?? 'week';
    $limit    = isset($options['limit']) ? (int) $options['limit'] : 10;
    $language = $options['language'] ?? null;

    $validDurations = ['day', 'week', 'month', 'year'];

    if (!in_array($duration, $validDurations, true)) {
        throw new \InvalidArgumentException(
            "Invalid duration: '{$duration}'. Use: " . implode(', ', $validDurations)
        );
    }

    if ($limit < 1) {
        throw new \InvalidArgumentException('The limit must be greater than zero.');
    }

    $client = new GithubClient();
    $repos = $client->fetchTrending(
        duration: $duration,
        language: $language,
        limit: $limit,
    );

    if (empty($repos)) {
        echo "No repository found for this filter.\n";
        exit(0);
    }

    foreach ($repos as $repo) {
        echo "| {$repo->name} | ✫ {$repo->stars} | {$repo->language} |\n";
    }
} catch (\InvalidArgumentException $e) {
    fwrite(STDERR, "Error: {$e->getMessage()}\n");
    exit(1);
} catch (\RuntimeException $e) {
    fwrite(STDERR, "Error: {$e->getMessage()}\n");
    exit(1);
}
