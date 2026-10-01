<?php

namespace Octotrend;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;

class GithubClient
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => 'https://api.github.com',
            'timeout' => 10,
        ]);
    }

    /**
     * @return Repository[]
     * @throws \RuntimeException
     */
    public function fetchTrending(string $duration, ?string $language, int $limit): array
    {
        $since = $this->dateFor($duration);

        $query = "created:>{$since}";
        if ($language !== null) {
            $query .= " language:{$language}";
        }

        try {
            $response = $this->http->get('/search/repositories', [
                'query' => [
                    'q' => $query,
                    'sort' => 'stars',
                    'order' => 'desc',
                    'per_page' => $limit,
                ],
            ]);
        } catch (ConnectException $e) {
            throw new \RuntimeException(
                'Unable to connect to GitHub. Check your internet connection.',
                previous: $e
            );
        } catch (ClientException $e) {
            $status = $e->getResponse()->getStatusCode();

            if ($status === 403) {
                throw new \RuntimeException(
                    'GitHub API request limit reached. Try again in a few minutes.',
                    previous: $e
                );
            }

            throw new \RuntimeException(
                "Error querying the GitHub API (HTTP {$status}).",
                previous: $e
            );
        } catch (GuzzleException $e) {
            throw new \RuntimeException('Unexpected error while querying the GitHub API.', previous: $e);
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (!isset($data['items'])) {
            throw new \RuntimeException('Unexpected response from the GitHub API.');
        }

        return array_map(
            fn (array $repo) => Repository::fromApiData($repo),
            $data['items']
        );
    }

    private function dateFor(string $duration): string
    {
        $modifier = match ($duration) {
            'day' => '-1 day',
            'week' => '-1 week',
            'month' => '-1 month',
            'year' => '-1 year',
            default => throw new \InvalidArgumentException("Invalid duration: {$duration}"),
        };

        return (new \DateTime())->modify($modifier)->format('Y-m-d');
    }
}
