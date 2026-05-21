#!/usr/bin/env php
<?php
// Fails if any locked dependency (composer.lock or package-lock.json)
// resolves to a version published within the last $thresholdDays days.
// Mirrors the intent of npm's `min-release-age` for Composer, which has
// no native equivalent yet.

declare(strict_types=1);

$thresholdDays = 7;
$repoRoot = dirname(__DIR__);
$now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
$cutoff = $now->sub(new DateInterval("P{$thresholdDays}D"));

$violations = [];
$unverified = [];

$violations = array_merge($violations, checkComposer($repoRoot . '/composer.lock', $cutoff, $unverified));
$violations = array_merge($violations, checkNpm($repoRoot . '/package-lock.json', $cutoff, $unverified));

if ($violations !== [] || $unverified !== []) {
    if ($unverified !== []) {
        fwrite(STDERR, "Could not verify dependency publish dates:\n");
        foreach ($unverified as $v) {
            fwrite(STDERR, "  - {$v}\n");
        }
    }
    if ($violations !== []) {
        fwrite(STDERR, "Found dependencies younger than {$thresholdDays} days:\n");
        foreach ($violations as $v) {
            fwrite(STDERR, "  - {$v}\n");
        }
    }
    exit(1);
}

echo "All locked dependencies are at least {$thresholdDays} days old.\n";
exit(0);

function checkComposer(string $lockPath, DateTimeImmutable $cutoff, array &$unverified): array
{
    if (!is_file($lockPath)) {
        return [];
    }
    $data = json_decode((string) file_get_contents($lockPath), true);
    if (!is_array($data)) {
        $unverified[] = "could not parse {$lockPath}";
        return [];
    }
    $violations = [];
    foreach (['packages', 'packages-dev'] as $section) {
        foreach (($data[$section] ?? []) as $pkg) {
            $name = $pkg['name'] ?? '?';
            $version = $pkg['version'] ?? '?';
            $time = $pkg['time'] ?? null;
            if ($time === null) {
                $unverified[] = "composer: {$name}@{$version} has no time field";
                continue;
            }
            try {
                $published = new DateTimeImmutable($time);
            } catch (Exception $e) {
                $unverified[] = "composer: {$name}@{$version} has unparseable time '{$time}'";
                continue;
            }
            if ($published > $cutoff) {
                $violations[] = sprintf(
                    'composer: %s@%s published %s',
                    $name,
                    $version,
                    $published->format(DateTimeInterface::ATOM)
                );
            }
        }
    }
    return $violations;
}

function checkNpm(string $lockPath, DateTimeImmutable $cutoff, array &$unverified): array
{
    if (!is_file($lockPath)) {
        return [];
    }
    $data = json_decode((string) file_get_contents($lockPath), true);
    if (!is_array($data)) {
        $unverified[] = "could not parse {$lockPath}";
        return [];
    }
    $packages = $data['packages'] ?? [];
    if (!is_array($packages)) {
        $unverified[] = "npm: {$lockPath} missing packages map";
        return [];
    }

    $timeCache = [];
    $violations = [];

    foreach ($packages as $key => $entry) {
        if ($key === '') {
            continue; // root project entry
        }
        if (!is_array($entry) || !isset($entry['version'])) {
            continue;
        }
        if (!empty($entry['link'])) {
            continue; // workspace symlink
        }

        $name = npmNameFromKey($key);
        $version = (string) $entry['version'];
        $resolved = $entry['resolved'] ?? '';

        // Only check things published to the npm registry. Git/file/http
        // tarballs don't have a registry publish date.
        if ($resolved !== '' && strpos($resolved, 'https://registry.npmjs.org/') !== 0) {
            continue;
        }

        if (!array_key_exists($name, $timeCache)) {
            $timeCache[$name] = fetchNpmTimes($name, $unverified);
        }
        if ($timeCache[$name] === null) {
            continue;
        }
        $time = $timeCache[$name][$version] ?? null;
        if ($time === null) {
            $unverified[] = "npm: no publish date for {$name}@{$version}";
            continue;
        }
        try {
            $published = new DateTimeImmutable($time);
        } catch (Exception $e) {
            $unverified[] = "npm: {$name}@{$version} has unparseable time '{$time}'";
            continue;
        }
        if ($published > $cutoff) {
            $violations[] = sprintf(
                'npm: %s@%s published %s',
                $name,
                $version,
                $published->format(DateTimeInterface::ATOM)
            );
        }
    }
    return $violations;
}

function npmNameFromKey(string $key): string
{
    // Lockfile keys look like "node_modules/foo" or
    // "node_modules/foo/node_modules/@scope/bar". The package name is
    // whatever follows the final "node_modules/".
    $marker = 'node_modules/';
    $pos = strrpos($key, $marker);
    if ($pos === false) {
        return $key;
    }
    return substr($key, $pos + strlen($marker));
}

function fetchNpmTimes(string $name, array &$unverified): ?array
{
    // Scoped names ("@scope/pkg") need the slash percent-encoded for the
    // registry packument URL.
    $encoded = str_replace('/', '%2F', $name);
    $url = 'https://registry.npmjs.org/' . $encoded;
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: proteuswidgets-dep-age-check\r\nAccept: application/json\r\n",
            'timeout' => 20,
            'ignore_errors' => true,
        ],
    ]);
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        $unverified[] = "npm: could not fetch metadata for {$name}";
        return null;
    }
    $meta = json_decode($raw, true);
    if (!is_array($meta) || !isset($meta['time']) || !is_array($meta['time'])) {
        $unverified[] = "npm: metadata for {$name} missing time map";
        return null;
    }
    return $meta['time'];
}
