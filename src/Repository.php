<?php

namespace Octotrend;

class Repository
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $stars,
        public readonly ?string $language,
        public readonly string $url,
    ) {}

    public static function fromApiData(array $data) : self
    {
        return new self(
            name: $data['full_name'],
            description: $data['description'] ?? null,
            stars: $data['stargazers_count'],
            language: $data['language'] ?? null,
            url: $data['html_url'],
        );
    }
}
