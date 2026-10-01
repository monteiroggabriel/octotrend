# Octotrend

A command-line interface (CLI) tool that interacts with the GitHub API and shows the trending repositories.

**Note:** GitHub does not expose an official "trending" endpoint. Here, we use the Search API (`/search/repositories`), filtering by creation date and sorting by stars. The tool provides clear error messages for network failures and API rate limits.

**Installation and Example Usage:**  
```bash
git clone https://github.com/monteiroggabriel/octotrend.git
cd octotrend
composer install

php bin/octotrend.php --duration month --limit 20
```

| Option | Description | Default |
| - | - | - |
| `--duration` | Period: `day`, `week`, `month` or `year` | `week` |
| `--limit`    | Number of repositories displayed | `10` |
| `--language` | Filter by language (ex: `PHP`) | - |
