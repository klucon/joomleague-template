<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$manifest = (string) file_get_contents($root . '/templateDetails.xml');
$index = (string) file_get_contents($root . '/index.php');

foreach (['css', 'forms', 'html', 'images', 'js', 'language'] as $directory) {
	if (!is_dir($root . '/' . $directory)) {
		throw new RuntimeException(sprintf('Template directory %s is missing.', $directory));
	}
}

foreach (['<name>joomleague</name>', '<version>0.1.4-dev</version>', 'forms/social.xml'] as $contract) {
	if (!str_contains($manifest, $contract)) {
		throw new RuntimeException(sprintf('Manifest contract %s is missing.', $contract));
	}
}

foreach ([
	'$isPositionPreview = $input->getBool(\'tp\')',
	'ComponentHelper::getParams(\'com_templates\')->get(\'template_positions_display\')',
] as $previewContract) {
	if (!str_contains($index, $previewContract)) {
		throw new RuntimeException(sprintf('Position preview contract %s is missing.', $previewContract));
	}
}

foreach ([
	'use Joomla\\CMS\\Uri\\Uri;',
	'$requestPath = trim((string) $requestUri->getPath(), \'/\');',
	'$hasExplicitComponentRoute = isset($rawQuery[\'option\'])',
	'$isHome = $isPositionPreview || ($isHomeRequest',
] as $homeRouteContract) {
	if (!str_contains($index, $homeRouteContract)) {
		throw new RuntimeException(sprintf('Homepage route contract %s is missing.', $homeRouteContract));
	}
}

if (str_contains($index, '$isHome = $isPositionPreview || ((int) ($menu?->id ?? 0) === $homeItem')) {
	throw new RuntimeException('Homepage detection still relies only on the active menu item.');
}

foreach (['en-GB', 'cs-CZ'] as $languageTag) {
	foreach (['ini', 'sys.ini'] as $suffix) {
		$relativePath = $languageTag . '/tpl_joomleague.' . $suffix;

		if (!str_contains($manifest, '>' . $relativePath . '</language>')) {
			throw new RuntimeException(sprintf('Manifest language path %s is missing.', $relativePath));
		}
	}
}

foreach (['brand', 'colours', 'hero', 'layout', 'navigation', 'social', 'footer'] as $fieldset) {
	if (!str_contains($manifest, 'fieldset name="' . $fieldset . '"')) {
		throw new RuntimeException(sprintf('Configuration fieldset %s is missing.', $fieldset));
	}
}

preg_match_all('/<position>([a-z0-9-]+)<\/position>/', $manifest, $positionMatches);
$positions = $positionMatches[1] ?? [];

if (count($positions) !== 19 || count($positions) !== count(array_unique($positions))) {
	throw new RuntimeException('Template must declare exactly 19 unique module positions.');
}

foreach ($positions as $position) {
	$isDirectlyRendered = str_contains($index, 'name="' . $position . '"');
	$isFooterPosition = in_array($position, ['footer-a', 'footer-b', 'footer-c'], true)
		&& str_contains($index, "['footer-a', 'footer-b', 'footer-c']")
		&& str_contains($index, 'name="<?php echo $position; ?>"');

	if (!$isDirectlyRendered && !$isFooterPosition) {
		throw new RuntimeException(sprintf('Position %s is declared but not rendered.', $position));
	}
}

$previewStart = strpos($index, '<?php if ($isPositionPreview) : ?>');
$previewEnd = strpos($index, '<?php else : ?>', $previewStart === false ? 0 : $previewStart);

if ($previewStart === false || $previewEnd === false) {
	throw new RuntimeException('Dedicated position preview branch is missing.');
}

$previewMarkup = substr($index, $previewStart, $previewEnd - $previewStart);

foreach ($positions as $position) {
	if (!str_contains($previewMarkup, 'name="' . $position . '"')) {
		throw new RuntimeException(sprintf('Position %s is missing from the clean preview.', $position));
	}
}

foreach (['TPL_JOOMLEAGUE_NEWS_TITLE', 'TPL_JOOMLEAGUE_COMPETITION_TITLE', 'type="component"'] as $forbiddenPreviewContent) {
	if (str_contains($previewMarkup, $forbiddenPreviewContent)) {
		throw new RuntimeException(sprintf('Presentation content %s leaked into position preview.', $forbiddenPreviewContent));
	}
}

foreach ([
	'TPL_JOOMLEAGUE_NEWS_KICKER',
	'TPL_JOOMLEAGUE_NEWS_TITLE',
	'TPL_JOOMLEAGUE_COMPETITION_KICKER',
	'TPL_JOOMLEAGUE_TABLE_TITLE',
	'TPL_JOOMLEAGUE_CALENDAR_KICKER',
	'TPL_JOOMLEAGUE_CALENDAR_TITLE',
	'TPL_JOOMLEAGUE_PERFORMANCE_KICKER',
	'TPL_JOOMLEAGUE_PERFORMANCE_TITLE',
	'TPL_JOOMLEAGUE_CLUB_KICKER',
	'TPL_JOOMLEAGUE_CLUB_TITLE',
	'TPL_JOOMLEAGUE_DISCOVER_KICKER',
	'TPL_JOOMLEAGUE_DISCOVER_TITLE',
] as $moduleOwnedHeading) {
	if (str_contains($index, $moduleOwnedHeading)) {
		throw new RuntimeException(sprintf('Module-owned heading %s remains hardcoded in the template.', $moduleOwnedHeading));
	}
}

foreach (['123', '170', '171'] as $demoItemId) {
	if (str_contains($index, 'Itemid=' . $demoItemId) || str_contains($manifest, 'default="' . $demoItemId . '"')) {
		throw new RuntimeException(sprintf('Demo menu ID %s remains hardcoded.', $demoItemId));
	}
}

$usedKeys = [];
foreach ([$manifest, $index, (string) file_get_contents($root . '/forms/social.xml')] as $source) {
	preg_match_all('/TPL_JOOMLEAGUE_[A-Z0-9_]+/', $source, $matches);
	$usedKeys = array_merge($usedKeys, $matches[0]);
}
$usedKeys = array_values(array_unique($usedKeys));

foreach (['en-GB', 'cs-CZ'] as $languageTag) {
	foreach (['ini', 'sys.ini'] as $suffix) {
		$file = $root . '/language/' . $languageTag . '/tpl_joomleague.' . $suffix;
		$definitions = parse_ini_file($file);
		if (!is_array($definitions)) {
			throw new RuntimeException(sprintf('Language file %s is invalid.', $file));
		}
		foreach ($usedKeys as $key) {
			if (!array_key_exists($key, $definitions)) {
				throw new RuntimeException(sprintf('%s is missing %s.', $file, $key));
			}
		}
	}

	$systemDefinitions = parse_ini_file($root . '/language/' . $languageTag . '/tpl_joomleague.sys.ini');

	foreach (['JOOMLEAGUE', 'TPL_JOOMLEAGUE_XML_DESCRIPTION'] as $systemKey) {
		if (!is_array($systemDefinitions) || !isset($systemDefinitions[$systemKey])) {
			throw new RuntimeException(sprintf('%s system language key is missing for %s.', $systemKey, $languageTag));
		}
	}
}

foreach (['JLDemo', 'JLDEMO', 'tpl_jldemo_football', 'tpl_tpl_joomleague'] as $legacyReference) {
	if (str_contains($manifest . $index, $legacyReference)) {
		throw new RuntimeException(sprintf('Legacy reference %s remains.', $legacyReference));
	}
}

echo "JoomLeague template contract OK.\n";
